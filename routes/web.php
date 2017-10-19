<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware'=>'cross'],function (){
    Route::post('upload','API\V1\UploadController@uploadImage');
    Route::options('upload',function (){
        return 'SUCCESS';
    });
    Route::post('login','API\V1\UserController@login')->name('login');
    Route::group(['middleware'=>'auth'],function (){
        Route::post('teacher','API\V1\UserController@addTeacher');
        Route::get('teachers','API\V1\UserController@getTeachers');
        Route::post('edit/teacher/{id}','API\V1\UserController@editTeacher');
        Route::get('teacher/delete/{id}','API\V1\UserController@delTeacher');
        Route::get('orders','API\V1\OrderController@getOrders');
        Route::get('count','API\V1\PictureController@count');
        Route::post('article','API\V1\PictureController@addArticle');
        Route::post('config','API\V1\UserController@modifyConfig');
        Route::get('config','API\V1\UserController@getConfig');
        Route::get('picture/delete/{id}','API\V1\PictureController@delPicture');
        Route::get('pictures','API\V1\PictureController@getPictures');
        Route::get('count/pictures','API\V1\PictureController@pictureCount');
    });
});