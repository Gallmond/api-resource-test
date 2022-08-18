<?php

namespace App\Repositories;

use App\Models\Interfaces\HasAutoCRUDMethods;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This class should be implemented per Model.
 * 
 * Set the $model property to the full className of the model.
 * Set the $modelRelations to a name => className key pair array.
 * 
 */
abstract class AutoCrudRepo {

  /**
   * The model this repository provides CRUD operations for.
   */
  protected string $model;
  protected Model $instance;

  /**
   * The defined relations this model has. like:
   * [
   *   'analytics' => PostAnalytics::class,
   *   'author' => User::class,
   * ]
   * 
   * @var array<string, string>
   */
  protected array $modelRelations = [];

  /** the current request */
  protected Request $request;

  public function __construct()
  {
    $this->request = app()->make(Request::class);
    $this->addUserId();
  }

  public function create(): Model
  {
    $this->createPrimaryModel();
    $this->createRelatedModels();
    $this->loadRelatedModels();
    
    return $this->instance;
  }

  public function read(mixed $id): Model
  {
    $this->instance = $this->model::findOrFail( $id ) ;
    $this->loadRelatedModels();
    
    return $this->instance;
  }

  public function update(array $data, mixed $id): Model
  {
    $this->instance = $this->model::findOrFail( $id );
    $this->instance->updateOrFail( $data );
    $this->updateRelatedModels();
    $this->loadRelatedModels();

    return $this->instance;

  }

  public function delete(mixed $id): bool
  {
    return $this->model::findOrFail( $id )->delete();
  }

  protected function updateRelatedModels(): void
  {
    /** @var Model $class */
    foreach($this->modelRelations as $name => [$class, $key]){
      if(!$this->request->has("data.$name")){
        continue;
      }

      $relationData = $this->request->input("data.$name");

      if(array_is_list($relationData)){
        foreach($relationData as $datum){
          $this->updateRelatedModel( $class, $datum );
        }
      }else{
        $this->updateRelatedModel($class, $relationData);
      }
    }
  }

  protected function updateRelatedModel(string $class, array $data): Model
  {
    $relationKeyName = (new $class)->getKeyName();
    $relationKeyValue = $data[ $relationKeyName ] ?? null;

    if(!$relationKeyValue){
      throw new Exception("Cannot update relation without $relationKeyName");
    }

    $identifiedBy = [
      $this->instance->getKeyName() => $this->instance->getKey(),
      $relationKeyName => $relationKeyValue,
    ];

    $data = array_merge($identifiedBy, $data);
        
    $createdOrUpdated = $class::updateOrCreate($identifiedBy, $data);
    if(!$createdOrUpdated->exists){
      throw new Exception("Could not create or update $class");
    }

    return $createdOrUpdated;
  }

  protected function createPrimaryModel(): Model
  {
    $this->instance = $this->model::create( $this->request->input('data') );
    return $this->instance;
  }

  protected function createRelatedModels(): array
  {
    $created = [];

    foreach($this->modelRelations as $name => [$class, $key]){
      if(!$this->request->has( "data.$name" )){
        continue;
      }
      assert($class instanceof Model);
      $data = $this->request->input(  "data.$name" );

      $modelKeyName = (new $class)->getKeyName();

      if(array_is_list($data)){
        foreach($data as $datum){
          $identifiedBy = array_filter([
            $key => $this->instance->getKey(),
            $modelKeyName => $datum[ $modelKeyName ] ?? null
          ]);
          $created[] = $this->createRelatedModel( $class, $identifiedBy, $datum );
        }
      }else{
        $identifiedBy = array_filter([
          $key => $this->instance->getKey(),
          $modelKeyName => $data[ $modelKeyName ] ?? null
        ]);
        $created[] = $this->createRelatedModel( $class, $identifiedBy, $data );
      }
      
    }
    return $created;
  }

  public function createRelatedModel(string $class, array $identifiedBy, array $data): Model
  {
    // if the table is the same table as the parent, update don't create
    $data = array_merge($identifiedBy, $data);

    return $this->instance->getTable() === (new $class())->getTable()
      ? $class::updateOrCreate( $identifiedBy, $data )
      : $class::create( $data );
  }


  protected function loadRelatedModels(): ?Model
  {
    if(
      !$this->request->has('with')
      || !is_array($this->request->input('with'))
      || !$this->instance->exists
    ){
      return null;
    }

    $this->instance->load( $this->request->input('with') );

    return $this->instance;
  }

  protected function addUserId(): void
  { 
    $user = Auth::guard('api')->user();
    if(!$user || !$this->request->has('data')){
      return;
    }

    $existingAll = $this->request->all();
    $existingAll['data'] = array_merge(
      $existingAll['data'],
      ['user_id' => $user->id]
    );
    
    $this->request->merge($existingAll);
  }

}