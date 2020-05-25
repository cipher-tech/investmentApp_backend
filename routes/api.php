<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user(); //fall in love, 2face
});

Route::post("check", function (Request $request){
    return response()->json("check good", 200);
});

Route::group([], function () {
    // 'middleware' => ['jwt.auth','api-header']
    // all routes to protected resources are registered here  
    Route::get('users/list', function(){
        $users = App\User::all();
        
        $response = ['success'=>true, 'data'=>$users];
        return response()->json($response, 201);
    });
    Route::get("users/all", "UserController@fetchAllUsers");
    Route::post("users/verify", "VerifyController@create");
});
Route::group([],function () {
    // ['middleware' => 'api-header']
    // The registration and login requests doesn't come with tokens 
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them

    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
});
