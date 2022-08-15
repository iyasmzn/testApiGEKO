<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\Desa;
use App\Kecamatan;
use App\Kabupaten;
use App\Province;
use App\LahanUmum;
use App\LahanUmumDetail;

class LahanUmumController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetLahanUmumAllAdmin",
     *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Lahan Umum All Admin",
     *   operationId="GetLahanUmumAllAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="typegetdata",in="query", type="string"),
     *      @SWG\Parameter(name="pic_lahan",in="query", type="string"),
     *      @SWG\Parameter(name="mu",in="query",  type="string"),
     *      @SWG\Parameter(name="ta",in="query", type="string"),
     *      @SWG\Parameter(name="village",in="query",  type="string"),
     * )
     */
    public function GetLahanUmumAllAdmin(Request $request){
        $typegetdata = $request->typegetdata;
        $pic_lahan = $request->pic_lahan;
        $getmu = $request->mu;
        $getta = $request->ta;
        $getvillage = $request->village;
        if($getmu){$mu='%'.$getmu.'%';}
        else{$mu='%%';}
        if($getta){$ta='%'.$getta.'%';}
        else{$ta='%%';}
        if($getvillage){$village='%'.$getvillage.'%';}
        else{$village='%%';}
        try{
            if($typegetdata == 'all' || $typegetdata == 'several'){
                if($typegetdata == 'all'){
                    $GetLahanUmumAll = DB::table('lahan_umums')->select('lahan_umums.id as idTblLahan','lahan_umums.lahan_no as lahanNo','lahan_umums.longitude','lahan_umums.latitude','lahan_umums.coordinate','lahan_umums.lahan_type',
                    'lahan_umums.pic_lahan as PIC', 'desas.name as namaDesa','users.name as fc','lahan_umums.complete_data', 'lahan_umums.approve', 'lahan_umums.is_dell')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahan_umums.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahan_umums.village')
                    ->where('lahan_umums.is_dell','=',0)
                    ->where('lahan_umums.mu_no','like',$mu)
                    ->where('lahan_umums.target_area','like',$ta)
                    ->where('lahan_umums.village','like',$village)
                    ->get();
                }else{
                    $ffdecode = (explode(",",$pic_lahan));

                    $GetLahanUmumAll = DB::table('lahan_umums')->select('lahan_umums.id as idTblLahan','lahan_umums.lahan_no as lahanNo','lahan_umums.longitude','lahan_umums.latitude','lahan_umums.coordinate',
                    'desas.name as namaDesa','users.name as pic','lahan_umums.complete_data', 'lahan_umums.is_verified', 'lahan_umums.is_dell')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahan_umums.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahan_umums.village')
                    ->where('lahan_umums.is_dell','=',0)
                    ->wherein('lahan_umums.user_id',$ffdecode)
                    ->where('lahan_umums.mu_no','like',$mu)
                    ->where('lahan_umums.target_area','like',$ta)
                    ->where('lahan_umums.village','like',$village)
                    ->get();
                }

                $dataval = [];
                $listval=array();
                foreach ($GetLahanAll as $val) {
                    $status = '';
                    if($val->complete_data==1 && $val->approve==1){
                        $status = 'Sudah Verifikasi';
                    }else if($val->complete_data==1 && $val->approve==0){
                        $status = 'Belum Verifikasi';
                    }else{
                        $status = 'Belum Lengkap';
                    }
                    $dataval = ['idTblLahan'=>$val->idTblLahan,'lahanNo'=>$val->lahanNo, 'location'=>$val->latitude." ".$val->longitude, 'coordinate'=>$val->coordinate, 'pic_lahan'=>$val->picLahan, 'desa' => $val->namaDesa, 'user' => $val->fc, 'status' => $status, 'is_dell' => $val->is_dell];
                    array_push($listval, $dataval);
                }

                if(count($GetLahanUmumAll)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('lahan_umums')
                        ->leftjoin('users', 'users.employee_no', '=', 'lahan_umums.user_id')
                        ->leftjoin('desas', 'desas.kode_desa', '=', 'lahan_umums.village')
                        ->where('lahan_umums.is_dell','=',0)
                        ->where('lahan_umums.mu_no','like',$mu)
                        ->where('lahan_umums.target_area','like',$ta)
                        ->where('lahan_umums.village','like',$village)
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$pic_lahan));
                        
                        $count = DB::table('lahan_umums')
                        ->leftjoin('users', 'users.employee_no', '=', 'lahans.user_id')
                        ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                        ->where('lahans.is_dell','=',0)
                        ->where('lahans.mu_no','like',$mu)
                        ->where('lahans.target_area','like',$ta)
                        ->where('lahans.village','like',$village)
                        ->count();
                    }
                    
                    $data = ['count'=>$count, 'data'=>$listval];
                    $rslt =  $this->ResultReturn(200, 'success', $data);
                    return response()->json($rslt, 200);  
                }
                else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                } 
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function ExportLahanUmumAllAdmin(Request $request)
    {
        $typegetdata = $request->typegetdata;
        $ff = $request->ff;
        $getmu = $request->mu;
        $getta = $request->ta;
        $getvillage = $request->village;
        if($getmu){$mu='%'.$getmu.'%';}
        else{$mu='%%';}
        if($getta){$ta='%'.$getta.'%';}
        else{$ta='%%';}
        if($getvillage){$village='%'.$getvillage.'%';}
        else{$village='%%';}
        
        try{                    
            if($typegetdata == 'all' || $typegetdata == 'several'){
                if($typegetdata == 'all'){
                    $GetLahanAll = DB::table('lahans')->select('lahans.id as idTblLahan','lahans.lahan_no as lahanNo','lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.pohon_kayu','lahans.pohon_mpts','lahans.land_area','lahans.planting_area','kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'farmers.farmer_no as kodePetani', 'farmers.name as namaPetani','desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff','lahans.complete_data', 'lahans.approve', 'lahans.is_dell')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahans.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->where('lahans.is_dell','=',0)
                    ->where('lahans.lahan_no','not like','00_%')
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    ->orderby('lahans.lahan_no','asc')
                    ->get();
                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetLahanAll = DB::table('lahans')->select('lahans.id as idTblLahan','lahans.lahan_no as lahanNo','lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.pohon_kayu','lahans.pohon_mpts','lahans.land_area','lahans.planting_area','kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'farmers.farmer_no as kodePetani', 'farmers.name as namaPetani','desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff','lahans.complete_data', 'lahans.approve', 'lahans.is_dell')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahans.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->where('lahans.is_dell','=',0)
                    ->wherein('lahans.user_id',$ffdecode)
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    ->orderby('lahans.lahan_no','asc')
                    ->get();
                }

                // $getTrees=DB::table('trees')
                //         ->select('tree_name','tree_code')
                //         ->get();

                $dataval = [];
                $listval=array();
                foreach ($GetLahanAll as $val) {
                    $status = '';
                    if($val->complete_data==1 && $val->approve==1){
                        $status = 'Sudah Verifikasi';
                    }else if($val->complete_data==1 && $val->approve==0){
                        $status = 'Belum Verifikasi';
                    }else{
                        $status = 'Belum Lengkap';
                    }

                    // var_dump($val->ff_no);
                    $getFF=DB::table('field_facilitators')
                    ->select('fc_no')
                    ->where('ff_no', '=',$val->ff_no)
                    ->first();
                    // var_dump($getFF);
                    if($getFF){
                        $getFC=DB::table('employees')
                        ->select('name')
                        ->where('nik', '=',$getFF->fc_no)
                        ->first();
                        $nama_fc = $getFC->name;
                    }else{
                        $nama_fc = '-';
                    }

                    // $datavaltrees = [];
                    // $listvaltrees=array();
                    // foreach ($getTrees as $value) {
                    //     $countPohon = 0;

                    //     $getPohon=DB::table('lahan_details')
                    //         ->where('lahan_no', '=',$val->lahanNo)
                    //         ->where('tree_code', '=',$value->tree_code)
                    //         ->first();

                    //     if($getPohon){
                    //         $countPohon = $getPohon->amount;
                    //     }else{
                    //         $countPohon = 0;
                    //     }

                    //     array_push($listvaltrees, $countPohon);
                    // }
                    

                    // var_dump($getFC->name);

                    $dataval = ['idTblLahan'=>$val->idTblLahan,'lahanNo'=>$val->lahanNo, 'location'=>$val->latitude." ".$val->longitude, 'coordinate'=>$val->coordinate,
                    'kodePetani'=>$val->kodePetani, 'petani'=>$val->namaPetani, 'desa' => $val->namaDesa, 'user' => $val->ff, 'status' => $status,
                    'pohon_kayu' => $val->pohon_kayu,'pohon_mpts' => $val->pohon_mpts,'land_area' => $val->land_area,'planting_area' => $val->planting_area, 
                    'ff' => $val->ff,'nama_fc_lahan' => $nama_fc,'nama_kec' => $val->nama_kec,'nama_mu' => $val->nama_mu,'is_dell' => $val->is_dell];
                    array_push($listval, $dataval);
                }

                if(count($GetLahanAll)!=0){ 
                    
                    // $data = ['count'=>$count, 'data'=>$listval];
                    // $rslt =  $this->ResultReturn(200, 'success', $data);
                    // return response()->json($rslt, 200);
                    

                    $nama_title = 'Cetak Excel Data Lahan';  

                    // var_dump($listval);

                    return view('exportlahan', compact('listval', 'nama_title'));
                }
                else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                } 
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        }catch(\Exception $ex){
            return response()->json($ex);
        }        
    }

    public function ExportLahanUmumAllSuperAdmin(Request $request)
    {
        $typegetdata = $request->typegetdata;
        $ff = $request->ff;
        $getmu = $request->mu;
        $getta = $request->ta;
        $getvillage = $request->village;
        if($getmu){$mu='%'.$getmu.'%';}
        else{$mu='%%';}
        if($getta){$ta='%'.$getta.'%';}
        else{$ta='%%';}
        if($getvillage){$village='%'.$getvillage.'%';}
        else{$village='%%';}
        
        try{                    
            if($typegetdata == 'all' || $typegetdata == 'several'){
                if($typegetdata == 'all'){
                    $GetLahanAll = DB::table('lahans')->select('lahans.id as idTblLahan','lahans.lahan_no as lahanNo','lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.pohon_kayu','lahans.pohon_mpts','lahans.land_area','lahans.planting_area','kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'farmers.farmer_no as kodePetani', 'farmers.name as namaPetani','desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff','lahans.complete_data', 'lahans.approve', 'lahans.is_dell')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahans.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->where('lahans.is_dell','=',0)
                    ->where('lahans.lahan_no','not like','00_%')
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    // ->where('lahans.lahan_no','=','10_0000005890')
                    ->orderby('lahans.lahan_no','asc')
                    ->get();
                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetLahanAll = DB::table('lahans')->select('lahans.id as idTblLahan','lahans.lahan_no as lahanNo','lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.pohon_kayu','lahans.pohon_mpts','lahans.land_area','lahans.planting_area','kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'farmers.farmer_no as kodePetani', 'farmers.name as namaPetani','desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff','lahans.complete_data', 'lahans.approve', 'lahans.is_dell')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'lahans.user_id')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->where('lahans.is_dell','=',0)
                    ->wherein('lahans.user_id',$ffdecode)
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    ->orderby('lahans.lahan_no','asc')
                    ->get();
                }

                $getTrees=DB::table('trees')
                        ->select('tree_name','tree_code')
                        ->get();

                $dataval = [];
                $listval=array();
                foreach ($GetLahanAll as $val) {
                    $status = '';
                    if($val->complete_data==1 && $val->approve==1){
                        $status = 'Sudah Verifikasi';
                    }else if($val->complete_data==1 && $val->approve==0){
                        $status = 'Belum Verifikasi';
                    }else{
                        $status = 'Belum Lengkap';
                    }

                    // var_dump($val->ff_no);
                    $getFF=DB::table('field_facilitators')
                    ->select('fc_no')
                    ->where('ff_no', '=',$val->ff_no)
                    ->first();
                    // var_dump($getFF);
                    if($getFF){
                        $getFC=DB::table('employees')
                        ->select('name')
                        ->where('nik', '=',$getFF->fc_no)
                        ->first();
                        $nama_fc = $getFC->name;
                    }else{
                        $nama_fc = '-';
                    }

                    $lahan_details=DB::table('lahan_details')
                            ->select('tree_code')
                            ->where('lahan_no', '=',$val->lahanNo)
                            ->get();

                    $listlhndtl=array();
                    array_push($listlhndtl, 'Nilai0Array');
                    foreach ($lahan_details as $lhndtl) {
                        array_push($listlhndtl, $lhndtl->tree_code);
                    }

                    // print_r($listlhndtl);

                    $datavaltrees = [];
                    $listvaltrees=array();
                    foreach ($getTrees as $value) {
                        $countPohon = 0;

                        $rslt_search = array_search($value->tree_code,$listlhndtl);

                        // var_dump($value->tree_code);
                        // var_dump($rslt_search);
                        // var_dump('---------');
                        
                        if($rslt_search){
                            // var_dump($rslt_search);
                            $getPohonFix=DB::table('lahan_details')
                            ->where('lahan_no', '=',$val->lahanNo)
                            ->where('tree_code', '=',$value->tree_code)
                            ->first();
                            $countPohon = $getPohonFix->amount;
                        }else{
                            $countPohon = 0;
                        }
                        // echo '<br>';

                        array_push($listvaltrees, $countPohon);
                    }
                    

                    // var_dump($getFC->name);

                    $dataval = ['idTblLahan'=>$val->idTblLahan,'lahanNo'=>$val->lahanNo, 'location'=>$val->latitude." ".$val->longitude, 'coordinate'=>$val->coordinate,
                    'kodePetani'=>$val->kodePetani, 'petani'=>$val->namaPetani, 'desa' => $val->namaDesa, 'user' => $val->ff, 'status' => $status,
                    'pohon_kayu' => $val->pohon_kayu,'pohon_mpts' => $val->pohon_mpts,'land_area' => $val->land_area,'planting_area' => $val->planting_area, 
                    'ff' => $val->ff,'nama_fc_lahan' => $nama_fc,'nama_kec' => $val->nama_kec,'nama_mu' => $val->nama_mu,'is_dell' => $val->is_dell,'listvaltrees' => $listvaltrees];
                    array_push($listval, $dataval);
                }

                if(count($GetLahanAll)!=0){ 
                    
                    // $data = ['count'=>$count, 'data'=>$listval];
                    // $rslt =  $this->ResultReturn(200, 'success', $data);
                    // return response()->json($rslt, 200);
                    

                    $nama_title = 'Cetak Excel Data Lahan';  

                    // var_dump($listval);

                    return view('exportlahanSuperAdmin', compact('listval', 'nama_title', 'getTrees'));
                }
                else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                } 
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
        }catch(\Exception $ex){
            return response()->json($ex);
        }        
    }

    /**
     * @SWG\Get(
     *   path="/api/GetLahanUmumAll",
     *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Lahan Umum All",
     *   operationId="GetLahanUmumAll",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     *      @SWG\Parameter(name="pic_lahan",in="query", type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetLahanUmumAll(Request $request){
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        $getfarmerno = $request->pic_lahan;
        if($getfarmerno){$pic_lahan='%'.$getfarmerno.'%';}
        else{$pic_lahan='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            if($pic_lahan!='%%'){
                $GetLahanUmumAll = LahanUmum::where('user_id', '=', $request->user_id)->where('pic_lahan','like',$pic_lahan)->where('is_dell', '=', 0)->orderBy('id', 'ASC')->get();
            }else{
                $GetLahanUmumAll = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->orderBy('id', 'ASC')->get();
            }
            if(count($GetLahanAll)!=0){
                
                if($pic_lahan!='%%'){
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('pic_lahan','like',$pic_lahan)->where('is_dell', '=', 0)->count();
                }else{
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->count();
                }
                $data = ['count'=>$count, 'data'=>$GetLahanUmumAll];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetLahanUmumNotComplete",
     *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Lahan Umum Not Complete",
     *   operationId="GetLahanUmumNotComplete",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetLahanUmumNotComplete(Request $request){
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        $getfarmerno = $request->farmer_no;
        if($getfarmerno){$farmer_no='%'.$getfarmerno.'%';}
        else{$farmer_no='%%';}
        try{
            // var_dump(count($GetLahanNotComplete));
            if($farmer_no!='%%'){
                $GetLahanNotComplete = LahanUmum::where('user_id', '=', $request->user_id)->where('farmer_no','like',$farmer_no)->where('is_dell', '=', 0)->where('complete_data', '=', 0)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }else{
                $GetLahanNotComplete = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 0)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }
            if(count($GetLahanNotComplete)!=0){
                
                if($farmer_no!='%%'){
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('farmer_no','like',$farmer_no)->where('is_dell', '=', 0)->where('complete_data', '=', 0)->count();
                }else{
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 0)->count();
                }
                $data = ['count'=>$count, 'data'=>$GetLahanNotComplete];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetLahanUmumCompleteNotApprove",
     *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Lahan Umum Complete Not Approve",
     *   operationId="GetLahanUmumCompleteNotApprove",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true,  type="string"),
     *      @SWG\Parameter(name="farmer_no",in="query", type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetLahanCompleteNotApprove(Request $request){
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        $getfarmerno = $request->farmer_no;
        if($getfarmerno){$farmer_no='%'.$getfarmerno.'%';}
        else{$farmer_no='%%';}
        try{            
            if($farmer_no!='%%'){
                $GetLahanCompleteNotApprove = Lahan::where('user_id', '=', $request->user_id)->where('farmer_no', 'Like', $farmer_no)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('approve', '=', 0)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }else{
                $GetLahanCompleteNotApprove = Lahan::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('approve', '=', 0)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }
            if(count($GetLahanCompleteNotApprove)!=0){
                
                if($farmer_no!='%%'){
                    $count = Lahan::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('farmer_no', 'Like', $farmer_no)->where('complete_data', '=', 1)->where('approve', '=', 0)->count();
                }else{
                    $count = Lahan::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('approve', '=', 0)->count();
                }
                $data = ['count'=>$count, 'data'=>$GetLahanCompleteNotApprove];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
    /**
     * @SWG\Get(
     *   path="/api/GetCompleteAndApproveUmum",
     *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Complete And Approve Umum",
     *   operationId="GetCompleteAndApproveUmum",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true,  type="string"),
     *      @SWG\Parameter(name="farmer_no",in="query", type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetCompleteAndApproveUmum(Request $request){
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        $getfarmerno = $request->pic_lahan;
        if($getfarmerno){$pic_lahan='%'.$getfarmerno.'%';}
        else{$pic_lahan='%%';}
        try{
            
            if($farmer_no!='%%'){
                $GetCompleteAndApprove = LahanUmum::where('user_id', '=', $request->user_id)->where('pic_lahan', 'Like', $pic_lahan)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('is_verified', '=', 1)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }else{
                $GetCompleteAndApprove = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('approve', '=', 1)->orderBy('id', 'ASC')->limit($limit)->offset($offset)->get();
            }
            if(count($GetCompleteAndApprove)!=0){
                
                if($farmer_no!='%%'){
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('pic_lahan', 'Like', $pic_lahan)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('is_verified', '=', 1)->count();
                }else{
                    $count = LahanUmum::where('user_id', '=', $request->user_id)->where('is_dell', '=', 0)->where('complete_data', '=', 1)->where('approve', '=', 1)->count();
                }
                $data = ['count'=>$count, 'data'=>$GetCompleteAndApprove];
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200);  
            }
            else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            } 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }


    /**
     * @SWG\Post(
     *   path="/api/AddMandatoryLahanUmum",
	 *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Mandatory Lahan Umum",
     *   operationId="AddMandatoryLahanUmum",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Mandatory Lahan Umum",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="luas_lahan", type="string", example="8200.00"),
     *              @SWG\Property(property="longitude", type="date", example="110.3300613"),
     *              @SWG\Property(property="latitude", type="string", example="-7.580778"),
     *              @SWG\Property(property="village", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="mu_no", type="string", example="025"),
     *              @SWG\Property(property="target_area", type="string", example="025001"),
     *              @SWG\Property(property="active", type="int", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="U0002")
     *          ),
     *      )
     * )
     *
     */
    public function AddMandatoryLahanUmum(Request $request){
        try{
            $validator = Validator::make($request->all(), [                
                'luas_lahan' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'village' => 'required|max:255',
                'target_area' => 'required|max:255',
                'mu_no' => 'required|max:255',
                'active' => 'required|max:1',
                'user_id' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $coordinate = $this->getCordinate($request->longitude, $request->latitude);

            $getLastIdLahan = LahanUmum::orderBy('lahan_no','desc')->first(); 
            if($getLastIdLahan){
                $lahan_no = '11_'.str_pad(((int)substr($getLastIdLahan->lahan_no,-10) + 1), 10, '0', STR_PAD_LEFT);
            }else{
                $lahan_no = '11_0000000001';
            }

            $getDesa = Desa::select('kode_desa','name','kode_kecamatan')->where('kode_desa','=',$request->village)->first(); 
            // $getKec = Kecamatan::select('kode_kecamatan','name','kabupaten_no')->where('kode_kecamatan','=',$getDesa->kode_kecamatan)->first(); 
            // $getKab = Kabupaten::select('kabupaten_no','name','province_code')->where('kabupaten_no','=',$getKec->kabupaten_no)->first(); 
            // $getProv = Province::select('province_code','name')->where('province_code','=',$getKab->province_code)->first();
            
            $description = $this->ReplaceNull($request->description, 'string');
            // $photo1 = $this->ReplaceNull($request->photo1, 'string');
            $access_lahan = $this->ReplaceNull($request->access_lahan, 'string');
            $jarak_lahan = $this->ReplaceNull($request->jarak_lahan, 'string');

            $complete_data = 0;
            if($description != "-" && $photo1 != "-" && $access_lahan != "-" && $jarak_lahan != "-")
            {
                $complete_data = 1;
            }

            LahanUmum::create([
                'lahan_no' => $lahan_no,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'village' => $request->village,
                'pic_lahan' => $request->pic_lahan,
                'ktp_no' => $request->ktp_no,
                'address' => $request->address,
                'mou_no' => $request->mou_no,
                'luas_lahan' => $request->luas_lahan,
                'luas_tanam' => $request->luas_tanam,
                
                'planting_pattern' => $this->ReplaceNull($request->planting_pattern, 'string'),
                'access_lahan' => $access_lahan,
                'jarak_lahan' => $jarak_lahan,
                
                'status' => $request->status,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'distribution_date' => $request->distribution_date,
                'user_id' => $request->user_id,
                'complete_data' =>$complete_data,
                'is_verified' => '0',
                'verified_by' => '-',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
                
                'photo1' => $this->ReplaceNull($request->photo1, 'string'),
                'photo2' => $this->ReplaceNull($request->photo2, 'string'),
                'photo3' => $this->ReplaceNull($request->photo3, 'string'),
                'photo4' => $this->ReplaceNull($request->photo4, 'string'),
                
                'active' => $request->active,
                'coordinate' => $coordinate,
                'tutupan_lahan' => $this->ReplaceNull($request->tutupan_lahan, 'string'),

                'is_dell' => 0,
                'description' => $description
            ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddMandatoryLahanUmumComplete",
	 *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Mandatory Lahan Umum Complete",
     *   operationId="AddMandatoryLahanUmumComplete",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Mandatory Lahan Umum Complete",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="lahan_no", type="string", example="10_000000002"),
     *              @SWG\Property(property="luas_lahan", type="string", example="8200.00"),
     *              @SWG\Property(property="longitude", type="date", example="110.3300613"),
     *              @SWG\Property(property="latitude", type="string", example="-7.580778"),
     *              @SWG\Property(property="village", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="mu_no", type="string", example="025"),
     *              @SWG\Property(property="target_area", type="string", example="025001"),
     *              @SWG\Property(property="active", type="int", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="U0002")
     *          ),
     *      )
     * )
     *
     */
    public function AddMandatoryLahanUmumComplete(Request $request){
        try{
            $validator = Validator::make($request->all(), [                
                'lahan_no' => 'required|max:255|unique:lahan_umums',
                'luas_lahan' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'village' => 'required',
                'target_area' => 'required',
                'mu_no' => 'required',
                'active' => 'required|max:1',
                'pic_lahan' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $coordinate = $this->getCordinate($request->longitude, $request->latitude);

            // $getLastIdLahan = Lahan::orderBy('lahan_no','desc')->first(); 
            // if($getLastIdLahan){
            //     $lahan_no = '10_'.str_pad(((int)substr($getLastIdLahan->lahan_no,-10) + 1), 10, '0', STR_PAD_LEFT);
            // }else{
            //     $lahan_no = '10_0000000001';
            // }

            $getDesa = Desa::select('kode_desa','name','kode_kecamatan')->where('kode_desa','=',$request->village)->first(); 
            // $getKec = Kecamatan::select('kode_kecamatan','name','kabupaten_no')->where('kode_kecamatan','=',$getDesa->kode_kecamatan)->first(); 
            // $getKab = Kabupaten::select('kabupaten_no','name','province_code')->where('kabupaten_no','=',$getKec->kabupaten_no)->first(); 
            // $getProv = Province::select('province_code','name')->where('province_code','=',$getKab->province_code)->first();
            
            // var_dump('test');
            // $photo1 = $this->ReplaceNull($request->photo1, 'string');

            $description = $this->ReplaceNull($request->description, 'string');
            // $photo1 = $this->ReplaceNull($request->photo1, 'string');
            $access_lahan = $this->ReplaceNull($request->access_lahan, 'string');
            $jarak_lahan = $this->ReplaceNull($request->jarak_lahan, 'string');

            $complete_data = 0;
            if($description != "-" && $photo1 != "-" && $access_lahan != "-" && $jarak_lahan != "-")
            {
                $complete_data = 1;
            }

            // var_dump($request->lahan_no);
            LahanUmum::create([
                'lahan_no' => $request->lahan_no,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'village' => $request->village,
                'pic_lahan' => $request->pic_lahan,
                'ktp_no' => $request->ktp_no,
                'address' => $request->address,
                'mou_no' => $request->mou_no,
                'luas_lahan' => $request->luas_lahan,
                'luas_tanam' => $request->luas_tanam,
                
                'planting_pattern' => $this->ReplaceNull($request->planting_pattern, 'string'),
                'access_lahan' => $access_lahan,
                'jarak_lahan' => $jarak_lahan,
                
                'status' => $request->status,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'distribution_date' => $request->distribution_date,
                'user_id' => $request->user_id,
                'complete_data' =>$complete_data,
                'is_verified' => '0',
                'verified_by' => '-',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
                
                'photo1' => $this->ReplaceNull($request->photo1, 'string'),
                'photo2' => $this->ReplaceNull($request->photo2, 'string'),
                'photo3' => $this->ReplaceNull($request->photo3, 'string'),
                'photo4' => $this->ReplaceNull($request->photo4, 'string'),
                
                'active' => $request->active,
                'coordinate' => $coordinate,
                'tutupan_lahan' => $this->ReplaceNull($request->tutupan_lahan, 'string'),

                'is_dell' => 0,
                'description' => $description
            ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/VerificationLahanUmum",
	 *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Verification Lahan Umum",
     *   operationId="VerificationLahanUmum",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Verification Lahan",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="2")
     *          ),
     *      )
     * )
     *
     */
    public function VerificationLahanUmum(Request $request){
        try{
            $validator = Validator::make($request->all(), [    
                'id' => 'required'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            LahanUmum::where('id', '=', $request->id)
                    ->update
                    ([
                        'complete_data' => 1,
                        'approve' => 1,
                    ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/AddMandatoryLahanUmumBarcode",
	 *   tags={"Lahan"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Mandatory Lahan Umum Barcode",
     *   operationId="AddMandatoryLahanUmumBarcode",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Mandatory Lahan Umum Barcode",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="U0001"),
     *              @SWG\Property(property="barcode", type="string", example="L0000001"),
     *              @SWG\Property(property="longitude", type="string", example="110.3300613"),
     *              @SWG\Property(property="latitude", type="string", example="-7.580778")
     *          ),
     *      )
     * )
     *
     */
    public function AddMandatoryLahanUmumBarcode(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required', 
                'lahan_no' => 'required|max:255|unique:lahans',
                'longitude' => 'required|max:255',
                'latitude' => 'required|max:255'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $coordinate = $this->getCordinate($request->longitude, $request->latitude);

            $kec= '-';
            $city= '-';
            $province= '-';

            if($request->village){
                $getDesa = Desa::select('kode_desa','name','kode_kecamatan')->where('kode_desa','=',$request->village)->first(); 
                $getKec = Kecamatan::select('kode_kecamatan','name','kabupaten_no')->where('kode_kecamatan','=',$getDesa->kode_kecamatan)->first(); 
                $getKab = Kabupaten::select('kabupaten_no','name','province_code')->where('kabupaten_no','=',$getKec->kabupaten_no)->first(); 
                $getProv = Province::select('province_code','name')->where('province_code','=',$getKab->province_code)->first();
            
                $kec = $getKec->kode_kecamatan;
                $city = $getKab->kabupaten_no;
                $province = $getProv->province_code; 
            }
            


            $codeempintern = '';
            $internal_code = '-';
            if($request->type_sppt){
                if($request->type_sppt == 1 || $request->type_sppt == 2 || $request->type_sppt == 3){

                    $tt = $getDesa->kode_desa;
                    $year = Carbon::now()->format('Y');
                    $mu = $request->mu_no;
                    $str = $request->barcode;
                    $barcodestring = substr($str,3, 13) ;

                    $internal_code = $tt.$year.$mu.$barcodestring;

                }
            }

            LahanUmum::create([
                'lahan_no' => $request->barcode,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'coordinate' => $coordinate,
                'user_id' => $request->user_id,

                'luas_lahan' => $this->ReplaceNull($request->land_area, 'int'),                
                'village' => $this->ReplaceNull($request->village, 'string'),
                'mu_no' => $this->ReplaceNull($request->mu_no, 'string'),
                'target_area' => $this->ReplaceNull($request->target_area, 'string'),
                'active' => $this->ReplaceNull($request->active, 'int'),
                // 'farmer_no' => $this->ReplaceNull($request->farmer_no, 'string'),                
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),

                'tutupan_lahan' => $this->ReplaceNull($request->tutupan_lahan, 'string'),
                'photo1' => $this->ReplaceNull($request->photo1, 'string'),
                'photo2' => $this->ReplaceNull($request->photo2, 'string'),
                'photo3' => $this->ReplaceNull($request->photo3, 'string'),
                'photo4' => $this->ReplaceNull($request->photo4, 'string'),
                'group_no' => $this->ReplaceNull($request->group_no, 'string'),
                
                // 'access_to_water_sources' => $access_to_water_sources,
                // 'access_to_lahan' => $access_to_lahan,
                
                'pattern_planting' => $this->ReplaceNull($request->opsi_pola_tanam, 'string'),
                
                'is_dell' => 0
            ]);

            $luas_lahan = $this->ReplaceNull($request->luas_lahan, 'int');
            $village = $this->ReplaceNull($request->village, 'string');
            $mu_no = $this->ReplaceNull($request->mu_no, 'string');
            $target_area = $this->ReplaceNull($request->target_area, 'string');

            $photo1 = $this->ReplaceNull($request->photo1, 'string');
            $photo2 = $this->ReplaceNull($request->photo2, 'string');
            $photo3 = $this->ReplaceNull($request->photo3, 'string');
            $photo4 = $this->ReplaceNull($request->photo4, 'string');

            $luas_tanam = $this->ReplaceNull($request->luas_tanam, 'int');
            $tutupan_lahan = $this->ReplaceNull($request->tutupan_lahan, 'string');
            
            if($document_no != "-" &&$luas_lahan != 0 &&$village != "-" &&$mu_no != "-" &&$target_area != "-" )
            {
                LahanUmum::where('lahan_no', '=', $request->lahan_no)
                ->update
                (['complete_data' => 1]);
            }
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }



    /**
     * @SWG\Post(
     *   path="/api/UpdateLahanUmum",
	 *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Lahan Umum",
     *   operationId="UpdateLahanUmum",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Lahan Umum",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="lahan_no", type="integer", example="L00000001"),
     *              @SWG\Property(property="document_no", type="string", example="0909090909"),
     *              @SWG\Property(property="type_sppt", type="integer", example=1),
     *              @SWG\Property(property="land_area", type="string", example="8200.00"),
     *              @SWG\Property(property="longitude", type="date", example="110.3300613"),
     *              @SWG\Property(property="latitude", type="string", example="-7.580778"),
     *              @SWG\Property(property="coordinate", type="string", example="S734.847E11019.935"),
     *              @SWG\Property(property="village", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="mu_no", type="string", example="025"),
     *              @SWG\Property(property="target_area", type="string", example="025001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F00000001"),
     *               @SWG\Property(property="farmer_temp", type="string", example="Nullable"),
     *              @SWG\Property(property="fertilizer", type="string", example="Nullable"),   
     *              @SWG\Property(property="pesticide", type="string", example="Nullable"),
     *              @SWG\Property(property="sppt", type="string", example="Nullable"),
     *              @SWG\Property(property="description", type="string", example="Nullable"),
     *              @SWG\Property(property="photo1", type="string", example="Nullable"),
     *              @SWG\Property(property="photo2", type="string", example="Nullable"),
     *              @SWG\Property(property="photo3", type="string", example="Nullable"),
     *              @SWG\Property(property="photo4", type="string", example="Nullable"),
     *              @SWG\Property(property="group_no", type="string", example="Nullable"),
     *              @SWG\Property(property="planting_area", type="string", example="Nullable"),
     *              @SWG\Property(property="polygon", type="string", example="Nullable"),
     *              @SWG\Property(property="elevation", type="string", example="Nullable"),
     *              @SWG\Property(property="soil_type", type="string", example="Nullable"),
     *              @SWG\Property(property="current_crops", type="string", example="Nullable"),
     *              @SWG\Property(property="tutupan_lahan", type="string", example="Nullable"),
     *              @SWG\Property(property="kelerengan_lahan", type="string", example="Nullable"),
     *              @SWG\Property(property="access_to_water_sources", type="string", example="Nullable"),
     *              @SWG\Property(property="access_to_lahan", type="string", example="Nullable"), 
     *              @SWG\Property(property="water_availability", type="string", example="Nullable"),
     *              @SWG\Property(property="lahan_type", type="string", example="Nullable"),
     *              @SWG\Property(property="potency", type="string", example="Nullable"),
     *              @SWG\Property(property="jarak_lahan", type="string", example="Nullable"),
     *              @SWG\Property(property="exposure", type="string", example="Nullable"),  
     *              @SWG\Property(property="opsi_pola_tanam", type="string", example="Nullable"), 
     *              @SWG\Property(property="pohon_kayu", type="string", example="Nullable"), 
     *              @SWG\Property(property="pohon_mpts", type="string", example="Nullable"), 
     *              @SWG\Property(property="active", type="int", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="U0002")
     * 
     *          ),
     *      )
     * )
     *
     */
    public function UpdateLahanUmum(Request $request){
        try{
            $validator = Validator::make($request->all(), [    
                'lahan_no' => 'required',            
                'luas_tanah' => 'required|max:255',
                'longitude' => 'required|max:255',
                'latitude' => 'required|max:255',
                'village' => 'required|max:255',
                'target_area' => 'required|max:255',
                'mu_no' => 'required|max:255',
                'active' => 'required|max:1',
                'pic_lahan' => 'required|max:11',
                'user_id' => 'required|max:11',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            // $getLastIdLahan = Lahan::orderBy('lahan_no','desc')->first(); 
            // if($getLastIdLahan){
            //     $lahan_no = 'L'.str_pad(((int)substr($getLastIdLahan->lahan_no,-8) + 1), 8, '0', STR_PAD_LEFT);
            // }else{
            //     $lahan_no = 'L00000001';
            // }
            // if($request->type_sppt){}
            // else{

            // }

            $getDesa = Desa::select('kode_desa','name','kode_kecamatan')->where('kode_desa','=',$request->village)->first(); 
            // $getKec = Kecamatan::select('kode_kecamatan','name','kabupaten_no')->where('kode_kecamatan','=',$getDesa->kode_kecamatan)->first(); 
            // $getKab = Kabupaten::select('kabupaten_no','name','province_code')->where('kabupaten_no','=',$getKec->kabupaten_no)->first(); 
            // $getProv = Province::select('province_code','name')->where('province_code','=',$getKab->province_code)->first();
            
            // $codeempintern = '';
            // $internal_code = '-';
            // if($request->type_sppt == 1){
            //     if($request->user_id){
            //         $getUser = User::select('employee_no','role','name')->where('employee_no','=',$request->user_id)->first(); 
            //         if($getUser->role == 'ff'){
            //             $getUserFF = DB::table('field_facilitators')->where('ff_no','=',$getUser->employee_no)->first();
            //             $codeempintern = $getUserFF->fc_no;
            //         }else{
            //             $codeempintern = $getUser->employee_no;
            //         }
            //     }
            //     $ss = substr($codeempintern,0,2);
            //     $tt = str_replace(".","",$getDesa->kode_desa);
            //     $getLastInternalCodeLahan = Lahan::orderBy('lahan_no','desc')->first(); 
            //     if($getLastInternalCodeLahan){
            //         $internal_code = $ss.$tt.str_pad(((int)substr($getLastInternalCodeLahan->internal_code,-4) + 1), 4, '0', STR_PAD_LEFT);
            //     }else{
            //         $internal_code = $ss.$tt.'00001';
            //     }
            // }

            $coordinate = $this->getCordinate($request->longitude, $request->latitude);

            // $codeempintern = '';
            // $internal_code = '-';
            // if($request->type_sppt != 0){

            //     $tt = $getDesa->kode_desa;
            //     $year = Carbon::now()->format('Y');
            //     $mu = $request->mu_no;
            //     $str = $request->lahan_no;
            //     $barcodestring = substr($str,3, 13) ;

            //     $internal_code = $tt.$year.$mu.$barcodestring;

            // }

            $photo1 = $this->ReplaceNull($request->photo1, 'string');
            $photo2 = $this->ReplaceNull($request->photo2, 'string');
            $photo3 = $this->ReplaceNull($request->photo3, 'string');
            $photo4 = $this->ReplaceNull($request->photo4, 'string');
            $luas_tanam = $this->ReplaceNull($request->luas_tanam, 'int');
            $tutupan_lahan = $this->ReplaceNull($request->tutupan_lahan, 'string');
            
            LahanUmum::where('lahan_no', '=', $request->lahan_no)
            ->update([
                'luas_lahan' => $request->luas_lahan,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'coordinate' => $coordinate,
                'village' => $request->village,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'active' => $request->active,
                'pic_lahan' => $request->pic_lahan,
                'user_id' => $request->user_id,

                'updated_at'=>Carbon::now(),

                'luas_tanam' => $luas_tanam,
                'tutupan_lahan' => $tutupan_lahan,
                
                'photo1' => $photo1,
                'photo2' => $photo2,
                'photo3' => $photo3,
                'photo4' => $photo4,

                'is_dell' => 0
            ]);
            // var_dump('-');
            // $getUserIdLahan = Lahan::where('id','=',$request->id)->first();
            // if($request->user_id == $getUserIdLahan->user_id ){}
            if($photo3 != "-"  && $photo4 != "-")
            {
                Lahan::where('lahan_no', '=', $request->lahan_no)
                ->update
                (['complete_data' => 1]);
            }

            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/SoftDeleteLahanUmum",
	 *   tags={"LahanUmum"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Soft Delete Lahan Umum",
     *   operationId="SoftDeleteLahanUmum",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Soft Delete Lahan",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="2")
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeleteLahan(Request $request){
        try{
            $validator = Validator::make($request->all(), [    
                'id' => 'required'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            LahanUmum::where('id', '=', $request->id)
                    ->update
                    ([
                        'is_dell' => 1
                    ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
}
