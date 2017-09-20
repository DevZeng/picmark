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
Route::get('test','API\V1\UserController@test');
Route::group(['prefix'=>'v1'],function (){
    Route::post('login','API\V1\UserController@OAuthLogin');
    Route::post('teacher/login','API\V1\UserController@TeacherLogin');
    Route::post('upload','API\V1\UploadController@uploadImage');
    Route::post('picture','API\V1\PictureController@addPicture');
    Route::post('picture/{id}/mark','API\V1\PictureController@addMark');
    Route::get('pictures','API\V1\PictureController@getPictures');
});