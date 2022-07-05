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
    return $request->user();
});
###########################################################################################################################################
// user authentication

Route::group(['prefix'=>'user'],function(){

 Route::post('login','UserController@login');

 Route::post('logout','UserController@logout')->middleware('checkLogin:user-api');

 Route::post('register','UserController@UserRegister');


});


// Admin
Route::group(['prefix'=>'admin'],function(){

Route::post('login','AdminController@login');

Route::post('logout','AdminController@logout')->middleware('checkAdmin:admin-api');

Route::get('get_all_user','AdminController@get_all_user')->middleware('checkLogin:admin-api');

Route::delete('delete_user_by_id/{id}','AdminController@delete_user')->middleware('checkLogin:admin-api');

Route::post('searsh_user_by_id/{name}','AdminController@search_user')->middleware('checkLogin:admin-api');

Route::post('sort_user/{sort_by}','AdminController@sort_user')->middleware('checkLogin:admin-api');

Route::post('register_user','AdminController@regester_user')->middleware('checkLogin:admin-api');

Route::get('ff','AdminController@ff');

});




