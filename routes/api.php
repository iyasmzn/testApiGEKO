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

Route::post('Regist', 'UserController@Regist');
Route::post('Login', 'UserController@Login');
Route::post('LoginWeb', 'UserController@LoginWeb');
Route::post('ForgotPassword', 'UserController@ForgotPassword');

Route::get('GetFarmerAllTemp', 'Api\FarmerController@GetFarmerAllTempDelete');
Route::get('GetFarmerDetailTemp', 'Api\FarmerController@GetFarmerDetail');

Route::get('GetApi', 'UserController@GetApi');
Route::get('UpdateFarmerFF', 'Api\FarmerController@UpdateFarmerFF');

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('Logout', 'UserController@Logout');
    Route::post('EditProfile', 'UserController@EditProfile');
    Route::post('EditUser', 'UserController@EditUser');
    Route::post('DeleteUser', 'UserController@DeleteUser');
    Route::post('ResetPasswordUser', 'UserController@ResetPasswordUser');
    Route::get('GetUser', 'UserController@GetUser');

    
    Route::get('Dashboard', 'Api\UtilitiesController@Dashboard');
  
    Route::get('GetProvinceAdmin', 'Api\UtilitiesController@GetProvinceAdmin');
    Route::get('GetKabupatenAdmin', 'Api\UtilitiesController@GetKabupatenAdmin');
    Route::get('GetKecamatanAdmin', 'Api\UtilitiesController@GetKecamatanAdmin');
    Route::get('GetDesaAdmin', 'Api\UtilitiesController@GetDesaAdmin');
    Route::get('GetManagementUnitAdmin', 'Api\UtilitiesController@GetManagementUnitAdmin');
    Route::get('GetTargetAreaAdmin', 'Api\UtilitiesController@GetTargetAreaAdmin');

    Route::get('GetProvince', 'Api\UtilitiesController@GetProvince');
    Route::get('GetKabupaten', 'Api\UtilitiesController@GetKabupaten');
    Route::get('GetKecamatan', 'Api\UtilitiesController@GetKecamatan');
    Route::get('GetDesa', 'Api\UtilitiesController@GetDesa');

    Route::post('AddProvince', 'Api\UtilitiesController@AddProvince');
    Route::post('AddKabupaten', 'Api\UtilitiesController@AddKabupaten');
    Route::post('AddKecamatan', 'Api\UtilitiesController@AddKecamatan');
    Route::post('AddDesa', 'Api\UtilitiesController@AddDesa');

    Route::post('UpdateProvince', 'Api\UtilitiesController@UpdateProvince');
    Route::post('UpdateKabupaten', 'Api\UtilitiesController@UpdateKabupaten');
    Route::post('UpdateKecamatan', 'Api\UtilitiesController@UpdateKecamatan');
    Route::post('UpdateDesa', 'Api\UtilitiesController@UpdateDesa');

    Route::post('DeleteProvince', 'Api\UtilitiesController@DeleteProvince');
    Route::post('DeleteKabupaten', 'Api\UtilitiesController@DeleteKabupaten');
    Route::post('DeleteKecamatan', 'Api\UtilitiesController@DeleteKecamatan');
    Route::post('DeleteDesa', 'Api\UtilitiesController@DeleteDesa');

    Route::get('GetManagementUnit', 'Api\UtilitiesController@GetManagementUnit');
    Route::get('GetTargetArea', 'Api\UtilitiesController@GetTargetArea');
    Route::post('AddManagementUnit', 'Api\UtilitiesController@AddManagementUnit');
    Route::post('AddTargetArea', 'Api\UtilitiesController@AddTargetArea');
    Route::post('UpdateManagementUnit', 'Api\UtilitiesController@UpdateManagementUnit');
    Route::post('UpdateTargetArea', 'Api\UtilitiesController@UpdateTargetArea');
    Route::post('DeleteManagementUnit', 'Api\UtilitiesController@DeleteManagementUnit');
    Route::post('DeleteTargetArea', 'Api\UtilitiesController@DeleteTargetArea');

    Route::get('GetVerification', 'Api\UtilitiesController@GetVerification');
    Route::post('AddVerification', 'Api\UtilitiesController@AddVerification');
    Route::post('UpdateVerification', 'Api\UtilitiesController@UpdateVerification');
    Route::post('DeleteVerification', 'Api\UtilitiesController@DeleteVerification');

    Route::get('GetPekerjaan', 'Api\UtilitiesController@GetPekerjaan');
    Route::get('GetSuku', 'Api\UtilitiesController@GetSuku');
    Route::post('AddPekerjaan', 'Api\UtilitiesController@AddPekerjaan');
    Route::post('AddSuku', 'Api\UtilitiesController@AddSuku');
    Route::post('UpdatePekerjaan', 'Api\UtilitiesController@UpdatePekerjaan');
    Route::post('UpdateSuku', 'Api\UtilitiesController@UpdateSuku');
    Route::post('DeletePekerjaan', 'Api\UtilitiesController@DeletePekerjaan');
    Route::post('DeleteSuku', 'Api\UtilitiesController@DeleteSuku');

    Route::get('GetAllMenuAccess', 'Api\UtilitiesController@GetAllMenuAccess');

    Route::get('GetFarmerAllAdmin', 'Api\FarmerController@GetFarmerAllAdmin');
    Route::get('GetFarmerNoAll', 'Api\FarmerController@GetFarmerNoAll');
    Route::get('GetFarmerAll', 'Api\FarmerController@GetFarmerAll');
    Route::get('GetFarmerNotComplete', 'Api\FarmerController@GetFarmerNotComplete');
    Route::get('GetFarmerCompleteNotApprove', 'Api\FarmerController@GetFarmerCompleteNotApprove');
    Route::get('GetFarmerCompleteAndApprove', 'Api\FarmerController@GetFarmerCompleteAndApprove');
    Route::get('GetFarmerDetail', 'Api\FarmerController@GetFarmerDetail');
    Route::get('GetFarmerDetailKtpNo', 'Api\FarmerController@GetFarmerDetailKtpNo');
    Route::get('GetFarmerNoDropDown', 'Api\FarmerController@GetFarmerNoDropDown');
    Route::get('GetFarmerGroupsDropDown', 'Api\FarmerController@GetFarmerGroupsDropDown');
    Route::post('AddMandatoryFarmer', 'Api\FarmerController@AddMandatoryFarmer');
    Route::post('UpdateFarmer', 'Api\FarmerController@UpdateFarmer');
    Route::post('SoftDeleteFarmer', 'Api\FarmerController@SoftDeleteFarmer');
    Route::post('VerificationFarmer', 'Api\FarmerController@VerificationFarmer');
    Route::post('DeleteFarmer', 'Api\FarmerController@DeleteFarmer');

    Route::get('GetLahanAllAdmin', 'Api\LahanController@GetLahanAllAdmin');
    Route::get('GetLahanAll', 'Api\LahanController@GetLahanAll');
    Route::get('GetLahanNotComplete', 'Api\LahanController@GetLahanNotComplete');
    Route::get('GetLahanCompleteNotApprove', 'Api\LahanController@GetLahanCompleteNotApprove');
    Route::get('GetCompleteAndApprove', 'Api\LahanController@GetCompleteAndApprove');
    Route::get('GetLahanDetail', 'Api\LahanController@GetLahanDetail');
    Route::get('GetLahanDetailLahanNo', 'Api\LahanController@GetLahanDetailLahanNo');
    Route::get('GetLahanDetailBarcode', 'Api\LahanController@GetLahanDetailBarcode');
    Route::post('AddMandatoryLahan', 'Api\LahanController@AddMandatoryLahan');
    Route::post('AddMandatoryLahanComplete', 'Api\LahanController@AddMandatoryLahanComplete');
    Route::post('AddMandatoryLahanBarcode', 'Api\LahanController@AddMandatoryLahanBarcode');
    Route::get('GetLahanDetailTrees', 'Api\LahanController@GetLahanDetailTrees');
    Route::post('AddDetailLahan', 'Api\LahanController@AddDetailLahan');
    Route::post('DeleteDetailLahan', 'Api\LahanController@DeleteDetailLahan');
    Route::post('UpdateLahan', 'Api\LahanController@UpdateLahan');
    Route::post('UpdateLahanGIS', 'Api\LahanController@UpdateLahanGIS');
    Route::post('VerificationLahan', 'Api\LahanController@VerificationLahan');
    Route::post('SoftDeleteLahan', 'Api\LahanController@SoftDeleteLahan');
    Route::post('UpdateDetailLahanPohon', 'Api\LahanController@UpdateDetailLahanPohon');    
    
    Route::get('GetLahanUmumAllAdmin', 'Api\LahanUmumController@GetLahanUmumAllAdmin');
    Route::get('GetLahanUmumAll', 'Api\LahanUmumController@GetLahanUmumAll');
    Route::get('GetLahanUmumNotComplete', 'Api\LahanUmumController@GetLahanUmumNotComplete');
    Route::get('GetLahanUmumCompleteNotApprove', 'Api\LahanUmumController@GetLahanUmumCompleteNotApprove');
    Route::get('GetCompleteAndApprove', 'Api\LahanUmumController@GetCompleteAndApprove');
    // Route::get('GetLahanUmumDetail', 'Api\LahanUmumController@GetLahanUmumDetail');
    Route::get('GetLahanUmumDetailLahanNo', 'Api\LahanUmumController@GetLahanUmumDetailLahanNo');
    Route::get('GetLahanUmumDetailBarcode', 'Api\LahanUmumController@GetLahanUmumDetailBarcode');
    Route::post('AddMandatoryLahanUmum', 'Api\LahanUmumController@AddMandatoryLahanUmum');
    Route::post('AddMandatoryLahanUmumComplete', 'Api\LahanUmumController@AddMandatoryLahanUmumComplete');
    Route::post('AddMandatoryLahanUmumBarcode', 'Api\LahanUmumController@AddMandatoryLahanUmumBarcode');
    // Route::get('GetLahanUmumDetailTrees', 'Api\LahanUmumController@GetLahanUmumDetailTrees');
    // Route::post('AddDetailLahanUmum', 'Api\LahanUmumController@AddDetailLahanUmum');
    // Route::post('DeleteDetailLahanUmum', 'Api\LahanUmumController@DeleteDetailLahanUmum');
    Route::post('UpdateLahanUmum', 'Api\LahanUmumController@UpdateLahanUmum');
    // Route::post('UpdateLahanUmumGIS', 'Api\LahanUmumController@UpdateLahanUmumGIS');
    // Route::post('VerificationLahanUmum', 'Api\LahanUmumController@VerificationLahanUmum');
    Route::post('SoftDeleteLahanUmum', 'Api\LahanUmumController@SoftDeleteLahanUmum');
    // Route::post('UpdateDetailLahanUmumPohon', 'Api\LahanUmumController@UpdateDetailLahanUmumPohon'); 

    Route::get('GetTreesAll', 'Api\TreesController@GetTreesAll');
    Route::get('GetTreesLocation', 'Api\TreesController@GetTreesLocation');
    Route::get('GetTrees', 'Api\TreesController@GetTrees');
    Route::get('GetTreesDetail', 'Api\TreesController@GetTreesDetail');
    Route::post('AddTrees', 'Api\TreesController@AddTrees');
    Route::post('UpdateTrees', 'Api\TreesController@UpdateTrees');
    Route::post('DeleteTrees', 'Api\TreesController@DeleteTrees');

    Route::get('GetFieldFacilitatorAllWeb', 'Api\FieldFacilitatorController@GetFieldFacilitatorAllWeb');
    Route::get('GetFieldFacilitatorAll', 'Api\FieldFacilitatorController@GetFieldFacilitatorAll');
    Route::get('GetFieldFacilitator', 'Api\FieldFacilitatorController@GetFieldFacilitator');
    Route::get('GetFieldFacilitatorDetail', 'Api\FieldFacilitatorController@GetFieldFacilitatorDetail');
    Route::post('AddFieldFacilitator', 'Api\FieldFacilitatorController@AddFieldFacilitator');
    Route::post('UpdateFieldFacilitator', 'Api\FieldFacilitatorController@UpdateFieldFacilitator');
    Route::post('DeleteFieldFacilitator', 'Api\FieldFacilitatorController@DeleteFieldFacilitator');

    Route::get('GetActivityUserId', 'Api\ActivityController@GetActivityUserId');
    Route::get('GetActivityLahanUser', 'Api\ActivityController@GetActivityLahanUser');
    Route::post('AddActivity', 'Api\ActivityController@AddActivity');
    Route::post('UpdateActivity', 'Api\ActivityController@UpdateActivity');
    Route::post('DeleteActivity', 'Api\ActivityController@DeleteActivity');
    Route::get('GetActivityDetail', 'Api\ActivityController@GetActivityDetail');
    Route::post('AddActivityDetail', 'Api\ActivityController@AddActivityDetail');
    Route::post('UpdateActivityDetail', 'Api\ActivityController@UpdateActivityDetail');
    Route::post('DeleteActivityDetail', 'Api\ActivityController@DeleteActivityDetail');

    
    Route::get('GetFormMinatAllAdmin', 'Api\FormMinatController@GetFormMinatAllAdmin');
    Route::get('GetFormMinatAll', 'Api\FormMinatController@GetFormMinatAll');
    Route::get('GetFormMinatDetail', 'Api\FormMinatController@GetFormMinatDetail');
    Route::post('AddFormMinat', 'Api\FormMinatController@AddFormMinat');
    Route::post('UpdateFormMinat', 'Api\FormMinatController@UpdateFormMinat');
    Route::post('DeleteFormMinat', 'Api\FormMinatController@DeleteFormMinat');

    Route::get('GetEmployeeAll', 'Api\EmployeeController@GetEmployeeAll');
    Route::get('GetEmployeebyManager', 'Api\EmployeeController@GetEmployeebyManager');
    Route::get('GetEmployeebyPosition', 'Api\EmployeeController@GetEmployeebyPosition');
    Route::get('GetFFbyUMandFC', 'Api\EmployeeController@GetFFbyUMandFC');
    Route::get('GetEmployeeManagePosition', 'Api\EmployeeController@GetEmployeeManagePosition');
    Route::get('GetJobPosition', 'Api\EmployeeController@GetJobPosition');
    Route::post('EditPositionEmp', 'Api\EmployeeController@EditPositionEmp');
    Route::get('GetEmployeeMenuAccess', 'Api\EmployeeController@GetEmployeeMenuAccess');
    Route::post('EditMenuAccessEmp', 'Api\EmployeeController@EditMenuAccessEmp');
    Route::post('AddEmployee', 'Api\EmployeeController@AddEmployee');
    Route::post('EditEmployee', 'Api\EmployeeController@EditEmployee');
    Route::post('DeleteEmployee', 'Api\EmployeeController@DeleteEmployee');

    Route::get('GetSosisalisasiTanamAdmin', 'Api\SosialisasiTanamController@GetSosisalisasiTanamAdmin');
    Route::get('GetSosisalisasiTanamTimeAll', 'Api\SosialisasiTanamController@GetSosisalisasiTanamTimeAll');
    Route::get('GetSosisalisasiTanamFF', 'Api\SosialisasiTanamController@GetSosisalisasiTanamFF');
    Route::get('GetDetailSosisalisasiTanam', 'Api\SosialisasiTanamController@GetDetailSosisalisasiTanam');
    Route::get('GetDetailSosisalisasiTanamFFNo', 'Api\SosialisasiTanamController@GetDetailSosisalisasiTanamFFNo');
    Route::post('AddSosisalisasiTanam', 'Api\SosialisasiTanamController@AddSosisalisasiTanam');
    Route::post('UpdateSosisalisasiTanam', 'Api\SosialisasiTanamController@UpdateSosisalisasiTanam');
    Route::post('UpdatePohonSosisalisasiTanam', 'Api\SosialisasiTanamController@UpdatePohonSosisalisasiTanam');
    Route::post('SoftDeleteSosisalisasiTanam', 'Api\SosialisasiTanamController@SoftDeleteSosisalisasiTanam');
    Route::post('ValidateSosisalisasiTanam', 'Api\SosialisasiTanamController@ValidateSosisalisasiTanam');

    
    Route::get('GetPlantingHoleAdmin', 'Api\PlantingHoleController@GetPlantingHoleAdmin');
    Route::get('GetPlantingHoleFF', 'Api\PlantingHoleController@GetPlantingHoleFF');
    Route::get('GetPlantingHoleDetail', 'Api\PlantingHoleController@GetPlantingHoleDetail');
    Route::get('GetPlantingHoleDetailFFNo', 'Api\PlantingHoleController@GetPlantingHoleDetailFFNo');
    Route::post('AddPlantingHole', 'Api\PlantingHoleController@AddPlantingHole');
    Route::post('AddPlantingHoleByFFNo', 'Api\PlantingHoleController@AddPlantingHoleByFFNo');
    Route::post('UpdatePlantingHole', 'Api\PlantingHoleController@UpdatePlantingHole');
    Route::post('UpdatePlantingHoleAll', 'Api\PlantingHoleController@UpdatePlantingHoleAll');
    Route::post('UpdatePohonPlantingHole', 'Api\PlantingHoleController@UpdatePohonPlantingHole');
    Route::post('SoftDeletePlantingHole', 'Api\PlantingHoleController@SoftDeletePlantingHole');
    Route::post('ValidatePlantingHole', 'Api\PlantingHoleController@ValidatePlantingHole');

    Route::get('GetMonitoringFF', 'Api\MonitoringController@GetMonitoringFF');
    Route::get('GetMonitoringAdmin', 'Api\MonitoringController@GetMonitoringAdmin');
    Route::get('GetMonitoringDetail', 'Api\MonitoringController@GetMonitoringDetail'); 
    Route::get('GetMonitoringDetailFFNo', 'Api\MonitoringController@GetMonitoringDetailFFNo'); 
    Route::get('GetMonitoringTest', 'Api\MonitoringController@GetMonitoringTest');    
    Route::post('AddMonitoring', 'Api\MonitoringController@AddMonitoring');
    Route::post('UpdateMonitoring', 'Api\MonitoringController@UpdateMonitoring');
    Route::post('UpdatePohonMonitoring', 'Api\MonitoringController@UpdatePohonMonitoring');
    Route::post('SoftDeleteMonitoring', 'Api\MonitoringController@SoftDeleteMonitoring');
    Route::post('ValidateMonitoring', 'Api\MonitoringController@ValidateMonitoring');
    
    Route::get('GetMonitoring2FF', 'Api\MonitoringController@GetMonitoring2FF');
    Route::get('GetMonitoring2Admin', 'Api\MonitoringController@GetMonitoring2Admin');
    Route::get('GetMonitoring2Detail', 'Api\MonitoringController@GetMonitoring2Detail'); 
    Route::get('GetMonitoring2DetailFFNo', 'Api\MonitoringController@GetMonitoring2DetailFFNo'); 
    Route::post('AddMonitoring2', 'Api\MonitoringController@AddMonitoring2');
    Route::post('UpdateMonitoring2', 'Api\MonitoringController@UpdateMonitoring2');
    Route::post('UpdatePohonMonitoring2', 'Api\MonitoringController@UpdatePohonMonitoring2');
    Route::post('SoftDeleteMonitoring2', 'Api\MonitoringController@SoftDeleteMonitoring2');
    Route::post('ValidateMonitoring2', 'Api\MonitoringController@ValidateMonitoring2');

    // Farmer Training Routers
    Route::get('GetFarmerTrainingAll', 'Api\FarmerTrainingController@GetFarmerTrainingAll');
    Route::get('GetFarmerTrainingAllTempDelete', 'Api\FarmerTrainingController@GetFarmerTrainingAllTempDelete');
    Route::post('AddFarmerTraining', 'Api\FarmerTrainingController@AddFarmerTraining');
    Route::post('UpdateFarmerTraining', 'Api\FarmerTrainingController@UpdateFarmerTraining');
    Route::post('AddDetailFarmerTraining', 'Api\FarmerTrainingController@AddDetailFarmerTraining');
    Route::post('UpdateFarmerTraining', 'Api\FarmerTrainingController@UpdateFarmerTraining');
    Route::post('DeleteFarmerTrainingDetail', 'Api\FarmerTrainingController@DeleteFarmerTrainingDetail');
    Route::post('SoftDeleteFarmerTraining', 'Api\FarmerTrainingController@SoftDeleteFarmerTraining');
    Route::post('DeleteFarmerTraining', 'Api\FarmerTrainingController@DeleteFarmerTraining');

});
