<?php

use Illuminate\Support\Facades\Route;

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

Route::get('migrateTagsFromReceipe', 'App\Http\Controllers\Admin\IndependentController@index');
Auth::routes(['register' => false]);
// Auth::routes();
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout');
Route::get('script_for_db', 'App\Http\Controllers\Admin\ReceipeController@script_for_db');


Route::group(['middleware' => ['auth'] ], function() {
    Route::get('/', 'App\Http\Controllers\Admin\DashboardController@dashboard')->name('dashboard');
    Route::get('users', 'App\Http\Controllers\Admin\UserController@listUsers')->name('users');
    Route::get('user/add', 'App\Http\Controllers\Admin\UserController@addUser')->name('addUser');
    Route::post('user/save', 'App\Http\Controllers\Admin\UserController@saveUser')->name('saveUser');
    Route::get('user/edit/{id}', 'App\Http\Controllers\Admin\UserController@editUser')->name('editUser');
    Route::post('user/update/{id}', 'App\Http\Controllers\Admin\UserController@updateUser')->name('updateUser');
    Route::get('user/delete/{id}', 'App\Http\Controllers\Admin\UserController@deleteUser')->name('deleteUser');
    Route::get('user/profile/{id}', 'App\Http\Controllers\Admin\UserController@userProfile')->name('userProfile');
    Route::get('user/export/', 'App\Http\Controllers\Admin\UserController@exportUsers')->name('exportUsers');

    Route::group(['prefix'=>'ingredients'], function() {
        Route::get('/', 'App\Http\Controllers\Admin\ItemController@listItems')->name('listItems');
        Route::get('/add', 'App\Http\Controllers\Admin\ItemController@addItem')->name('addItem');
        Route::post('/save', 'App\Http\Controllers\Admin\ItemController@saveItem')->name('saveItem');
        Route::get('/edit/{id}', 'App\Http\Controllers\Admin\ItemController@editItem')->name('editItem');
        Route::post('/update/{id}', 'App\Http\Controllers\Admin\ItemController@updateItem')->name('updateItem');
        Route::get('/delete/{id}', 'App\Http\Controllers\Admin\ItemController@deleteItem')->name('deleteItem');
        Route::get('/unit', 'App\Http\Controllers\Admin\ItemController@getUnit')->name('getUnit');
        Route::post('/getIngredients', 'App\Http\Controllers\Api\IngredientController@getIngredients')->name('getIngredients');

    });

    Route::group(['prefix'=>'ingredient/categories'], function() {
        Route::get('/', 'App\Http\Controllers\Admin\CategoryController@listCategories')->name('listCategories');
        Route::get('/add', 'App\Http\Controllers\Admin\CategoryController@addCategory')->name('addCategory');
        Route::post('/save', 'App\Http\Controllers\Admin\CategoryController@saveCategory')->name('saveCategory');
        Route::get('/edit/{id}', 'App\Http\Controllers\Admin\CategoryController@editCategory')->name('editCategory');
        Route::post('/update/{id}', 'App\Http\Controllers\Admin\CategoryController@updateCategory')->name('updateCategory');
        Route::get('/delete/{id}', 'App\Http\Controllers\Admin\CategoryController@deleteCategory')->name('deleteCategory');
        
    });

    Route::get('receipes', 'App\Http\Controllers\Admin\ReceipeController@listReceipes')->name('listReceipes');
    Route::get('receipe/add', 'App\Http\Controllers\Admin\ReceipeController@addReceipe')->name('addReceipe');
    Route::post('receipe/save', 'App\Http\Controllers\Admin\ReceipeController@saveReceipe')->name('saveReceipe');
    Route::get('receipe/edit/{id}', 'App\Http\Controllers\Admin\ReceipeController@editReceipe')->name('editReceipe');
    Route::post('receipe/update/{id}', 'App\Http\Controllers\Admin\ReceipeController@updateReceipe')->name('updateReceipe');
    Route::get('receipe/delete/{id}', 'App\Http\Controllers\Admin\ReceipeController@deleteReceipe')->name('deleteReceipe');


    Route::group(['prefix'=>'diet/categories/'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\DietCategoriesController@index')->name('listDietCategories');
        Route::get('create', 'App\Http\Controllers\Admin\DietCategoriesController@create')->name('addDietCategory');
        Route::post('store', 'App\Http\Controllers\Admin\DietCategoriesController@store')->name('storeDietCategory');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\DietCategoriesController@destroy')->name('deleteDietCategory');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\DietCategoriesController@edit')->name('editDietCategory');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\DietCategoriesController@update')->name('updateDietCategories');
    });

    Route::group(['prefix'=>'cuisine/types/'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\CuisineTypesController@index')->name('listCuisineTypes');
        Route::get('create', 'App\Http\Controllers\Admin\CuisineTypesController@create')->name('addCuisineType');
        Route::post('store', 'App\Http\Controllers\Admin\CuisineTypesController@store')->name('storeCuisineType');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\CuisineTypesController@destroy')->name('deleteCuisineType');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\CuisineTypesController@edit')->name('editCuisineType');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\CuisineTypesController@update')->name('updateCuisineType');
    });

    Route::group(['prefix'=>'dish/types/'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\DishTypesController@index')->name('listDishTypes');
        Route::get('create', 'App\Http\Controllers\Admin\DishTypesController@create')->name('addDishType');
        Route::post('store', 'App\Http\Controllers\Admin\DishTypesController@store')->name('storeDishType');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\DishTypesController@destroy')->name('deleteDishType');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\DishTypesController@edit')->name('editDishType');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\DishTypesController@update')->name('updateDishType');
    });

    Route::group(['prefix'=>'time/filters/'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\TimeFiltersController@index')->name('listTimeFilters');
        Route::get('create', 'App\Http\Controllers\Admin\TimeFiltersController@create')->name('addTimeFilter');
        Route::post('store', 'App\Http\Controllers\Admin\TimeFiltersController@store')->name('storeTimeFilter');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\TimeFiltersController@destroy')->name('deleteTimeFilter');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\TimeFiltersController@edit')->name('editTimeFilter');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\TimeFiltersController@update')->name('updateTimeFilter');
    });

    //Faq
    Route::group(['prefix'=>'faq'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\FaqController@index')->name('listFaqs');
        Route::get('create', 'App\Http\Controllers\Admin\FaqController@create')->name('addFaq');
        Route::post('store', 'App\Http\Controllers\Admin\FaqController@store')->name('storeFaq');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\FaqController@destroy')->name('deleteFaq');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\FaqController@edit')->name('editFaq');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\FaqController@update')->name('updateFaq');
    });

    Route::group(['prefix'=>'unit'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\UnitController@index')->name('listUnits');
        Route::get('create', 'App\Http\Controllers\Admin\UnitController@create')->name('addUnit');
        Route::post('store', 'App\Http\Controllers\Admin\UnitController@store')->name('storeUnit');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\UnitController@edit')->name('editUnit');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\UnitController@update')->name('updateUnit');
         Route::get('delete/{id}', 'App\Http\Controllers\Admin\UnitController@destroy')->name('deleteUnit');
    });
    Route::post('import/ingredients', 'App\Http\Controllers\Admin\ItemController@importIngredients')->name('importIngredients');
    
    Route::group(['prefix'=>'import'], function() {
        Route::post('tags', 'App\Http\Controllers\Admin\TagsController@import')->name('importTags');
        Route::post('dishType', 'App\Http\Controllers\Admin\DishTypesController@import')->name('importDishTypes');
        Route::post('cuisineType', 'App\Http\Controllers\Admin\CuisineTypesController@import')->name('importCuisineType');
        Route::post('dietCategories', 'App\Http\Controllers\Admin\DietCategoriesController@import')->name('importDietCategories');
    });
    Route::group(['prefix'=>'tags'], function() {
        Route::get('list', 'App\Http\Controllers\Admin\TagsController@index')->name('listTags');
        Route::get('search', 'App\Http\Controllers\Admin\TagsController@search')->name('searchTags');
        Route::get('create', 'App\Http\Controllers\Admin\TagsController@create')->name('addTag');
        Route::post('store', 'App\Http\Controllers\Admin\TagsController@store')->name('storeTag');
        Route::get('edit/{id}', 'App\Http\Controllers\Admin\TagsController@edit')->name('editTag');
        Route::post('update/{id}', 'App\Http\Controllers\Admin\TagsController@update')->name('updateTag');
        Route::get('delete/{id}', 'App\Http\Controllers\Admin\TagsController@destroy')->name('deleteTag');
    });


});
