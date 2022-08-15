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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/ExportLahanTest', 'Api\LahanController@ExportLahanAllAdmin');

Route::get('/ExportLahanAllAdmin', 'Api\LahanController@ExportLahanAllAdmin');
Route::get('/ExportLahanAllSuperAdmin', 'Api\LahanController@ExportLahanAllSuperAdmin');
Route::get('/ExportSostamAllSuperAdmin', 'Api\SosialisasiTanamController@ExportSostamAllSuperAdmin');
Route::get('/ExportFarmerAllAdmin', 'Api\FarmerController@ExportFarmerAllAdmin');

Route::get('/CetakLabelSosTam', 'Api\SosialisasiTanamController@CetakLabelSosTam');
Route::get('/CetakLabelLubangTanam', 'Api\PlantingHoleController@CetakLabelLubangTanam');
Route::get('/CetakLabelLubangTanamTemp', 'Api\PlantingHoleController@CetakLabelLubangTanamTemp');
Route::get('/CetakBuktiPenyerahanTemp', 'Api\PlantingHoleController@CetakBuktiPenyerahanTemp');
Route::get('/CetakBuktiPenyerahan', 'Api\PlantingHoleController@CetakBuktiPenyerahan');
Route::get('/CetakExcelPlantingHoleAll', 'Api\PlantingHoleController@CetakExcelPlantingHoleAll');
Route::get('/CetakExcelLoadingPlan', 'Api\PlantingHoleController@CetakExcelLoadingPlan');
Route::get('/CetakExcelPackingPlan', 'Api\PlantingHoleController@CetakExcelPackingPlan');
Route::get('/CetakExcelShippingPlan', 'Api\PlantingHoleController@CetakExcelShippingPlan');
// Route::get('/ExportStockGudang', 'ApiBosman\UtilitiesController@ExportStockGudang');
