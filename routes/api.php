<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');

Route::middleware('auth:api')->group(function () {
    Route::prefix('/user')->namespace('Auth')->group(function () {
        Route::get('/', 'UserController@show');
        Route::put('/', 'UserController@update');
        Route::delete('/', 'UserController@destroy');

        Route::post('avatar', 'AvatarController');
        Route::put('password', 'PasswordController');
    });

    Route::get('feed', 'FeedController');

    Route::get('search', 'SearchController@index');
    Route::get('search/users', 'SearchController@users');

    Route::get('files/{name}', 'FilesController');

    Route::get('suggestions', 'SuggestionsController');

    Route::post('logout', 'Auth\LoginController@logout');

    Route::prefix('notifications')->group(function () {
        Route::get('/', 'NotificationsController@index');
        Route::get('{id}', 'NotificationsController@show');
        Route::put('read', 'NotificationsController@read');
        Route::put('unread', 'NotificationsController@unread');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', 'UsersController@index');
        Route::get('{id}', 'UsersController@show');

        Route::get('{id}/posts', 'UserPostsController');
        Route::get('{id}/followers', 'UserFollowersController');
        Route::get('{id}/followees', 'UserFolloweesController');

        Route::post('{id}/follow', 'FollowingsController@follow');
        Route::delete('{id}/unfollow', 'FollowingsController@unfollow');
    });

    Route::prefix('posts')->group(function () {
        Route::post('/', 'PostsController@store');
        Route::post('{id}/update', 'PostsController@update');
        Route::delete('{id}', 'PostsController@destroy');

        Route::get('{id}/likes', 'PostLikesController');
        Route::get('{id}/comments', 'PostCommentsController');

        Route::post('{id}/like', 'LikesController@like');
        Route::delete('{id}/unlike', 'LikesController@unlike');
    });

    Route::prefix('comments')->group(function () {
        Route::post('/', 'CommentsController@store');
        Route::post('{id}/update', 'CommentsController@update');
        Route::delete('{id}', 'CommentsController@destroy');
    });

    Route::prefix('messages')->group(function () {
        Route::post('/', 'MessagesController@store');
        Route::get('{id}', 'MessagesController@show');
        Route::post('{id}/update', 'MessagesController@update');
        Route::delete('{id}', 'MessagesController@destroy');
    });

    Route::prefix('conversations')->group(function () {
        Route::get('/', 'ConversationsController@index');
        Route::get('{id}', 'ConversationsController@show');
        Route::put('/{id}', 'ConversationsController@update');
    });
});
