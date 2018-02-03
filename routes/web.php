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




Route::get('/', 'StockController@view')->name('stock');





//stock controller
Route::post('/save-purchase-old-invoice', 'StockController@save_purchaseOLD');
Route::post('/save-purchase', 'StockController@save_purchase')->name('save_purchase');
Route::get('/add-stock', 'StockController@index')->name('StockPurchase-purchase');
Route::get('/stock-manage', 'StockController@view')->name('home');
Route::get('/view-stock-details/{ID}', 'StockController@viewDetails');
Route::post('/update-product-details', 'StockController@updateproductDetails');





//supplyer controller
Route::get('/getAllSupplyer', 'SupplyerController@getAllsupplyer');
Route::post('/save-supplyerAJAX', 'SupplyerController@save_supplyerAJAX');



//Settings route controller
//brand

Route::get('/settings', 'SettingsController@add_brand');
Route::post('/save-brand', 'SettingsController@save_brand');
Route::get('/published-brand/{productId}', 'SettingsController@published_brand');
Route::get('/unpublished-brand/{productId}', 'SettingsController@unpublished_brand');
Route::post('/update-brand-info', 'SettingsController@update_brand_info');


//Product Category -STYLE

Route::get('/get-brand', 'ReportController@getBrand');

Route::get('/get-style', 'ProductController@get');
Route::post('/save-style', 'ProductController@save');
Route::get('/published-style/{ID}', 'ProductController@publish');
Route::get('/unpublished-style/{ID}', 'ProductController@unpublish');
Route::post('/update-style-info', 'ProductController@update');


Route::get('/makepdfpurchase/{ID}', 'StockController@pdf');
