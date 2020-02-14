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

Route::post('qb', 'Api\QueryBuilderController@build');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


   Route::get('similars', 'Api\SimilarsController@index')->name('similars');
   Route::get('similars/history', 'Api\SimilarsController@history')->name('similars_merge_history');
   Route::get('similars/{id}', 'Api\SimilarsController@one')->name('similars');
   Route::post('similars/{id}/merge', 'Api\SimilarsController@merge')->name('similars_merge');
   Route::post('similars/{id}/discard', 'Api\SimilarsController@discard')->name('similars_discard');
   Route::post('similars/{id}/revert', 'Api\SimilarsController@revert')->name('similars_revert');
