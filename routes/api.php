<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function(){
    Route::apiResources([
        'users' => UserController::class,
        'posts' => PostController::class,
    ]);
});

Route::post('/test', function(Request $request){

    $existingAll = $request->all();
    $existingAll['data'] = array_merge($existingAll['data'], ['user_id' => 123]);
    
    $request->merge($existingAll);

    return response()->json(['foo' => 'bar', 'all' => $request->all()]);
})->name('test.test');


