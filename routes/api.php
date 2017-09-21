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
    Route::post('oath/login','API\V1\UserController@OAuthLogin');
    Route::post('login','API\V1\UserController@login');
    Route::post('teacher/login','API\V1\UserController@TeacherLogin');
    Route::post('upload','API\V1\UploadController@uploadImage');
    Route::post('picture','API\V1\PictureController@addPicture');
    Route::post('picture/{id}/mark','API\V1\PictureController@addMark');
    Route::get('pictures','API\V1\PictureController@getPictures');
    Route::get('picture/{id}','API\V1\PictureController@getPicture');
    Route::get('count','API\V1\PictureController@count');
    Route::post('teacher','API\V1\UserController@addTeacher');
    Route::get('teachers','API\V1\UserController@getTeachers');
    Route::delete('teacher','API\V1\UserController@delTeacher');
    Route::get('orders','API\V1\OrderController@getOrders');
});