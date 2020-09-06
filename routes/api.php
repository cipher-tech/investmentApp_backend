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

Route::post("check", function (Request $request) {
    return response()->json("check good", 200);
});
Route::group([], function () {
    Route::get("admin/get-rate", "RateController@index");
    Route::post("users/userSellCoin", "HistoryController@userSellCoin");
    Route::post("users/userSellCard", "HistoryController@userSellCard");
});

Route::group(['middleware' => ['jwt.auth']], function () {
    // 'middleware' => ['jwt.auth','api-header']
    // all routes to protected resources are registered here  
    Route::post("user/get", "UserController@getUser");

    Route::get("users/all", "UserController@fetchAllUsers");
    Route::post("users/update", "UserController@update");
    Route::post("users/requestVerification", "VerifyController@create");
    Route::post("users/updateUserInfo", "UserController@updateUserInfo");
    Route::post("users/updateUserPassword", "UserController@updateUserPassword");
    Route::post("users/userTransactions", "UserController@userTransactions");

    Route::get("users/unverified", "VerifyController@getUnverifiedUsers");
    Route::post("users/verify", "VerifyController@verifyUsers");

    Route::post("users/requestEmailVerification", "VerifyController@VerifyViaEmail");
    Route::post("users/verify-delete", "VerifyController@destory");

    Route::post("users/userBuyCoin", "HistoryController@userBuyCoin");
    Route::get("admin/getOrders", "HistoryController@index");
    Route::post("admin/confirmTransaction", "HistoryController@confirmTransaction");
    Route::post("admin/destroyTransaction", "HistoryController@destroyTransaction");


    Route::post("users/deposit", "DepositController@store");
    Route::get("admin/deposits", "DepositController@index");
    Route::post("admin/acceptDeposit", "DepositController@update");
    Route::post("admin/delete-deposit", "DepositController@destroy");

    Route::post("users/widthdrawl", "WidthdrawlController@store");
    Route::get("admin/widthdrawls", "WidthdrawlController@index");
    Route::post("admin/acceptWidthdrawl", "WidthdrawlController@update");
    Route::post("admin/delete-Widthdrawl", "WidthdrawlController@destroy");

    Route::post("admin/add-plan", "PlanController@store");
    Route::get("admin/get-plan", "PlanController@index");
    Route::post("admin/register-plan", "PlanController@registerPlan");

    Route::post("admin/add-rate", "RateController@create");
    Route::post("admin/update-rate", "RateController@update");

    Route::post("admin/add-giftcard", "RateController@createGiftcard");
    Route::post("admin/edit-giftcard", "RateController@editGiftcard");

    Route::get("getCoinAddress", "StatisticController@getCoinAddress");
    Route::post("user/getStats", "StatisticController@getUserStats");
});
Route::group([], function () {
    // ['middleware' => 'api-header']
    // The registration and login requests doesn't come with tokens 
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them

    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
    Route::post('passwordReset', 'UserController@resetPassword');
});

Route::get('send-mail', function () {

    $details = [
        'name' => "name",
        'title' => 'Welcome',
        "header" => " Registration Successful",
        'body' =>   [
            "This is to confirm your registration. Please kindly visit the link below to verify your account. ",
            "Or copy the link and paste it on your browser. ",
            
            // "credentials used in registration to access your dashboard and lots of other features. Thanks and welcome",
            // "To start Earning, you need to make a deposit",
            // "Choose an investment plan, invest and Earn"
        ],
        "links" => [
            "registerLink" => env("REMOTE_SERVER_NAME") . 'login/'. "slug",
        ],
        "companyName" => env('COMPANY_NAME', '')
    ];

    \Mail::to("nickchibuikem@gmail.com")->send(new \App\Mail\GenMailer($details));

    dd("Email is Sent.");
    return "sent";
});

//https://coin-app-ackend.herokuapp.com/ | https://git.heroku.com/coin-app-ackend.git
//https://coin-app-ackend.herokuapp.com/ | https://git.heroku.com/coin-app-ackend.git
// db-password = y.e9KWCpRci_YMj
// db-name = coin_app
// db-username = nickchibuikem