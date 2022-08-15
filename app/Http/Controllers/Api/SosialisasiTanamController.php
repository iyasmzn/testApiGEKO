<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\PlantingSocializations;
use App\PlantingSocializationsDetails;
use App\PlantingSocializationsPeriod;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SosialisasiTanamController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetSosisalisasiTanamAdmin",
     *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Sosisalisasi Tanam Admin",
     *   operationId="GetSosisalisasiTanamAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="typegetdata",in="query",required=true, type="string"),
     *      @SWG\Parameter(name="ff",in="query",required=true, type="string"),
     * )
     */
    public function GetSosisalisasiTanamAdmin(Request $request){
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
                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at', 'farmers.name as nama_petani', 'users.name as nama_ff')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                    ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                    ->where('planting_socializations.is_dell','=',0)                    
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    ->get();
                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at', 'farmers.name as nama_petani', 'users.name as nama_ff')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                    ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                    ->where('planting_socializations.is_dell','=',0)
                    ->wherein('planting_socializations.ff_no',$ffdecode)
                    ->get(); 
                }

                $dataval = [];
                $listval=array();
                foreach ($GetSosialisasiAll as $val) {
                    $status = '';
                    if($val->validation==0){
                        $status = 'Belum Verifikasi';
                    }else{
                        $status = 'Sudah Verifikasi';
                    }
                    $dataval = ['id'=>$val->id,'no_lahan'=>$val->no_lahan, 'farmer_no'=>$val->farmer_no, 'form_no'=>$val->form_no,
                    'planting_year'=>$val->planting_year, 'no_document'=>$val->no_document, 'ff_no' => $val->ff_no, 'validation' => $val->validation, 
                    'nama_ff'=>$val->nama_ff,'validate_by'=>$val->validate_by,  'nama_petani'=>$val->nama_petani, 'status' => $status, 'is_dell' => $val->is_dell, 'created_at' => $val->created_at];
                    array_push($listval, $dataval);
                }

                if(count($GetSosialisasiAll)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('planting_socializations')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                        ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                        ->where('planting_socializations.is_dell','=',0)                  
                        ->where('lahans.mu_no','like',$mu)
                        ->where('lahans.target_area','like',$ta)
                        ->where('lahans.village','like',$village)
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$ff));
                        
                        $count = DB::table('planting_socializations')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                        ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                        ->where('planting_socializations.is_dell','=',0)
                        ->wherein('planting_socializations.ff_no',$ffdecode)
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
    public function GetSosisalisasiTanamTimeAll(Request $request){
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
                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no',
                    'planting_period.distribution_time','planting_period.distribution_location')
                    ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                    ->where('planting_socializations.is_dell','=',0)               
                    ->orderBy('planting_period.distribution_time', 'asc')
                    ->get();
                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no',
                    'planting_period.distribution_time','planting_period.distribution_location')
                    ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                    ->where('planting_socializations.is_dell','=',0)
                    ->wherein('planting_socializations.ff_no',$ffdecode)
                    ->orderBy('planting_period.distribution_time', 'asc')
                    ->get(); 
                }

                if(count($GetSosialisasiAll)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('planting_socializations')
                        ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                        ->where('planting_socializations.is_dell','=',0)      
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$ff));
                        
                        $count = DB::table('planting_socializations')
                        ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                        ->where('planting_socializations.is_dell','=',0)
                        ->wherein('planting_socializations.ff_no',$ffdecode)
                        ->count();
                    }
                    
                    $data = ['count'=>$count, 'data'=>$GetSosialisasiAll];
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

    /**
     * @SWG\Get(
     *   path="/api/GetSosisalisasiTanamFF",
     *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Sosisalisasi FF",
     *   operationId="GetSosisalisasiTanamFF",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ff_no",in="query", required=true, type="string"),
     * )
     */
    public function GetSosisalisasiTanamFF(Request $request){
        $ff_no = $request->ff_no;
        // $getmu = $request->mu;
        // $getta = $request->ta;
        // $getvillage = $request->village;
        // if($getmu){$mu='%'.$getmu.'%';}
        // else{$mu='%%';}
        // if($getta){$ta='%'.$getta.'%';}
        // else{$ta='%%';}
        // if($getvillage){$village='%'.$getvillage.'%';}
        // else{$village='%%';}
        try{
           
                $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at', 'farmers.name as nama_petani', 'users.name as nama_ff')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                    ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                    ->where('planting_socializations.is_dell','=',0)
                    ->where('planting_socializations.ff_no','=',$ff_no)
                    ->get();

                if(count($GetSosialisasiAll)!=0){ 
                    $count = DB::table('planting_socializations')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                        ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                        ->where('planting_socializations.is_dell','=',0)
                        ->where('planting_socializations.ff_no','=',$ff_no)
                        ->count();
                    
                    $data = ['count'=>$count, 'data'=>$GetSosialisasiAll];
                    $rslt =  $this->ResultReturn(200, 'success', $data);
                    return response()->json($rslt, 200); 
                }else{
                    $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                    return response()->json($rslt, 404);
                }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function ExportSostamAllSuperAdmin(Request $request)
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
                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select('lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.land_area','lahans.planting_area','lahans.opsi_pola_tanam',
                    'kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff',

                    'planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at', 'farmers.name as nama_petani', 'users.name as nama_ff',
                    'planting_period.pembuatan_lubang_tanam','planting_period.distribution_time','planting_period.distribution_location','planting_period.planting_time')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                    ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                    ->where('planting_socializations.is_dell','=',0)                    
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    // ->where('planting_socializations.form_no','=','SO-2021-0000000477')
                    ->get();

                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetSosialisasiAll = DB::table('planting_socializations')
                    ->select(
                    'lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.land_area','lahans.planting_area','lahans.opsi_pola_tanam',
                    'kecamatans.name as nama_kec','managementunits.name as nama_mu',
                    'desas.name as namaDesa','lahans.user_id as ff_no','users.name as ff',

                    'planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at', 'farmers.name as nama_petani', 'users.name as nama_ff',
                    'planting_period.pembuatan_lubang_tanam','planting_period.distribution_time','planting_period.distribution_location','planting_period.planting_time')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'planting_socializations.farmer_no')
                    ->leftjoin('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'lahans.village')
                    ->leftjoin('kecamatans', 'kecamatans.kode_kecamatan', '=', 'lahans.kecamatan')
                    ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'lahans.mu_no')
                    ->leftjoin('users', 'users.employee_no', '=', 'planting_socializations.ff_no')
                    ->where('planting_socializations.is_dell','=',0)
                    ->wherein('planting_socializations.ff_no',$ffdecode)
                    ->get(); 

                    
                }

                $getTrees=DB::table('trees')
                        ->select('tree_name','tree_code')
                        ->get();

                $dataval = [];
                $listval=array();

                // var_dump($GetSosialisasiAll);

                foreach ($GetSosialisasiAll as $val) {
                    $status = '';
                    if($val->validation==1){
                        $status = 'Sudah Verifikasi';                    
                    }else{
                        $status = 'Belum Verifikasi';
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
                    
                    // var_dump($getFF);

                    $lahan_details=DB::table('planting_details')
                            ->select('tree_code','amount')
                            ->where('form_no', '=',$val->form_no)
                            ->get();
                    
                    // var_dump($lahan_details);

                    $listlhndtl=array();
                    array_push($listlhndtl, 'Nilai0Array');
                    $pohon_kayu = 0;
                    $pohon_mpts = 0;
                    $pohon_crops = 0;
                    $other = 0;
                    foreach ($lahan_details as $lhndtl) {
                        array_push($listlhndtl, $lhndtl->tree_code);

                        $getTreesCode=DB::table('trees')
                            ->select('tree_category','tree_code')
                            ->where('tree_code', '=',$lhndtl->tree_code)
                            ->first();

                            
                        
                        if($getTreesCode->tree_category == 'Pohon_Kayu'){
                            $pohon_kayu = $pohon_kayu + $lhndtl->amount;
                        }else if ($getTreesCode->tree_category == 'Pohon_Buah'){
                            $pohon_mpts = $pohon_mpts + $lhndtl->amount;
                        }else if ($getTreesCode->tree_category == 'Tanaman_Bawah_Empon'){
                            $pohon_crops = $pohon_crops + $lhndtl->amount;
                        }else{
                            $other = $other + $lhndtl->amount;
                        }

                        // var_dump($pohon_kayu);
                    }

                    // print_r($listlhndtl);

                    $datavaltrees = [];
                    $listvaltrees=array();
                    foreach ($getTrees as $value) {
                        $countPohon = 0;

                        $rslt_search = array_search($value->tree_code,$listlhndtl);
                        
                        if($rslt_search){
                            // var_dump($rslt_search);
                            $getPohonFix=DB::table('planting_details')
                            ->where('form_no', '=',$val->form_no)
                            ->where('tree_code', '=',$value->tree_code)
                            ->first();
                            $countPohon = $getPohonFix->amount;
                        }else{
                            $countPohon = 0;
                        }
                        // echo '<br>';

                        array_push($listvaltrees, $countPohon);
                    }

                    // var_dump($listvaltrees);
                    

                    // var_dump($getFC->name);
                    // var_dump('test');

                    $dataval = ['form_no'=>$val->form_no,'lahanNo'=>$val->no_lahan, 'location'=>$val->latitude." ".$val->longitude, 'coordinate'=>$val->coordinate,
                    'kodePetani'=>$val->farmer_no, 'petani'=>$val->nama_petani, 'desa' => $val->namaDesa, 'user' => $val->ff, 'status' => $status,
                    'pohon_kayu' => $pohon_kayu,'pohon_mpts' => $pohon_mpts,'pohon_crops' => $pohon_crops,'land_area' => $val->land_area,'planting_area' => $val->planting_area, 
                    'ff' => $val->ff,'nama_fc_lahan' => $nama_fc,'nama_kec' => $val->nama_kec,'nama_mu' => $val->nama_mu,
                    'form_no'=>$val->form_no,'planting_year' => $val->planting_year,'pembuatan_lubang_tanam' => $this->tanggal_indo($val->pembuatan_lubang_tanam),
                    'distribution_time' => $this->tanggal_indo($val->distribution_time),'distribution_location' => $val->distribution_location,'planting_time' => $this->tanggal_indo($val->planting_time),
                    'opsi_pola_tanam'=>$val->opsi_pola_tanam,'listvaltrees' => $listvaltrees];
                    array_push($listval, $dataval);

                    // var_dump($dataval);
                }

                

                if(count($GetSosialisasiAll)!=0){ 

                    $nama_title = 'Cetak Excel Data Sosialisasi Tanam'; 
                    // print_r($listval); 

                    return view('exportSostamSuperAdmin', compact('listval', 'nama_title', 'getTrees'));
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

    public function tanggal_indo($tanggal)
    {
        // $bulan = array (1 =>   'Januari',
        //             'Februari',
        //             'Maret',
        //             'April',
        //             'Mei',
        //             'Juni',
        //             'Juli',
        //             'Agustus',
        //             'September',
        //             'Oktober',
        //             'November',
        //             'Desember'
        //         );
        // $split = explode('-', $tanggal);

        $date = date('d F Y', strtotime($tanggal));
        return $date;
    }

    /**
     * @SWG\Get(
     *   path="/api/GetDetailSosisalisasiTanam",
     *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Detail Sosisalisasi Tanam",
     *   operationId="GetDetailSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="form_no",in="query",required=true, type="string"),
     * )
     */
    public function GetDetailSosisalisasiTanam(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.form_no','=',$request->form_no)
                    ->first();

            if($GetSosialisasiDetail){
                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                $planting_details = DB::table('planting_details')
                                    ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                                    'planting_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                                    ->where('form_no','=',$GetSosialisasiDetail->form_no)
                                    ->get();

                $planting_details_sum = DB::table('planting_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                                    ->where('planting_details.form_no','=',$GetSosialisasiDetail->form_no)
                                    ->sum('planting_details.amount');

                $get_amount_bag = ceil($planting_details_sum/20);
                                    
                $datavalpohon = [];
                $listvalpohon=array();
                foreach($planting_details as  $valpohon){                        
                    $datavalpohon = ['tree_name' => $valpohon->tree_name, 'tree_code' => $valpohon->tree_code, 'amount' => $valpohon->amount];
                    array_push($listvalpohon, $datavalpohon);                    
                }

                $datavalbag = [];
                $listvalbag=array();            
                $listvalnewpohon=array();
                $datavalnewpohon = [];   
                $newlist = 'no'; 
                for ($x = 1; $x <= $get_amount_bag; $x++) {
                    $listvaltemp=array();                       
                    $datavaltemp = [];                 
                    $jumlah_new_pohon = 0;
                    $jumlah_temp_detail = 0;
                    $jumlah_batas = 20;
                    if($newlist == 'yes'){                        
                        $listvalpohon = $listvalnewpohon;        
                        $listvalnewpohon=array();
                        $datavalnewpohon = []; 
                    }
                    $newlist = 'no';    
                    foreach($listvalpohon as  $valdetail){

                        $new_name = $valdetail['tree_name'];
                        if (strripos($valdetail['tree_name'], "Crops") !== false) {
                            $new_name = substr($valdetail['tree_name'],0,-8);
                        }
                        
                        $jumlah_new_pohon = $valdetail['amount'];

                        if($newlist == 'no'){
                            if($valdetail['amount'] < $jumlah_batas){
                                $datavaltemp = ['pohon' => $new_name,'amount' => $valdetail['amount']];
                                array_push($listvaltemp, $datavaltemp);
                                $jumlah_temp_detail = $jumlah_temp_detail + $valdetail['amount'];
                                $jumlah_batas = 20 - $jumlah_temp_detail;
                            }else{
                                $datavaltemp = ['pohon' => $new_name,'amount' => $jumlah_batas];
                                array_push($listvaltemp, $datavaltemp);
                                $jumlah_new_pohon = $valdetail['amount'] - $jumlah_batas;
                                $newlist = 'yes';
                            }
                        }
                        
                        
                        if($newlist == 'yes'){
                            $datavalnewpohon = ['tree_name' => $new_name, 'tree_code' => $valdetail['tree_code'], 'amount' => $jumlah_new_pohon];
                            array_push($listvalnewpohon, $datavalnewpohon); 
                        }
                                               
                    }

                    $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp];
                    array_push($listvalbag, $datavalbag);
                }


                $validate_name = '-';
                if($GetSosialisasiDetail->validate_by != '-'){
                    $employees = DB::table('employees')->where('nik','=',$GetSosialisasiDetail->validate_by)->first();
                    $validate_name = $employees->name;
                }

                $type_sppt = "Sendiri";
                if($Lahan->type_sppt == 0){
                    $type_sppt = "Sendiri";
                }elseif($Lahan->type_sppt == 1){
                    $type_sppt = "Keterkaitan Keluarga";
                }elseif($Lahan->type_sppt == 2){
                    $type_sppt = "Umum";
                }else{
                    $type_sppt = "Lain-lain";
                }

                // var_dump($type_sppt);
                $alamat = $Desas->name.' ('.$Farmer->address .')';
                // var_dump($alamat);

                $SosialisasiDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$field_facilitators->name,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalbag];
                
                $rslt =  $this->ResultReturn(200, 'success', $SosialisasiDetail);
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetDetailSosisalisasiTanamFFNo",
     *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Detail Sosisalisasi Tanam FFNo",
     *   operationId="GetDetailSosisalisasiTanamFFNo",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ff_no",in="query",required=true, type="string"),
     * )
     */
    public function GetDetailSosisalisasiTanamFFNo(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ff_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.ff_no','=',$request->ff_no)
                    ->where('planting_socializations.is_dell','=',0)
                    ->first();

            if($GetSosialisasiDetail){                
                $planting_period = DB::table('planting_period')
                                    ->join('planting_socializations', 'planting_socializations.form_no', '=', 'planting_period.form_no')
                                    ->where('planting_socializations.ff_no','=',$request->ff_no)
                                    ->where('planting_socializations.is_dell','=',0)
                                    ->get();
                $planting_details = DB::table('planting_details')
                                    ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                                    'planting_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                                    ->join('planting_socializations', 'planting_socializations.form_no', '=', 'planting_details.form_no')
                                    ->where('planting_socializations.ff_no','=',$request->ff_no)
                                    ->where('planting_socializations.is_dell','=',0)
                                    ->get();
                
                $data = ['planting_period'=>$planting_period, 'planting_details'=>$planting_details];
                
                $rslt =  $this->ResultReturn(200, 'success', $data);
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function CetakLabelSosTam(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.form_no','=',$request->form_no)
                    ->first();

            if($GetSosialisasiDetail){
                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                $planting_details = DB::table('planting_details')
                                    ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                                    'planting_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                                    ->where('form_no','=',$GetSosialisasiDetail->form_no)
                                    ->get();

                $planting_details_sum = DB::table('planting_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                                    ->where('planting_details.form_no','=',$GetSosialisasiDetail->form_no)
                                    ->sum('planting_details.amount');

                $get_amount_bag = ceil($planting_details_sum/20);
                                    
                $datavalpohon = [];
                $listvalpohon=array();
                foreach($planting_details as  $valpohon){                        
                    $datavalpohon = ['tree_name' => $valpohon->tree_name, 'tree_code' => $valpohon->tree_code, 'amount' => $valpohon->amount];
                    array_push($listvalpohon, $datavalpohon);                    
                }

                $datavalbag = [];
                $listvalbag=array();            
                $listvalnewpohon=array();
                $datavalnewpohon = [];   
                $newlist = 'no'; 
                for ($x = 1; $x <= $get_amount_bag; $x++) {
                    $listvaltemp=array();                       
                    $datavaltemp = [];                 
                    $jumlah_new_pohon = 0;
                    $jumlah_temp_detail = 0;
                    $jumlah_batas = 20;
                    if($newlist == 'yes'){                        
                        $listvalpohon = $listvalnewpohon;        
                        $listvalnewpohon=array();
                        $datavalnewpohon = []; 
                    }
                    $newlist = 'no';
                    $nn = 0;    
                    foreach($listvalpohon as  $valdetail){

                        $new_name = $valdetail['tree_name'];
                        if (strripos($valdetail['tree_name'], "Crops") !== false) {
                            $new_name = substr($valdetail['tree_name'],0,-8);
                        }
                        
                        $jumlah_new_pohon = $valdetail['amount'];

                        if($newlist == 'no'){
                            if($valdetail['amount'] < $jumlah_batas){
                                $datavaltemp = ['pohon' => $new_name,'amount' => $valdetail['amount']];
                                array_push($listvaltemp, $datavaltemp);
                                $jumlah_temp_detail = $jumlah_temp_detail + $valdetail['amount'];
                                $jumlah_batas = 20 - $jumlah_temp_detail;
                            }else{
                                $datavaltemp = ['pohon' => $new_name,'amount' => $jumlah_batas];
                                array_push($listvaltemp, $datavaltemp);
                                $jumlah_new_pohon = $valdetail['amount'] - $jumlah_batas;
                                $newlist = 'yes';
                            }
                            $nn = $nn + 1;
                        }
                        
                        
                        if($newlist == 'yes'){
                            $datavalnewpohon = ['tree_name' => $new_name, 'tree_code' => $valdetail['tree_code'], 'amount' => $jumlah_new_pohon];
                            array_push($listvalnewpohon, $datavalnewpohon); 
                        }
                                               
                    }

                    $now = Carbon::now();
                    $yearnow = now()->year;
                    $yeardigit = substr($yearnow,-2);
                    $no_lahan_qr = substr($GetSosialisasiDetail->no_lahan,2);
                    $no_qr_code = $x.'_'.$yeardigit.$no_lahan_qr;
                    $qrcodelahan = $this->generateqrcode($no_qr_code);

                    $n = 5-$nn;

                    $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
                    array_push($listvalbag, $datavalbag);
                }


                $validate_name = '-';
                if($GetSosialisasiDetail->validate_by != '-'){
                    $employees = DB::table('employees')->where('nik','=',$GetSosialisasiDetail->validate_by)->first();
                    $validate_name = $employees->name;
                }

                $type_sppt = "Sendiri";
                if($Lahan->type_sppt == 0){
                    $type_sppt = "Sendiri";
                }elseif($Lahan->type_sppt == 1){
                    $type_sppt = "Keterkaitan Keluarga";
                }elseif($Lahan->type_sppt == 2){
                    $type_sppt = "Umum";
                }else{
                    $type_sppt = "Lain-lain";
                }

                // var_dump($type_sppt);
                $alamat = $Desas->name.' ('.$Farmer->address .')';

                
                $newDateformatdistribution = date("d/m/Y", strtotime($planting_period->distribution_time));

                $countnama = count(explode(" ",$Farmer->name));
                // var_dump($alamat);

                $SosialisasiDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$field_facilitators->name,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalbag,'newDateformatdistribution'=>$newDateformatdistribution,'countnama'=>$countnama];
                
                return view('cetakLabelSostam', compact('SosialisasiDetail','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function generateqrcode ($val)
    {
        // $data = Data::findOrFail($id);
        $qrcode = QrCode::size(90)->generate($val);
        return $qrcode;
        // return view('qrcode',compact('qrcode'));
    }

    /**
     * @SWG\Post(
     *   path="/api/AddSosisalisasiTanam",
	 *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add SosisalisasiTanam",
     *   operationId="AddSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add SosisalisasiTanam",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ff_no", type="string", example="FF0001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F00000001"),
     *              @SWG\Property(property="no_lahan", type="string", example="L0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="pembuatan_lubang_tanam", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="distribution_time", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="planting_time", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="distribution_location", type="string", example="Kebon"),
     *          ),
     *      )
     * )
     *
     */
    public function AddSosisalisasiTanam(Request $request){
        $validator = Validator::make($request->all(), [
            'ff_no' => 'required',
            'farmer_no' => 'required', 
            'no_lahan' => 'required|unique:planting_socializations', 
            'planting_year' => 'required',
            'pembuatan_lubang_tanam' => 'required',
            'distribution_time' => 'required',
            'planting_time' => 'required',
            'distribution_location' => 'required'
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{             
            
            $Lahan = DB::table('lahans')->where('lahan_no','=',$request->no_lahan)->first();
            
            if($Lahan){
                $year = Carbon::now()->format('Y');
                $form_no = 'SO-'.$year.'-'.substr($request->no_lahan,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                PlantingSocializations::create([
                    'form_no' => $form_no,
                    'planting_year' => $request->planting_year,
                    'farmer_no' => $request->farmer_no,
                    'no_lahan' => $request->no_lahan,
                    'no_document' => $Lahan->document_no,
                    'validation' => $validation,
                    'validate_by' => $validate_by,
    
                    'ff_no' => $request->ff_no,
    
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
    
                    'is_dell' => 0
                ]);
    
                PlantingSocializationsPeriod::create([
                    'form_no' => $form_no,
                    'pembuatan_lubang_tanam' => $request->pembuatan_lubang_tanam,
                    'distribution_time' => $request->distribution_time,
                    'distribution_location' => $request->distribution_location,
                    'planting_time' => $request->planting_time,
    
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ]);

                $LahanDetails = DB::table('lahan_details')->where('lahan_no','=',$request->no_lahan)->get();
    
                foreach($LahanDetails as $val){
                    PlantingSocializationsDetails::create([
                        'form_no' => $form_no,
                        'tree_code' => $val->tree_code,
                        'amount' => $val->amount,
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);
                }

                DB::commit();
    
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(400, 'gagal', 'gagal');
                return response()->json($rslt, 400);
            }

            
        }catch (\Exception $ex){
            DB::rollback();
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateSosisalisasiTanam",
	 *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update SosisalisasiTanam",
     *   operationId="UpdateSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update SosisalisasiTanam",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="form_no", type="string", example="SO-2021-0000001"),
     *              @SWG\Property(property="ff_no", type="string", example="FF0001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F00000001"),
     *              @SWG\Property(property="no_lahan", type="string", example="L0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="pembuatan_lubang_tanam", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="distribution_time", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="planting_time", type="string", example="2021-10-22 10:00:00"),
     *              @SWG\Property(property="distribution_location", type="string", example="Kebon"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdateSosisalisasiTanam(Request $request){
        $validator = Validator::make($request->all(), [
            'form_no' => 'required',
            'ff_no' => 'required',
            'farmer_no' => 'required', 
            'no_lahan' => 'required', 
            'planting_year' => 'required',
            'pembuatan_lubang_tanam' => 'required',
            'distribution_time' => 'required',
            'planting_time' => 'required',
            'distribution_location' => 'required'
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        }  
        
        DB::beginTransaction();

        try{
            
            
            $form_no_old = $request->form_no;
            $Lahan = DB::table('lahans')->where('lahan_no','=',$request->no_lahan)->first();
            $planting_socializations = DB::table('planting_socializations')->where('form_no','=',$form_no_old)->first();
            
            if($planting_socializations){

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                PlantingSocializations::where('form_no', '=', $form_no_old)
                ->update([
                    'planting_year' => $request->planting_year,
                    'farmer_no' => $request->farmer_no,
                    'no_lahan' => $request->no_lahan,
                    'no_document' => $Lahan->document_no,
                    'validation' => $validation,
                    'validate_by' => $validate_by,
    
                    'ff_no' => $request->ff_no,
    
                    'updated_at'=>Carbon::now(),
    
                    // 'is_dell' => 0
                ]);
    
                PlantingSocializationsPeriod::where('form_no', '=', $form_no_old)
                ->update([
                    'pembuatan_lubang_tanam' => $request->pembuatan_lubang_tanam,
                    'distribution_time' => $request->distribution_time,
                    'distribution_location' => $request->distribution_location,
                    'planting_time' => $request->planting_time,
                    
                    'updated_at'=>Carbon::now(),
                ]);

                // $LahanDetails = DB::table('lahan_details')->where('lahan_no','=',$request->no_lahan)->get();

                // DB::table('planting_details')->where('form_no', $form_no_old)->delete();

                // foreach($LahanDetails as $val){
                //     PlantingSocializationsDetails::create([
                //         'form_no' => $form_no,
                //         'tree_code' => $val->tree_code,
                //         'amount' => $val->amount,
        
                //         'created_at'=>Carbon::now(),
                //         'updated_at'=>Carbon::now()
                //     ]);
                // }

                DB::commit();
    
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(400, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 400);
            }

            
        }catch (\Exception $ex){
            DB::rollback();
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdatePohonSosisalisasiTanam",
	 *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Pohon SosisalisasiTanam",
     *   operationId="UpdatePohonSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Pohon SosisalisasiTanam",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="form_no", type="string", example="SO-2021-0000001"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon bosku"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePohonSosisalisasiTanam(Request $request){
        $validator = Validator::make($request->all(), [
            'form_no' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{
             
            
            $form_no_old = $request->form_no;
            $list_pohon = $request->list_pohon;
            $planting_socializations = DB::table('planting_socializations')->where('form_no','=',$form_no_old)->first();
            
            if($planting_socializations){
                
                DB::table('planting_details')->where('form_no', $form_no_old)->delete();

                foreach($list_pohon as $val){
                    PlantingSocializationsDetails::create([
                        'form_no' => $form_no_old,
                        'tree_code' => $val['tree_code'],
                        'amount' => $val['amount'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);
                }

                DB::commit();
    
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(400, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 400);
            }

            
        }catch (\Exception $ex){
            DB::rollback();
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/SoftDeleteSosisalisasiTanam",
	 *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="SoftDelete SosisalisasiTanam",
     *   operationId="SoftDeleteSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="SoftDelete SosisalisasiTanam",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="form_no", type="string", example="SO-2021-0000001"),
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeleteSosisalisasiTanam(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'form_no' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $form_no_old = $request->form_no;
            $planting_socializations = DB::table('planting_socializations')->where('form_no','=',$form_no_old)->first();
            
            if($planting_socializations){

                PlantingSocializations::where('form_no', '=', $form_no_old)
                ->update([    
                    'updated_at'=>Carbon::now(),    
                    'is_dell' => 1
                ]);
    
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(400, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 400);
            }

            
        }catch (\Exception $ex){
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/ValidateSosisalisasiTanam",
	 *   tags={"SosisalisasiTanam"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Validate SosisalisasiTanam",
     *   operationId="ValidateSosisalisasiTanam",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Validate SosisalisasiTanam",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="form_no", type="string", example="SO-2021-0000001"),
     *              @SWG\Property(property="validate_by", type="string", example="00-11010"),
     *          ),
     *      )
     * )
     *
     */
    public function ValidateSosisalisasiTanam(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'form_no' => 'required',
                'validate_by' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $form_no_old = $request->form_no;
            $planting_socializations = DB::table('planting_socializations')->where('form_no','=',$form_no_old)->first();
            
            if($planting_socializations){

                PlantingSocializations::where('form_no', '=', $form_no_old)
                ->update([    
                    'updated_at'=>Carbon::now(),
                    'validate_by' => $request->validate_by,    
                    'validation' => 1
                ]);
    
                $rslt =  $this->ResultReturn(200, 'success', 'success');
                return response()->json($rslt, 200); 
            }else{
                $rslt =  $this->ResultReturn(400, 'doesnt match data', 'doesnt match data');
                return response()->json($rslt, 400);
            }

            
        }catch (\Exception $ex){
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }
}
