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

Route::group(['middleware' => ['jwt.auth']], function () {
    // 'middleware' => ['jwt.auth','api-header']
    // all routes to protected resources are registered here  
    Route::get('users/list', function(){
        $users = App\User::all();
        
        $response = ['success'=>true, 'data'=>$users];
        return response()->json($response, 201);
    });
    Route::get("users/all", "UserController@fetchAllUsers");
    Route::post("users/update", "UserController@update");
    Route::post("users/requestVerification", "VerifyController@create");

    Route::get("users/unverified", "VerifyController@getUnverifiedUsers");
    Route::post("users/verify", "VerifyController@verifyUsers");


    Route::post("users/deposit", "DepositController@store");
    Route::get("admin/deposits", "DepositController@index");
    Route::post("admin/acceptDeposit", "DepositController@update");

    Route::post("users/widthdrawl", "WidthdrawlController@store");
    Route::get("admin/widthdrawls", "WidthdrawlController@index");
    Route::post("admin/acceptWidthdrawl", "WidthdrawlController@update");

    Route::post("admin/add-rate", "RateController@create");
    Route::get("admin/get-rate", "RateController@index");
    Route::post("admin/update-rate", "RateController@update");

});
Route::group([],function () {
    // ['middleware' => 'api-header']
    // The registration and login requests doesn't come with tokens 
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them

    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
});
