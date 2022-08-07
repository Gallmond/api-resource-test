<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This class should be implemented per Model.
 * 
 * Set the $model property to the full className of the model.
 * Set the $modelRelations to a name => className k pair array.
 * 
 * //TODO refactor this it should not rely on the request itself
 * - change to use data input
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
    //TODO this will need to handle relations
    return $this->model::findOrFail( $id )->delete();
  }

  protected function updateRelatedModels(): void
  {
    foreach($this->modelRelations as $name => $class){
      if(!$this->request->has("data.$name")){
        continue;
      }

      assert($class instanceof Model);
      assert($this->instance instanceof Model);

      $relationKeyName = (new $class)->getKeyName();

      if(!$this->request->has("data.$name.$relationKeyName")){
        throw new Exception('Cannot update relation with out id');
      }

      $relationData = $this->request->input("data.$name");

      $identifiedBy = [
        $this->instance->getKeyName() => $this->instance->getKey(),
        $relationKeyName => $relationData[ $relationKeyName ]
      ];
          
      $related = $class::updateOrCreate($identifiedBy, $relationData);
      if(!$related->exists){
        throw new Exception('Could not update relation');
      }
    }
  }

  protected function createPrimaryModel(): Model
  {
    $this->instance = $this->model::create( $this->request->input('data') );
    return $this->instance;
  }

  protected function createRelatedModels(): array
  {
    $created = [];

    foreach($this->modelRelations as $name => $class){
      if(!$this->request->has( "data.$name" )){
        continue;
      }
      assert($class instanceof Model);
      $data = $this->request->input(  "data.$name" );

      // if the table is the same table as the parent, update don't create
      $instanceTable = $this->instance->getTable();
      $relatedTable = (new $class())->getTable();
      if($instanceTable === $relatedTable){
        $created[ $class ] = $class::findOrFail( $this->instance->id )->update( $data );
      }else{
        $created[ $class ] = $class::create( $data );
      }
      
    }
    return $created;
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