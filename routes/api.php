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
    Route::post('teacher/login','API\V1\UserController@TeacherLogin');
    Route::post('upload','API\V1\UploadController@uploadImage');
    Route::post('picture','API\V1\PictureController@addPicture')->middleware('student');
    Route::post('picture/{id}/mark','API\V1\PictureController@addMark')->middleware('teacher');
    Route::get('pictures','API\V1\PictureController@getPictures');
    Route::get('picture/{id}','API\V1\PictureController@getPicture');
    Route::get('teacher/count','API\V1\UserController@count')->middleware('teacher');
    Route::post('order','API\V1\OrderController@makeOrder');
    Route::post('pay/notify','API\V1\OrderController@notify');
    Route::get('article','API\V1\PictureController@getArticle');
});