<?php

use Illuminate\Http\Request;

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
    return $request->user();
});

Route::get('sms', 'SmsController@get');
Route::get('webhook', 'WebhookController@get');
Route::get('el', 'ElController@get');
Route::get('el/{refreshToken}', 'ElController@get');
Route::get('el/{start_date}/{end_date}/{price_area}/{refreshToken}', 'ElController@getFromDate');
Route::get('el/{refreshToken}/delete', 'ElController@delete');
Route::post('webhook', 'WebhookController@get');
