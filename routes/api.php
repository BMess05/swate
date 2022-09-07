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
Route::group(['middleware' => ['api']], function ($router) {

    /******notification****/
    Route::get('notification', 'App\Http\Controllers\Api\UserController@notification');
    Route::post('import/ingredients', 'App\Http\Controllers\Api\UserController@importIngredients');
    Route::post('import/recipes', 'App\Http\Controllers\Api\UserController@importRecipes');

});

Route::group([

    'middleware' => ['api', 'localization'],
    // 'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
    Route::post('me', 'App\Http\Controllers\Api\AuthController@me');
    Route::post('register', 'App\Http\Controllers\Api\RegisterController@register');
    Route::post('forgot_password', 'App\Http\Controllers\Api\ForgotPasswordController@forgot_password');
    Route::post('update_password', 'App\Http\Controllers\Api\ForgotPasswordController@update_password');
    /******notification****/
    Route::get('notification', 'App\Http\Controllers\Api\UserController@notification');

});

Route::group([ 'middleware' => ['jwt.verify', 'localization'] ], function ($router) {
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\Api\AuthController@refresh');
    Route::post('add_user_info', 'App\Http\Controllers\Api\UserController@add_user_info');



    //*****ingredients*****//
    Route::get('get_ingredients_by_category', 'App\Http\Controllers\Api\UserController@getIngredientsByCategory');
    Route::post('save_user_ingredients', 'App\Http\Controllers\Api\UserController@saveUserIngredients');
    Route::post('search_ingredients', 'App\Http\Controllers\Api\UserController@searchIngredients');
    Route::get('ingredients_catgeory', 'App\Http\Controllers\Api\IngredientController@ingredientsCatgeory');
    Route::post('search_multiple_ingredients', 'App\Http\Controllers\Api\IngredientController@searchMultipleIngredients');



    //********Recipes****//
    Route::post('get_recipes', 'App\Http\Controllers\Api\ReceipeController@getRecipes');
    Route::post('get_recipe_by_ingredient', 'App\Http\Controllers\Api\ReceipeController@getRecipesByIngredients');

    //get receipe by ingredeient (optional)
    Route::post('get_filtered_recipies', 'App\Http\Controllers\Api\ReceipeController@searchRecipes');

    //search recipe by text
    Route::post('search_recipes', 'App\Http\Controllers\Api\ReceipeController@searchRecipes');
    Route::post('favourite_recipes', 'App\Http\Controllers\Api\ReceipeController@favouriteRecipe');
    Route::post('get_favourite_recipes', 'App\Http\Controllers\Api\ReceipeController@getFavouriteRecipes');
    Route::post('RawQuery', 'App\Http\Controllers\Api\ReceipeController@RawQuery');

    //*****filter******//
    Route::get('get_filters', 'App\Http\Controllers\Api\UserController@getFilters');
    Route::post('remove_my_inventory', 'App\Http\Controllers\Api\UserController@removeMyInventory');
    Route::post('get_my_inventory', 'App\Http\Controllers\Api\UserController@getMyInventory');
    Route::post('change_password', 'App\Http\Controllers\Api\UserController@change_password');
    Route::get('get_profile', 'App\Http\Controllers\Api\UserController@getProfile');
    Route::post('upload_image', 'App\Http\Controllers\Api\UserController@upload_image');
    Route::post('update_profile', 'App\Http\Controllers\Api\UserController@update_profile');
    Route::post('update_notification', 'App\Http\Controllers\Api\UserController@update_notification');

    //***faq***//
    Route::get('faqs', 'App\Http\Controllers\Api\FaqController@faqs');


    //****categories*****//



});
