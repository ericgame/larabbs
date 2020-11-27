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

/*
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
*/

Route::get('/', 'TopicsController@index')->name('root');

//會員身分驗證
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

//會員註冊
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

//密碼重設
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//Email認證
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

//個人中心、編輯資料
/*
| GET|HEAD  | users/{user}      | users.show   | App\Http\Controllers\UsersController@show   | web
| PUT|PATCH | users/{user}      | users.update | App\Http\Controllers\UsersController@update | web
| GET|HEAD  | users/{user}/edit | users.edit   | App\Http\Controllers\UsersController@edit   | web
*/
Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'edit']]);

/*
| GET|HEAD  | topics | topics.index | App\Http\Controllers\TopicsController@index | web
| POST      | topics | topics.store | App\Http\Controllers\TopicsController@store | web
| GET|HEAD  | topics/create | topics.create | App\Http\Controllers\TopicsController@create | web
| PUT|PATCH | topics/{topic} | topics.update | App\Http\Controllers\TopicsController@update | web
| DELETE    | topics/{topic} | topics.destroy | App\Http\Controllers\TopicsController@destroy | web
| GET|HEAD  | topics/{topic}/edit | topics.edit | App\Http\Controllers\TopicsController@edit | web
*/
Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);

/*
| GET|HEAD | categories/{category} | categories.show | App\Http\Controllers\CategoriesController@show | web
*/
Route::resource('categories', 'CategoriesController', ['only' => ['show']]);

Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');

/*
| POST      | replies | replies.store | App\Http\Controllers\RepliesController@store | web
| GET|HEAD  | replies | replies.index | App\Http\Controllers\RepliesController@index | web
| GET|HEAD  | replies/create | replies.create | App\Http\Controllers\RepliesController@create | web
| DELETE    | replies/{reply} | replies.destroy | App\Http\Controllers\RepliesController@destroy | web
| PUT|PATCH | replies/{reply} | replies.update | App\Http\Controllers\RepliesController@update | web
| GET|HEAD  | replies/{reply} | replies.show | App\Http\Controllers\RepliesController@show | web
| GET|HEAD  | replies/{reply}/edit | replies.edit | App\Http\Controllers\RepliesController@edit | web
*/
Route::resource('replies', 'RepliesController', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);

Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);

/*
| GET|HEAD | notifications | notifications.index | App\Http\Controllers\NotificationsController@index | web
*/
Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);

Route::get('permission-denied', 'PagesController@permissionDenied')->name('permission-denied');


/*
Route::get('', '')->name('');
Route::post('', '')->name('');
Route::resource('', '', ['only' => ['', '', '']]);
*/
