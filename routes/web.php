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

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/post', 'PostController@create');
Route::post('/home/post/store', 'PostController@store');
Route::get('/home/posts/{userId}', 'PostController@userView')
  ->where('userId', '[0-9]+');
Route::get('/home/posts/calendar/{userId}', 'PostController@userCalendarView')
  ->where('userId', '[0-9]+');

Route::get('/home/user/profile', 'UserController@profileView');
Route::post('/home/user/profile/update', 'UserController@profileUpdate');

Route::get('/home/users', 'UserController@usersView');

// ユーザメニュー
Route::get('/home/user/{userId}/posts', 'UserMenuController@postsView');
Route::get('/home/user/{userId}/calendar', 'UserMenuController@calendarView');
Route::get('/home/user/{userId}/follows', 'UserMenuController@followsView');
Route::get('/home/user/{userId}/followers', 'UserMenuController@followersView');
Route::get('/home/user/{userId}/likes', 'UserMenuController@likesView');

Route::post('/home/posts/dayPosts', 'PostController@dayPosts');
// Ajax
Route::post('/home/like/add', 'LikeController@likeAdd');
Route::post('/home/like/take', 'LikeController@likeTake');

Route::post('/home/follow/add', 'FollowController@followAdd');
Route::post('/home/follow/take', 'FollowController@followTake');

Route::post('/home/posts/dayPosts', 'PostController@dayPosts');


Route::get('/{test}', function ($test) {
    return 'Hello World' . $test;
});
