<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\PlantingHoleSurviellance;
use App\PlantingHoleSurviellanceDetail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlantingHoleController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetPlantingHoleFF",
     *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get PlantingHole FF",
     *   operationId="GetSosisalisasiTanamFF",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ff_no",in="query", required=true, type="string"),
     * )
     */
    public function GetPlantingHoleFF(Request $request){
        $ff_no = $request->ff_no;
        try{
           
                $GetPH = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at',  'planting_hole_surviellance.user_id as ff_no',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.user_id','=',$ff_no)
                    ->get();

                if(count($GetPH)!=0){ 
                    $count = DB::table('planting_hole_surviellance')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)
                        ->where('planting_hole_surviellance.user_id','=',$ff_no)
                        ->count();
                    
                    $data = ['count'=>$count, 'data'=>$GetPH];
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

    /**
     * @SWG\Get(
     *   path="/api/GetPlantingHoleAdmin",
     *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get PlantingHole Admin",
     *   operationId="GetPlantingHoleAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="typegetdata",in="query",required=true, type="string"),
     *      @SWG\Parameter(name="ff",in="query",required=true, type="string"),
     * )
     */
    public function GetPlantingHoleAdmin(Request $request){
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
                    $GetPH = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    ->where('planting_hole_surviellance.is_dell','=',0)                                        
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    // ->where('planting_hole_surviellance.user_id','=',$ff_no)
                    ->get();

                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetPH = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    ->where('planting_hole_surviellance.is_dell','=',0)
                    ->wherein('planting_hole_surviellance.user_id',$ffdecode)
                    // ->where('planting_hole_surviellance.user_id','=',$ff_no)
                    ->get();
                }

                $dataval = [];
                $listval=array();
                foreach ($GetPH as $val) {
                    $status = '';
                    if($val->is_validate==0){
                        $status = 'Belum Verifikasi';
                    }else{
                        $status = 'Sudah Verifikasi';
                    }
                    $dataval = ['id'=>$val->id,'lahan_no'=>$val->lahan_no, 'ph_form_no'=>$val->ph_form_no,
                    'planting_year'=>$val->planting_year, 'total_holes'=>$val->total_holes, 'ff_no' => $val->user_id, 'is_validate' => $val->is_validate, 
                    'nama_ff'=>$val->nama_ff,'validate_by'=>$val->validate_by,  'nama_petani'=>$val->nama_petani, 'status' => $status, 'is_dell' => $val->is_dell, 'created_at' => $val->created_at];
                    array_push($listval, $dataval);
                }

                if(count($listval)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('planting_hole_surviellance')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)                                       
                        ->where('lahans.mu_no','like',$mu)
                        ->where('lahans.target_area','like',$ta)
                        ->where('lahans.village','like',$village)
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$ff));

                        $count = DB::table('planting_hole_surviellance')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0) 
                        ->wherein('planting_hole_surviellance.user_id',$ffdecode)
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

    /**
     * @SWG\Get(
     *   path="/api/GetPlantingHoleDetail",
     *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get PlantingHole Detail",
     *   operationId="GetSosisalisasiTanamDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ph_form_no",in="query", required=true, type="string"),
     * )
     */
    public function GetPlantingHoleDetail(Request $request){
        $ph_form_no = $request->ph_form_no;
        try{
           
                $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.ph_form_no','=',$ph_form_no)
                    ->first();

                if($GetPHDetail){ 
                    $GetPHDetailList = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id',
                        'planting_hole_details.ph_form_no','planting_hole_details.tree_code','trees.tree_category',
                        'planting_hole_details.amount', 'planting_hole_details.created_at',
                        'trees.tree_name as tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        // ->where('planting_hole_surviellance.is_dell','=',0)
                        ->where('planting_hole_details.ph_form_no','=',$ph_form_no)
                        ->get();
                    
                    $data = ['list_detail'=>$GetPHDetailList, 'data'=>$GetPHDetail];
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

    /**
     * @SWG\Get(
     *   path="/api/GetPlantingHoleDetailFFNo",
     *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get PlantingHole Detail FFNo",
     *   operationId="GetPlantingHoleDetailFFNo",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     * )
     */
    public function GetPlantingHoleDetailFFNo(Request $request){
        $user_id = $request->user_id;
        try{
           
                $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.user_id','=',$user_id)
                    ->first();

                if($GetPHDetail){ 
                    $GetPHDetailList = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id',
                        'planting_hole_details.ph_form_no','planting_hole_details.tree_code','trees.tree_category',
                        'planting_hole_details.amount', 'planting_hole_details.created_at',
                        'trees.tree_name as tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        ->leftjoin('planting_hole_surviellance', 'planting_hole_surviellance.ph_form_no', '=', 'planting_hole_details.ph_form_no')
                        // ->where('planting_hole_surviellance.is_dell','=',0)
                        ->where('planting_hole_surviellance.user_id','=',$user_id)
                        ->get();
                    
                    $data = ['list_detail'=>$GetPHDetailList];
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

    public function CetakLabelLubangTanam(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }


            $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.ph_form_no','=',$request->ph_form_no)
                    ->first();

                    
            
            // var_dump($GetPHDetail);

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.no_lahan','=',$GetPHDetail->lahan_no)
                    ->first();

            if($GetSosialisasiDetail){


                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                // $planting_details = DB::table('planting_details')
                //                     ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                //                     'planting_details.amount','trees.tree_name','trees.tree_category')
                //                     ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                //                     ->where('form_no','=',$GetSosialisasiDetail->form_no)
                //                     ->get();
                $planting_details = DB::table('planting_hole_details')
                                    ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                    'planting_hole_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->get();
                                    

                $planting_details_sum = DB::table('planting_hole_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('planting_hole_details.ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->sum('planting_hole_details.amount');

                // $get_amount_bag = ceil($planting_details_sum/20);
                $get_amount_bag = 0;
                                    
                $datavalpohon = [];
                $listvalpohon=array();
                foreach($planting_details as  $valpohon){
                    if($valpohon->tree_category =='Pohon_Kayu'){
                        $get_amount_bag = $get_amount_bag + ceil($valpohon->amount/15);  
                    }else if($valpohon->tree_category =='Pohon_Buah'){
                        $get_amount_bag = $get_amount_bag + ceil($valpohon->amount/6); 
                    }else{
                        $get_amount_bag = $get_amount_bag + ceil($valpohon->amount/5);  
                    }                      
                    $datavalpohon = ['tree_name' => $valpohon->tree_name, 'tree_code' => $valpohon->tree_code,'tree_category' => $valpohon->tree_category, 
                    'amount' => $valpohon->amount];
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
                                if((int)$valdetail['amount'] != 0 ){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $valdetail['amount']];
                                    array_push($listvaltemp, $datavaltemp);
                                }
                                $jumlah_temp_detail = $jumlah_temp_detail + $valdetail['amount'];
                                $jumlah_batas = 20 - $jumlah_temp_detail;
                            }else{
                                if((int)$valdetail['amount'] != 0 ){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $jumlah_batas];
                                    array_push($listvaltemp, $datavaltemp);
                                }                                
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

                    

                // var_dump('test');

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

                

                $LubangTanamDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'ph_form_no'=>$GetPHDetail->ph_form_no,'total_holes'=>$GetPHDetail->total_holes,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$GetPHDetail->nama_ff,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalbag,'newDateformatdistribution'=>$newDateformatdistribution,'countnama'=>$countnama];
                
                return view('cetakLabelLubangTanam', compact('LubangTanamDetail','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'No Lahan Tidak ada dalam SOSTAM');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function CetakLabelLubangTanamTemp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }


            $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.ph_form_no','=',$request->ph_form_no)
                    ->first();

                    
            
            // var_dump($GetPHDetail);

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.no_lahan','=',$GetPHDetail->lahan_no)
                    ->first();

            if($GetSosialisasiDetail){


                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                // $planting_details = DB::table('planting_details')
                //                     ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                //                     'planting_details.amount','trees.tree_name','trees.tree_category')
                //                     ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                //                     ->where('form_no','=',$GetSosialisasiDetail->form_no)
                //                     ->get();
                $planting_details = DB::table('planting_hole_details')
                                    ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                    'planting_hole_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->orderBy('trees.tree_category', 'DESC')
                                    ->get();
                                    

                $planting_details_sum = DB::table('planting_hole_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('planting_hole_details.ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->sum('planting_hole_details.amount');

                // $get_amount_bag = ceil($planting_details_sum/20);
                $get_amount_bag = 0;
                                    
                $datavalpohon = [];
                $listvalpohon=array();
                $datavalbag = [];
                $listvalbag=array(); 

                $datavaltemp = [];
                $listvaltemp=array();
                $datavalbag = [];
                $listvalbag=array();
                $looping = false;
                $amount_loop = 0; 
                $mount_total_temp = 0;
                $sisa = 0;
                $previous_category = '-'; 
                foreach($planting_details as  $valpohon){
                        $new_name =$valpohon->tree_name;
                        if (strripos($valpohon->tree_name, "Crops") !== false) {
                            $new_name = substr($valpohon->tree_name,0,-8);
                        }
                    if($valpohon->tree_category =='Pohon_Kayu'){
                        $batas = 15;
                        if($valpohon->amount > $batas){
                            $looping = true;
                            if ($sisa != 0 && $previous_category==$valpohon->tree_category){
                                $valsisainput = 15-$sisa;
                                $datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
                                array_push($listvaltemp, $datavaltemp);
        
                                $datavalbag = ['listvaltemp'=>$listvaltemp];
                                array_push($listvalbag, $datavalbag);

                                $amount_loop = ceil($valpohon->amount - $valsisainput/15);
                                $mount_total_temp = $valpohon->amount - $valsisainput;
                            }else{
                                if($sisa != 0){
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                }                                
                                $amount_loop = ceil($valpohon->amount/15);
                                $mount_total_temp = $valpohon->amount;
                            }  
                        }else{
                            $looping = false;
                            $datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
                            array_push($listvaltemp, $datavaltemp);

                            $datavalbag = ['listvaltemp'=>$listvaltemp];
                            array_push($listvalbag, $datavalbag);
                        }
                                              
                    }else if($valpohon->tree_category =='Pohon_Buah'){
                        if($valpohon->amount > 6){
                            $looping = true;
                            if ($sisa != 0 && $previous_category==$valpohon->tree_category){
                                $valsisainput = 6-$sisa;
                                $datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
                                array_push($listvaltemp, $datavaltemp);
        
                                $datavalbag = ['listvaltemp'=>$listvaltemp];
                                array_push($listvalbag, $datavalbag);

                                $amount_loop = ceil($valpohon->amount - $valsisainput/6);
                                $mount_total_temp = $valpohon->amount - $valsisainput;
                            }else{  
                                if($sisa != 0){
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                }                               
                                $amount_loop = ceil($valpohon->amount/6);
                                $mount_total_temp = $valpohon->amount;
                            } 
                            // $amount_loop = ceil($valpohon->amount/6);
                            // $mount_total_temp = $valpohon->amount;
                        }else{
                            $looping = false;
                            $datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
                            array_push($listvaltemp, $datavaltemp);

                            $datavalbag = ['listvaltemp'=>$listvaltemp];
                            array_push($listvalbag, $datavalbag);
                        }
                    }else{
                        if($valpohon->amount > 5){
                            $looping = true;
                            if ($sisa != 0 && $previous_category==$valpohon->tree_category){
                                $valsisainput = 5-$sisa;
                                $datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
                                array_push($listvaltemp, $datavaltemp);
        
                                $datavalbag = ['listvaltemp'=>$listvaltemp];
                                array_push($listvalbag, $datavalbag);

                                $amount_loop = ceil($valpohon->amount - $valsisainput/5);
                                $mount_total_temp = $valpohon->amount - $valsisainput;
                            }else{    
                                if($sisa != 0){
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                }                             
                                $amount_loop = ceil($valpohon->amount/5);
                                $mount_total_temp = $valpohon->amount;
                            } 
                            // $amount_loop = ceil($valpohon->amount/5);
                            // $mount_total_temp = $valpohon->amount;
                        }else{
                            $looping = false;
                            $datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
                            array_push($listvaltemp, $datavaltemp);

                            $datavalbag = ['listvaltemp'=>$listvaltemp];
                            array_push($listvalbag, $datavalbag);
                        }
                    }
                    
                    if( $looping == true){
                        for ($x = 1; $x <= $amount_loop; $x++) {
                            $datavaltemp = [];
                            $listvaltemp=array();
                            $sisa=0;

                            if($valpohon->tree_category =='Pohon_Kayu'){
                                if($mount_total_temp > 15){
                                   
                                    $datavaltemp = ['pohon' => $new_name,'amount' => 15];
                                    array_push($listvaltemp, $datavaltemp);
                                    
                                    $mount_total_temp = $mount_total_temp - 15;

                                    
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                }else{
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
                                    array_push($listvaltemp, $datavaltemp);

                                    $sisa = $mount_total_temp;
                                    $previous_category=$valpohon->tree_category;
                                }

                            }else if($valpohon->tree_category =='Pohon_Buah'){
                                if($mount_total_temp > 6){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => 6];
                                    array_push($listvaltemp, $datavaltemp);

                                    
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                    $mount_total_temp = $mount_total_temp - 6;
                                }else{
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
                                    array_push($listvaltemp, $datavaltemp);
                                    $sisa = $mount_total_temp;
                                    $previous_category=$valpohon->tree_category;
                                }

                            }else{
                                if($mount_total_temp > 5){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => 5];
                                    array_push($listvaltemp, $datavaltemp);

                                    
                                    $datavalbag = ['listvaltemp'=>$listvaltemp];
                                    array_push($listvalbag, $datavalbag);
                                    $mount_total_temp = $mount_total_temp - 5;
                                }else{
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
                                    array_push($listvaltemp, $datavaltemp);
                                    $sisa = $mount_total_temp;
                                    $previous_category=$valpohon->tree_category;
                                }

                            }
                        }
                    }
                }

                
                $datavalbag = ['listvaltemp'=>$listvaltemp];
                array_push($listvalbag, $datavalbag);
                
                // print_r($listvalbag);

                $datavalfix = [];
                $listvalfix=array();
                $ii = 1;
                $countlist = count($listvalbag);
                foreach($listvalbag as  $valbag){
                    $now = Carbon::now();
                    $yearnow = now()->year;
                    $yeardigit = substr($yearnow,-2);
                    $no_lahan_qr = substr($GetSosialisasiDetail->no_lahan,2);
                    $no_qr_code = $x.'_'.$yeardigit.$no_lahan_qr;
                    $qrcodelahan = $this->generateqrcode($no_qr_code);

                    $n = 3;

                    $datavalfix = ['no_bag'=>$ii.'/'.$countlist, 'listvaltemp'=>$valbag['listvaltemp'], 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
                    // $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
                    array_push($listvalfix, $datavalfix);

                    $ii = $ii + 1;
                }

                // print_r($listvalfix);

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

                

                $LubangTanamDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'ph_form_no'=>$GetPHDetail->ph_form_no,'total_holes'=>$GetPHDetail->total_holes,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$GetPHDetail->nama_ff,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalfix,'newDateformatdistribution'=>$newDateformatdistribution,'countnama'=>$countnama];
                
                $listvalbag = $listvalfix;
                return view('cetakLabelLubangTanam', compact('LubangTanamDetail','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'No Lahan Tidak ada dalam SOSTAM');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function CetakBuktiPenyerahan(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }


            $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.ph_form_no','=',$request->ph_form_no)
                    ->first();

                    
            
            // var_dump($GetPHDetail);

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.no_lahan','=',$GetPHDetail->lahan_no)
                    ->first();

            if($GetSosialisasiDetail){


                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                // $planting_details = DB::table('planting_details')
                //                     ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                //                     'planting_details.amount','trees.tree_name','trees.tree_category')
                //                     ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                //                     ->where('form_no','=',$GetSosialisasiDetail->form_no)
                //                     ->get();
                $planting_details = DB::table('planting_hole_details')
                                    ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                    'planting_hole_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->get();
                                    

                $planting_details_sum = DB::table('planting_hole_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('planting_hole_details.ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->sum('planting_hole_details.amount');

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
                                if((int)$valdetail['amount'] != 0 ){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $valdetail['amount']];
                                    array_push($listvaltemp, $datavaltemp);
                                }
                                $jumlah_temp_detail = $jumlah_temp_detail + $valdetail['amount'];
                                $jumlah_batas = 20 - $jumlah_temp_detail;
                            }else{
                                if((int)$valdetail['amount'] != 0 ){
                                    $datavaltemp = ['pohon' => $new_name,'amount' => $jumlah_batas];
                                    array_push($listvaltemp, $datavaltemp);
                                }                                
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

                    

                // var_dump('test');

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

                

                $LubangTanamDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'ph_form_no'=>$GetPHDetail->ph_form_no,'total_holes'=>$GetPHDetail->total_holes,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$GetPHDetail->nama_ff,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalbag,'newDateformatdistribution'=>$newDateformatdistribution,'countnama'=>$countnama];
                
                return view('cetakBuktiPenyerahan', compact('LubangTanamDetail','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'No Lahan Tidak ada dalam SOSTAM');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function CetakBuktiPenyerahanTemp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }


            $GetPHDetail = DB::table('planting_hole_surviellance')
                    ->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                    'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                    'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
                    'planting_hole_surviellance.farmer_signature','planting_hole_surviellance.gambar1','planting_hole_surviellance.gambar2',
                    'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by','planting_hole_surviellance.total_holes',
                    'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'planting_hole_surviellance.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                    // ->where('planting_hole_surviellance.is_dell','=',0)
                    ->where('planting_hole_surviellance.ph_form_no','=',$request->ph_form_no)
                    ->first();

                    
            
            // var_dump($GetPHDetail);

            $GetSosialisasiDetail = DB::table('planting_socializations')
                    ->select('planting_socializations.id','planting_socializations.no_lahan',
                    'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
                    'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
                    'planting_socializations.is_dell', 'planting_socializations.created_at')
                    ->where('planting_socializations.no_lahan','=',$GetPHDetail->lahan_no)
                    ->first();

            if($GetSosialisasiDetail){


                $field_facilitators = DB::table('field_facilitators')->where('ff_no','=',$GetSosialisasiDetail->ff_no)->first();
                $Farmer = DB::table('farmers')->where('farmer_no','=',$GetSosialisasiDetail->farmer_no)->first();
                $Desas = DB::table('desas')->where('kode_desa','=',$Farmer->village)->first();
                $Lahan = DB::table('lahans')->where('lahan_no','=',$GetSosialisasiDetail->no_lahan)->first();
                
                $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
                // $planting_details = DB::table('planting_details')
                //                     ->select('planting_details.id','planting_details.form_no','planting_details.tree_code',
                //                     'planting_details.amount','trees.tree_name','trees.tree_category')
                //                     ->join('trees', 'trees.tree_code', '=', 'planting_details.tree_code')
                //                     ->where('form_no','=',$GetSosialisasiDetail->form_no)
                //                     ->get();
                $planting_details = DB::table('planting_hole_details')
                                    ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                    'planting_hole_details.amount','trees.tree_name','trees.tree_category')
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->orderBy('trees.tree_category', 'DESC')
                                    ->get();
                                    

                $planting_details_sum = DB::table('planting_hole_details')
                                    // ->select(DB::raw('SUM(planting_details.amount) As total'))
                                    ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                                    ->where('planting_hole_details.ph_form_no','=',$GetPHDetail->ph_form_no)
                                    ->sum('planting_hole_details.amount');

				$get_amount_bag = 0;
				
				$datavalpohon = [];
				$listvalpohon=array();
				$datavalbag = [];
				$listvalbag=array(); 

				$datavaltemp = [];
				$listvaltemp=array();
				$datavalbag = [];
				$listvalbag=array();
				$looping = false;
				$amount_loop = 0; 
				$mount_total_temp = 0;
				$sisa = 0;
				$previous_category = '-'; 
				foreach($planting_details as  $valpohon){
						$new_name =$valpohon->tree_name;
						if (strripos($valpohon->tree_name, "Crops") !== false) {
							$new_name = substr($valpohon->tree_name,0,-8);
						}
					if($valpohon->tree_category =='Pohon_Kayu'){
						$batas = 15;
						if($valpohon->amount > $batas){
							$looping = true;
							if ($sisa != 0 && $previous_category==$valpohon->tree_category){
								$valsisainput = 15-$sisa;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
								array_push($listvaltemp, $datavaltemp);
		
								$datavalbag = ['listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$amount_loop = ceil($valpohon->amount - $valsisainput/15);
								$mount_total_temp = $valpohon->amount - $valsisainput;
							}else{
								if($sisa != 0){
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
								}                                
								$amount_loop = ceil($valpohon->amount/15);
								$mount_total_temp = $valpohon->amount;
							}  
						}else{
							$looping = false;
							$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
							array_push($listvaltemp, $datavaltemp);

							$datavalbag = ['listvaltemp'=>$listvaltemp];
							array_push($listvalbag, $datavalbag);
						}
												
					}else if($valpohon->tree_category =='Pohon_Buah'){
						if($valpohon->amount > 6){
							$looping = true;
							if ($sisa != 0 && $previous_category==$valpohon->tree_category){
								$valsisainput = 6-$sisa;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
								array_push($listvaltemp, $datavaltemp);
		
								$datavalbag = ['listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$amount_loop = ceil($valpohon->amount - $valsisainput/6);
								$mount_total_temp = $valpohon->amount - $valsisainput;
							}else{  
								if($sisa != 0){
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
								}                               
								$amount_loop = ceil($valpohon->amount/6);
								$mount_total_temp = $valpohon->amount;
							} 
							// $amount_loop = ceil($valpohon->amount/6);
							// $mount_total_temp = $valpohon->amount;
						}else{
							$looping = false;
							$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
							array_push($listvaltemp, $datavaltemp);

							$datavalbag = ['listvaltemp'=>$listvaltemp];
							array_push($listvalbag, $datavalbag);
						}
					}else{
						if($valpohon->amount > 5){
							$looping = true;
							if ($sisa != 0 && $previous_category==$valpohon->tree_category){
								$valsisainput = 5-$sisa;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
								array_push($listvaltemp, $datavaltemp);
		
								$datavalbag = ['listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$amount_loop = ceil($valpohon->amount - $valsisainput/5);
								$mount_total_temp = $valpohon->amount - $valsisainput;
							}else{    
								if($sisa != 0){
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
								}                             
								$amount_loop = ceil($valpohon->amount/5);
								$mount_total_temp = $valpohon->amount;
							} 
							// $amount_loop = ceil($valpohon->amount/5);
							// $mount_total_temp = $valpohon->amount;
						}else{
							$looping = false;
							$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
							array_push($listvaltemp, $datavaltemp);

							$datavalbag = ['listvaltemp'=>$listvaltemp];
							array_push($listvalbag, $datavalbag);
						}
					}
					
					if( $looping == true){
						for ($x = 1; $x <= $amount_loop; $x++) {
							$datavaltemp = [];
							$listvaltemp=array();
							$sisa=0;

							if($valpohon->tree_category =='Pohon_Kayu'){
								if($mount_total_temp > 15){
									
									$datavaltemp = ['pohon' => $new_name,'amount' => 15];
									array_push($listvaltemp, $datavaltemp);
									
									$mount_total_temp = $mount_total_temp - 15;

									
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
								}else{
									$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
									array_push($listvaltemp, $datavaltemp);

									$sisa = $mount_total_temp;
									$previous_category=$valpohon->tree_category;
								}

							}else if($valpohon->tree_category =='Pohon_Buah'){
								if($mount_total_temp > 6){
									$datavaltemp = ['pohon' => $new_name,'amount' => 6];
									array_push($listvaltemp, $datavaltemp);

									
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
									$mount_total_temp = $mount_total_temp - 6;
								}else{
									$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
									array_push($listvaltemp, $datavaltemp);
									$sisa = $mount_total_temp;
									$previous_category=$valpohon->tree_category;
								}

							}else{
								if($mount_total_temp > 5){
									$datavaltemp = ['pohon' => $new_name,'amount' => 5];
									array_push($listvaltemp, $datavaltemp);

									
									$datavalbag = ['listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);
									$mount_total_temp = $mount_total_temp - 5;
								}else{
									$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
									array_push($listvaltemp, $datavaltemp);
									$sisa = $mount_total_temp;
									$previous_category=$valpohon->tree_category;
								}

							}
						}
					}
				}

				
				$datavalbag = ['listvaltemp'=>$listvaltemp];
				array_push($listvalbag, $datavalbag);
				
				// print_r($listvalbag);

				$datavalfix = [];
				$listvalfix=array();
				$ii = 1;
				$countlist = count($listvalbag);
				foreach($listvalbag as  $valbag){
					$now = Carbon::now();
					$yearnow = now()->year;
					$yeardigit = substr($yearnow,-2);
					$no_lahan_qr = substr($GetSosialisasiDetail->no_lahan,2);
					$no_qr_code = $x.'_'.$yeardigit.$no_lahan_qr;
					$qrcodelahan = $this->generateqrcode($no_qr_code);

					$n = 3;

					$datavalfix = ['no_bag'=>$ii.'/'.$countlist, 'listvaltemp'=>$valbag['listvaltemp'], 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
					// $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
					array_push($listvalfix, $datavalfix);

					$ii = $ii + 1;
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

                

                $LubangTanamDetail = ['id'=>$GetSosialisasiDetail->id, 'form_no'=>$GetSosialisasiDetail->form_no,
                'ph_form_no'=>$GetPHDetail->ph_form_no,'total_holes'=>$GetPHDetail->total_holes,
                'planting_year'=>$GetSosialisasiDetail->planting_year,'validation'=>$GetSosialisasiDetail->validation,
                'validate_by'=>$GetSosialisasiDetail->validate_by, 'validate_name'=>$validate_name, 
                'ff_no'=>$GetSosialisasiDetail->ff_no,'ff_name'=>$GetPHDetail->nama_ff,
                'kode'=>$GetSosialisasiDetail->farmer_no,'farmer_no'=>$GetSosialisasiDetail->farmer_no,'nama_petani'=>$Farmer->name,'ktp_no'=>$Farmer->ktp_no,'alamat'=>$alamat,
                'no_lahan'=>$GetSosialisasiDetail->no_lahan,'opsi_pola_tanam'=>$Lahan->opsi_pola_tanam,'document_no'=>$Lahan->document_no,'type_sppt'=>$type_sppt,'luas_lahan'=>$Lahan->land_area,'luas_tanam'=>$Lahan->planting_area, 'current_crops'=>$Lahan->current_crops,
                'pembuatan_lubang_tanam'=>$planting_period->pembuatan_lubang_tanam,'distribution_time'=>$planting_period->distribution_time,
                'planting_time'=>$planting_period->planting_time,'distribution_location'=>$planting_period->distribution_location,
                'planting_details'=>$planting_details,'planting_details_sum'=>$planting_details_sum,'get_amount_bag'=>$get_amount_bag,
                'listvalbag'=>$listvalfix,'newDateformatdistribution'=>$newDateformatdistribution,'countnama'=>$countnama];
                
                $listvalbag = $listvalfix;

                return view('cetakBuktiPenyerahan', compact('LubangTanamDetail','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'No Lahan Tidak ada dalam SOSTAM');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function CetakExcelPlantingHoleAll(Request $request){
        try{
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

            $getmax_kayu = $request->max_kayu;
			if($getmax_kayu){$max_kayu=$getmax_kayu;}
            else{$max_kayu=15;}
			$getmax_mpts = $request->max_mpts;
			if($getmax_mpts){$max_mpts=$getmax_mpts;}
            else{$max_mpts=6;}
			$getmax_crops = $request->max_crops;
			if($getmax_crops){$max_crops=$getmax_crops;}
            else{$max_crops=5;}

			if($typegetdata == 'all'){
				$GetPHAll = DB::table('planting_hole_surviellance')
				->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
				'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
				'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
				'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
				'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
				'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
				->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
				->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
				->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
				->where('planting_hole_surviellance.is_dell','=',0)                                        
				->where('lahans.mu_no','like',$mu)
				->where('lahans.target_area','like',$ta)
				->where('lahans.village','like',$village)
				// ->where('planting_hole_surviellance.user_id','=',$ff_no)
				->get();

			}else{
				$ffdecode = (explode(",",$ff));

				$GetPHAll = DB::table('planting_hole_surviellance')
				->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
				'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
				'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
				'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
				'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
				'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
				->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
				->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
				->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
				->where('planting_hole_surviellance.is_dell','=',0)
				->wherein('planting_hole_surviellance.user_id',$ffdecode)
				// ->where('planting_hole_surviellance.user_id','=',$ff_no)
				->get();
			}
            
            // var_dump($max_kayu);

            if(count($GetPHAll)!=0){

                $get_amount_bag = 0;                                 
																
																
				$dataxx = [];
				$listxxx=array();

				foreach($GetPHAll as  $valphall){
						$GetPH = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                'planting_hole_details.amount','trees.tree_name','trees.tree_category',
                                'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'lahans.village',
                                'planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                                'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                                'planting_hole_surviellance.total_holes', 'planting_hole_surviellance.latitude', 
                                'planting_hole_surviellance.longitude','planting_hole_surviellance.is_validate',
                                'planting_hole_surviellance.validate_by','planting_hole_surviellance.is_dell', 
                                'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id'
                                )
                        ->leftjoin('planting_hole_surviellance', 'planting_hole_surviellance.ph_form_no', '=', 'planting_hole_details.ph_form_no')
                        ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)                                        
                        ->where('planting_hole_surviellance.ph_form_no','=',$valphall->ph_form_no)
						->orderBy('trees.tree_category', 'DESC')
                        // ->where('ph_form_no','=',$GetPH->ph_form_no)
                        ->get();

					// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH)];
					// array_push($listxxx, $dataxx);

					$datavalpohon = [];
					$listvalpohon=array();
					$datavalbag = [];
					$listvalbag=array(); 

					$datavaltemp = [];
					$listvaltemp=array();
					$datavalbag = [];
					$listvalbag=array();
					$looping = false;
					$amount_loop = 0; 
					$mount_total_temp = 0;
					$sisa = 0;
					$previous_category = '-';
					$x = 0; 
					$previous_code_ph = '-';
					$max = 0;
					$datamaxbagph = [];
					$listmaxbagph=array();

					

					foreach($GetPH as  $valpohon){

						$GetSosialisasiDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->where('planting_socializations.no_lahan','=',$valpohon->lahan_no)
									->first();

						$Desas = DB::table('desas')->where('kode_desa','=',$valpohon->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						
						$planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period->distribution_location;
						}

						$nama_ff = $valpohon->nama_ff;
						$nama_petani = $valpohon->nama_petani;
						$ph_form_no = $valpohon->ph_form_no;
						$lahan_no = $valpohon->lahan_no;
						$total_holes = $valpohon->total_holes;
						

						$new_name =$valpohon->tree_name;
						if (strripos($valpohon->tree_name, "Crops") !== false) {
							$new_name = substr($valpohon->tree_name,0,-8);
						}

						// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH),'nama_desa' => $nama_desa,
						// 'distribution_location' => $distribution_location,'distribution_time' => $distribution_time,
						// 'valpohon' => $new_name,'amount' => $valpohon->amount];
						// array_push($listxxx, $dataxx);

						if($valpohon->tree_category =='Pohon_Kayu'){
								$batas = $max_kayu;
								$pohon_kategori = 'Pohon_Kayu';

								if($valpohon->amount > $batas){
									$looping = true;
									if ($sisa != 0 && $previous_category==$valpohon->tree_category){
										$valsisainput = $max_kayu-$sisa;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$amount_loop = ceil($valpohon->amount - $valsisainput/$max_kayu);
										$mount_total_temp = $valpohon->amount - $valsisainput;
									}else{
										if($sisa != 0){
											$prv_ctg = $this->convertcategorytrees($previous_category);
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);
										}                                
										$amount_loop = ceil($valpohon->amount/$max_kayu);
										$mount_total_temp = $valpohon->amount;
									}  
								}else{
										$looping = false;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$datavaltemp = [];
										$listvaltemp=array();
								}
																																
						}else if($valpohon->tree_category =='Pohon_Buah'){
							$pohon_kategori = 'Pohon_Buah (MPTS)';
							if($valpohon->amount > $max_mpts){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_mpts-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_mpts);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{  
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                               
									$amount_loop = ceil($valpohon->amount/$max_mpts);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/6);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}else{
							$pohon_kategori = 'Crops';
							if($valpohon->amount > $max_crops){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_crops-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_crops);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{    
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                             
									$amount_loop = ceil($valpohon->amount/$max_crops);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/5);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}
							
						if( $looping == true){
							$nn = 1;
							for ($x = 1; $x <= $amount_loop; $x++) {
								$datavaltemp = [];
								$listvaltemp=array();
								$sisa=0;

								if($nn>$amount_loop){
									break;
								}

								if($valpohon->tree_category =='Pohon_Kayu'){
									if($mount_total_temp > $max_kayu){
												
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_kayu];
										array_push($listvaltemp, $datavaltemp);
										
										$mount_total_temp = $mount_total_temp - $max_kayu;

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}else{
										if($mount_total_temp == 0){
											break;
										}
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_kayu){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else if($valpohon->tree_category =='Pohon_Buah'){
									if($mount_total_temp > $max_mpts){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_mpts];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_mpts;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_mpts){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else{
									if($mount_total_temp > $max_crops){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_crops];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_crops;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);
										if($mount_total_temp == $max_crops){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
										
									}

								}

								$nn = $nn + 1;
							}
						}
					}

					// $prv_ctg = $this->convertcategorytrees($previous_category);
					// $datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
					// 'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
					// 'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];;
					// array_push($listvalbag, $datavalbag);
				
					$datavalfix = [];
					$listvalfix=array();
					$ii = 1;
					$countlist = count($listvalbag);
					foreach($listvalbag as  $valbag){
								
						$dataxx = ['no_bag'=>$ii.'/'.$countlist, 'nama_ff'=>$valbag['nama_ff'],'nama_petani'=>$valbag['nama_petani'],
						'distribution_time'=>$valbag['distribution_time'],'pohon_kategori'=>$valbag['pohon_kategori'],'nama_desa'=>$valbag['nama_desa'],
						'distribution_location'=>$valbag['distribution_location'],'listvaltemp'=>$valbag['listvaltemp'],
						'lahan_no'=>$valbag['lahan_no'],'total_holes'=>$valbag['total_holes']];
						// $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
						array_push($listxxx, $dataxx);

						$ii = $ii + 1;
					}
			
				}                   
                                    

				// var_dump($listxxx);

				$nama_title = 'Cetak Excel Data Lubang Tanam & Distribusi Bibit'; 
                $listvalbag = $listxxx;

                return view('cetakPlantingHoleAll', compact('nama_title','listvalbag'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
	public function CetakExcelLoadingPlan(Request $request){
        try{
			$getmax_kayu = $request->max_kayu;
			if($getmax_kayu){$max_kayu=$getmax_kayu;}
            else{$max_kayu=15;}
			$getmax_mpts = $request->max_mpts;
			if($getmax_mpts){$max_mpts=$getmax_mpts;}
            else{$max_mpts=6;}
			$getmax_crops = $request->max_crops;
			if($getmax_crops){$max_crops=$getmax_crops;}
            else{$max_crops=5;}

            $typegetdatadownload = $request->typegetdatadownload;
			$detailexcel=[];
			if($typegetdatadownload == 'ff'){
				if($request->ff){
					$GetPHAll = DB::table('planting_hole_surviellance')
						->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
						'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
						'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
						'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
						'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
						'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
						->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
						->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
						->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
						->where('planting_hole_surviellance.is_dell','=',0)   
						->where('planting_hole_surviellance.user_id','=',$request->ff)
						// ->where('planting_hole_surviellance.user_id','=',$ff_no)
						->get();

						$GetDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan','lahans.village',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
									->where('planting_socializations.ff_no','=',$request->ff)
									->first();
						$planting_period_detail = DB::table('planting_period')->where('form_no','=',$GetDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period_detail){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period_detail->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period_detail->distribution_location;
						}
						$Desas = DB::table('desas')->where('kode_desa','=',$GetDetail->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						$GetFF= DB::table('field_facilitators')
									->select('field_facilitators.name')
									->where('field_facilitators.ff_no','=',$request->ff)
									->first();			
						$detailexcel = ['type' => 'loading_plan','nama_ff' => $GetFF->name,'distribution_time' => $distribution_time, 
						'distribution_location' => $distribution_location, 'nama_desa' => $nama_desa];
						
						$nama_title = 'Cetak Excel Loading Plan'; 
				}else{
					$rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                	return response()->json($rslt, 404);
				}
				

			}else{
				// $ffdecode = (explode(",",$ff));
				if($request->farmer_no){
					$GetPHAll = DB::table('planting_hole_surviellance')
						->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
						'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
						'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
						'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
						'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
						'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
						->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
						->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
						->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
						->where('planting_hole_surviellance.is_dell','=',0)   
						->where('lahans.farmer_no','=',$request->farmer_no)
						// ->where('planting_hole_surviellance.user_id','=',$ff_no)
						->get();

						$GetDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan','lahans.village',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_socializations.no_lahan')
									->where('planting_socializations.farmer_no','=',$request->farmer_no)
									->first();
						$planting_period_detail = DB::table('planting_period')->where('form_no','=',$GetDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period_detail){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period_detail->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period_detail->distribution_location;
						}
						$Desas = DB::table('desas')->where('kode_desa','=',$GetDetail->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						$GetFarmer= DB::table('farmers')
									->select('farmers.name', 'farmers.user_id')
									->where('farmers.farmer_no','=',$request->farmer_no)
									->first();
						$GetFF= DB::table('field_facilitators')
									->select('field_facilitators.name')
									->where('field_facilitators.ff_no','=',$GetFarmer->user_id)
									->first();			
						$detailexcel = ['type' => 'farmer_report','nama_ff' => $GetFF->name,'nama_petani' => $GetFarmer->name,
						'distribution_time' => $distribution_time, 'distribution_location' => $distribution_location, 'nama_desa' => $nama_desa];
						
						$nama_title = 'Cetak Excel Farmer Receipt'; 
					}else{
					$rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                	return response()->json($rslt, 404);
				}
			}
            
            

            if(count($GetPHAll)!=0){

                $get_amount_bag = 0;                                 
																
																
				$dataxx = [];
				$listxxx=array();

				foreach($GetPHAll as  $valphall){
						$GetPH = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                'planting_hole_details.amount','trees.tree_name','trees.tree_category',
                                'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'lahans.village',
                                'planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                                'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                                'planting_hole_surviellance.total_holes', 'planting_hole_surviellance.latitude', 
                                'planting_hole_surviellance.longitude','planting_hole_surviellance.is_validate',
                                'planting_hole_surviellance.validate_by','planting_hole_surviellance.is_dell', 
                                'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id'
                                )
                        ->leftjoin('planting_hole_surviellance', 'planting_hole_surviellance.ph_form_no', '=', 'planting_hole_details.ph_form_no')
                        ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)                                        
                        ->where('planting_hole_surviellance.ph_form_no','=',$valphall->ph_form_no)
						->orderBy('trees.tree_category', 'DESC')
                        // ->where('ph_form_no','=',$GetPH->ph_form_no)
                        ->get();

					// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH)];
					// array_push($listxxx, $dataxx);

					$datavalpohon = [];
					$listvalpohon=array();
					$datavalbag = [];
					$listvalbag=array(); 

					$datavaltemp = [];
					$listvaltemp=array();
					$datavalbag = [];
					$listvalbag=array();
					$looping = false;
					$amount_loop = 0; 
					$mount_total_temp = 0;
					$sisa = 0;
					$previous_category = '-';
					$x = 0; 
					$previous_code_ph = '-';
					$max = 0;
					$datamaxbagph = [];
					$listmaxbagph=array();

					foreach($GetPH as  $valpohon){

						$GetSosialisasiDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->where('planting_socializations.no_lahan','=',$valpohon->lahan_no)
									->first();

						$Desas = DB::table('desas')->where('kode_desa','=',$valpohon->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						
						$planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period->distribution_location;
						}

						$nama_ff = $valpohon->nama_ff;
						$nama_petani = $valpohon->nama_petani;
						$ph_form_no = $valpohon->ph_form_no;
						$lahan_no = $valpohon->lahan_no;
						$total_holes = $valpohon->total_holes;
						

						$new_name =$valpohon->tree_name;
						if (strripos($valpohon->tree_name, "Crops") !== false) {
							$new_name = substr($valpohon->tree_name,0,-8);
						}

						// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH),'nama_desa' => $nama_desa,
						// 'distribution_location' => $distribution_location,'distribution_time' => $distribution_time,
						// 'valpohon' => $new_name,'amount' => $valpohon->amount];
						// array_push($listxxx, $dataxx);

						if($valpohon->tree_category =='Pohon_Kayu'){
								$batas = $max_kayu;
								$pohon_kategori = 'Pohon_Kayu';

								if($valpohon->amount > $batas){
									$looping = true;
									if ($sisa != 0 && $previous_category==$valpohon->tree_category){
										$valsisainput = $max_kayu-$sisa;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$amount_loop = ceil($valpohon->amount - $valsisainput/$max_kayu);
										$mount_total_temp = $valpohon->amount - $valsisainput;
									}else{
										if($sisa != 0){
											$prv_ctg = $this->convertcategorytrees($previous_category);
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);
										}                                
										$amount_loop = ceil($valpohon->amount/$max_kayu);
										$mount_total_temp = $valpohon->amount;
									}  
								}else{
										$looping = false;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$datavaltemp = [];
										$listvaltemp=array();
								}
																																
						}else if($valpohon->tree_category =='Pohon_Buah'){
							$pohon_kategori = 'Pohon_Buah (MPTS)';
							if($valpohon->amount > $max_mpts){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_mpts-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_mpts);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{  
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                               
									$amount_loop = ceil($valpohon->amount/$max_mpts);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/6);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}else{
							$pohon_kategori = 'Crops';
							if($valpohon->amount > $max_crops){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_crops-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_crops);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{    
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                             
									$amount_loop = ceil($valpohon->amount/$max_crops);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/5);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}
							
						if( $looping == true){
							$nn = 1;
							for ($x = 1; $x <= $amount_loop; $x++) {
								$datavaltemp = [];
								$listvaltemp=array();
								$sisa=0;

								if($nn>$amount_loop){
									break;
								}

								if($valpohon->tree_category =='Pohon_Kayu'){
									if($mount_total_temp > $max_kayu){
												
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_kayu];
										array_push($listvaltemp, $datavaltemp);
										
										$mount_total_temp = $mount_total_temp - $max_kayu;

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}else{
										if($mount_total_temp == 0){
											break;
										}
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_kayu){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else if($valpohon->tree_category =='Pohon_Buah'){
									if($mount_total_temp > $max_mpts){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_mpts];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_mpts;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_mpts){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else{
									if($mount_total_temp > $max_crops){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_crops];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_crops;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);
										if($mount_total_temp == $max_crops){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
										
									}

								}

								$nn = $nn + 1;
							}
						}
					}

					// $prv_ctg = $this->convertcategorytrees($previous_category);
					// $datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
					// 'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
					// 'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];;
					// array_push($listvalbag, $datavalbag);
				
					$datavalfix = [];
					$listvalfix=array();
					$ii = 1;
					$countlist = count($listvalbag);
					foreach($listvalbag as  $valbag){
								
						$dataxx = ['no_bag'=>$ii.'/'.$countlist, 'nama_ff'=>$valbag['nama_ff'],'nama_petani'=>$valbag['nama_petani'],
						'distribution_time'=>$valbag['distribution_time'],'pohon_kategori'=>$valbag['pohon_kategori'],'nama_desa'=>$valbag['nama_desa'],
						'distribution_location'=>$valbag['distribution_location'],'listvaltemp'=>$valbag['listvaltemp'],
						'lahan_no'=>$valbag['lahan_no'],'total_holes'=>$valbag['total_holes']];
						// $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
						array_push($listxxx, $dataxx);

						$ii = $ii + 1;
					}
			
				}                   
                                    

				// var_dump($listxxx);

                $listvalbag = $listxxx;

				// var_dump($listvalbag);

                return view('cetakPlantingHoleLoadingPlan', compact('nama_title','listvalbag','detailexcel'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
	public function CetakExcelPackingPlan(Request $request){
        try{
			$getmax_kayu = $request->max_kayu;
			if($getmax_kayu){$max_kayu=$getmax_kayu;}
            else{$max_kayu=15;}
			$getmax_mpts = $request->max_mpts;
			if($getmax_mpts){$max_mpts=$getmax_mpts;}
            else{$max_mpts=6;}
			$getmax_crops = $request->max_crops;
			if($getmax_crops){$max_crops=$getmax_crops;}
            else{$max_crops=5;}

            // $typegetdata = $request->typegetdata;
			// $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
            $GetLahanNoSosialisasi = DB::table('planting_socializations')
									->join('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
									->where('planting_period.distribution_time','=',$request->date)
									->pluck('planting_socializations.no_lahan');

			$distribution_time = '-';
			if(count($GetLahanNoSosialisasi)!=0){
				
				$date=date_create($request->date);
				$distribution_time = date_format($date,"d-F-Y");

				$GetPHAll = DB::table('planting_hole_surviellance')
				->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
				'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
				'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
				'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
				'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
				'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
				->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
				->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
				->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
				->where('planting_hole_surviellance.is_dell','=',0)
				->wherein('planting_hole_surviellance.lahan_no',$GetLahanNoSosialisasi)
				// ->where('planting_hole_surviellance.user_id','=',$ff_no)
				->get();

			}else{
				$rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
			}
            
            

            if(count($GetPHAll)!=0){

                $get_amount_bag = 0;                                 
																
																
				$dataxx = [];
				$listxxx=array();

				foreach($GetPHAll as  $valphall){
						$GetPH = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                'planting_hole_details.amount','trees.tree_name','trees.tree_category',
                                'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'lahans.village',
                                'planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                                'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                                'planting_hole_surviellance.total_holes', 'planting_hole_surviellance.latitude', 
                                'planting_hole_surviellance.longitude','planting_hole_surviellance.is_validate',
                                'planting_hole_surviellance.validate_by','planting_hole_surviellance.is_dell', 
                                'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id'
                                )
                        ->leftjoin('planting_hole_surviellance', 'planting_hole_surviellance.ph_form_no', '=', 'planting_hole_details.ph_form_no')
                        ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)                                        
                        ->where('planting_hole_surviellance.ph_form_no','=',$valphall->ph_form_no)
						->orderBy('trees.tree_category', 'DESC')
                        // ->where('ph_form_no','=',$GetPH->ph_form_no)
                        ->get();

					// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH)];
					// array_push($listxxx, $dataxx);

					$datavalpohon = [];
					$listvalpohon=array();
					$datavalbag = [];
					$listvalbag=array(); 

					$datavaltemp = [];
					$listvaltemp=array();
					$datavalbag = [];
					$listvalbag=array();
					$looping = false;
					$amount_loop = 0; 
					$mount_total_temp = 0;
					$sisa = 0;
					$previous_category = '-';
					$x = 0; 
					$previous_code_ph = '-';
					$max = 0;
					$datamaxbagph = [];
					$listmaxbagph=array();

					foreach($GetPH as  $valpohon){

						$GetSosialisasiDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->where('planting_socializations.no_lahan','=',$valpohon->lahan_no)
									->first();

						$Desas = DB::table('desas')->where('kode_desa','=',$valpohon->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						
						$planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period->distribution_location;
						}

						$nama_ff = $valpohon->nama_ff;
						$nama_petani = $valpohon->nama_petani;
						$ph_form_no = $valpohon->ph_form_no;
						$lahan_no = $valpohon->lahan_no;
						$total_holes = $valpohon->total_holes;
						

						$new_name =$valpohon->tree_name;
						if (strripos($valpohon->tree_name, "Crops") !== false) {
							$new_name = substr($valpohon->tree_name,0,-8);
						}

						// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH),'nama_desa' => $nama_desa,
						// 'distribution_location' => $distribution_location,'distribution_time' => $distribution_time,
						// 'valpohon' => $new_name,'amount' => $valpohon->amount];
						// array_push($listxxx, $dataxx);

						if($valpohon->tree_category =='Pohon_Kayu'){
								$batas = $max_kayu;
								$pohon_kategori = 'Pohon_Kayu';

								if($valpohon->amount > $batas){
									$looping = true;
									if ($sisa != 0 && $previous_category==$valpohon->tree_category){
										$valsisainput = $max_kayu-$sisa;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$amount_loop = ceil($valpohon->amount - $valsisainput/$max_kayu);
										$mount_total_temp = $valpohon->amount - $valsisainput;
									}else{
										if($sisa != 0){
											$prv_ctg = $this->convertcategorytrees($previous_category);
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);
										}                                
										$amount_loop = ceil($valpohon->amount/$max_kayu);
										$mount_total_temp = $valpohon->amount;
									}  
								}else{
										$looping = false;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$datavaltemp = [];
										$listvaltemp=array();
								}
																																
						}else if($valpohon->tree_category =='Pohon_Buah'){
							$pohon_kategori = 'Pohon_Buah (MPTS)';
							if($valpohon->amount > $max_mpts){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_mpts-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_mpts);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{  
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                               
									$amount_loop = ceil($valpohon->amount/$max_mpts);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/6);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}else{
							$pohon_kategori = 'Crops';
							if($valpohon->amount > $max_crops){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_crops-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_crops);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{    
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                             
									$amount_loop = ceil($valpohon->amount/$max_crops);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/5);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}
							
						if( $looping == true){
							$nn = 1;
							for ($x = 1; $x <= $amount_loop; $x++) {
								$datavaltemp = [];
								$listvaltemp=array();
								$sisa=0;

								if($nn>$amount_loop){
									break;
								}

								if($valpohon->tree_category =='Pohon_Kayu'){
									if($mount_total_temp > $max_kayu){
												
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_kayu];
										array_push($listvaltemp, $datavaltemp);
										
										$mount_total_temp = $mount_total_temp - $max_kayu;

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}else{
										if($mount_total_temp == 0){
											break;
										}
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_kayu){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else if($valpohon->tree_category =='Pohon_Buah'){
									if($mount_total_temp > $max_mpts){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_mpts];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_mpts;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_mpts){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else{
									if($mount_total_temp > $max_crops){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_crops];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_crops;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);
										if($mount_total_temp == $max_crops){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
										
									}

								}

								$nn = $nn + 1;
							}
						}
					}

					// $prv_ctg = $this->convertcategorytrees($previous_category);
					// $datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
					// 'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
					// 'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];;
					// array_push($listvalbag, $datavalbag);
				
					$datavalfix = [];
					$listvalfix=array();
					$ii = 1;
					$countlist = count($listvalbag);
					foreach($listvalbag as  $valbag){
								
						$dataxx = ['no_bag'=>$ii.'/'.$countlist, 'nama_ff'=>$valbag['nama_ff'],'nama_petani'=>$valbag['nama_petani'],
						'distribution_time'=>$valbag['distribution_time'],'pohon_kategori'=>$valbag['pohon_kategori'],'nama_desa'=>$valbag['nama_desa'],
						'distribution_location'=>$valbag['distribution_location'],'listvaltemp'=>$valbag['listvaltemp'],
						'lahan_no'=>$valbag['lahan_no'],'total_holes'=>$valbag['total_holes']];
						// $datavalbag = ['no_bag'=>$x.'/'.$get_amount_bag, 'listvaltemp'=>$listvaltemp, 'qrcodelahan'=>$qrcodelahan, 'n'=>$n];
						array_push($listxxx, $dataxx);

						$ii = $ii + 1;
					}
			
				}                   
                                    

				// var_dump($listxxx);

				$nama_title = 'Cetak Excel Packing Plan Report'; 
                $listvalbag = $listxxx;

                return view('cetakPackingPlan', compact('nama_title','listvalbag', 'distribution_time'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
	public function CetakExcelShippingPlan(Request $request){
        try{
			$getmax_kayu = $request->max_kayu;
			if($getmax_kayu){$max_kayu=$getmax_kayu;}
            else{$max_kayu=15;}
			$getmax_mpts = $request->max_mpts;
			if($getmax_mpts){$max_mpts=$getmax_mpts;}
            else{$max_mpts=6;}
			$getmax_crops = $request->max_crops;
			if($getmax_crops){$max_crops=$getmax_crops;}
            else{$max_crops=5;}
            // $typegetdata = $request->typegetdata;
			// $planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
            $GetLahanNoSosialisasi = DB::table('planting_socializations')
									->join('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
									->where('planting_period.distribution_time','=',$request->date)
									->pluck('planting_socializations.no_lahan');

			$distribution_time = '-';
			if(count($GetLahanNoSosialisasi)!=0){
				
				$date=date_create($request->date);
				$distribution_time = date_format($date,"d-F-Y");

				$GetPHAll = DB::table('planting_hole_surviellance')
				->select('planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
				'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year','planting_hole_surviellance.total_holes',
				'planting_hole_surviellance.latitude', 'planting_hole_surviellance.longitude',
				'planting_hole_surviellance.is_validate','planting_hole_surviellance.validate_by',
				'planting_hole_surviellance.is_dell', 'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id',
				'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
				->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
				->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
				->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
				->where('planting_hole_surviellance.is_dell','=',0)
				->wherein('planting_hole_surviellance.lahan_no',$GetLahanNoSosialisasi)
				->orderBy('planting_hole_surviellance.user_id', 'asc')
				// ->where('planting_hole_surviellance.user_id','=',$ff_no)
				->get();

			}else{
				$rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
			}
            
            

            if(count($GetPHAll)!=0){

                $get_amount_bag = 0;                                 
																
																
				$dataxx = [];
				$listxxx=array();

				$qty_total = 0; 
				$ff_code_previous = '-';
				 
				$countmax = count($GetPHAll);
				$xxx = 0 ;
				$datafftemp = [];
				$listfftemp=array();
				foreach($GetPHAll as  $valphall){
						$GetPH = DB::table('planting_hole_details')
                        ->select('planting_hole_details.id','planting_hole_details.ph_form_no','planting_hole_details.tree_code',
                                'planting_hole_details.amount','trees.tree_name','trees.tree_category',
                                'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'lahans.village',
                                'planting_hole_surviellance.id','planting_hole_surviellance.lahan_no',
                                'planting_hole_surviellance.ph_form_no','planting_hole_surviellance.planting_year',
                                'planting_hole_surviellance.total_holes', 'planting_hole_surviellance.latitude', 
                                'planting_hole_surviellance.longitude','planting_hole_surviellance.is_validate',
                                'planting_hole_surviellance.validate_by','planting_hole_surviellance.is_dell', 
                                'planting_hole_surviellance.created_at', 'planting_hole_surviellance.user_id'
                                )
                        ->leftjoin('planting_hole_surviellance', 'planting_hole_surviellance.ph_form_no', '=', 'planting_hole_details.ph_form_no')
                        ->join('trees', 'trees.tree_code', '=', 'planting_hole_details.tree_code')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'planting_hole_surviellance.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'planting_hole_surviellance.user_id')
                        ->where('planting_hole_surviellance.is_dell','=',0)                                        
                        ->where('planting_hole_surviellance.ph_form_no','=',$valphall->ph_form_no)
						->orderBy('trees.tree_category', 'DESC')
                        // ->where('ph_form_no','=',$GetPH->ph_form_no)
                        ->get();

					// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH)];
					// array_push($listxxx, $dataxx);

					$datavalpohon = [];
					$listvalpohon=array();
					$datavalbag = [];
					$listvalbag=array(); 

					$datavaltemp = [];
					$listvaltemp=array();
					$datavalbag = [];
					$listvalbag=array();
					$looping = false;
					$amount_loop = 0; 
					$mount_total_temp = 0;
					$sisa = 0;
					$previous_category = '-';
					$x = 0; 
					$previous_code_ph = '-';
					$max = 0;
					$datamaxbagph = [];
					$listmaxbagph=array();

					foreach($GetPH as  $valpohon){

						$GetSosialisasiDetail = DB::table('planting_socializations')
									->select('planting_socializations.id','planting_socializations.no_lahan',
									'planting_socializations.farmer_no','planting_socializations.form_no','planting_socializations.planting_year',
									'planting_socializations.no_document', 'planting_socializations.ff_no','planting_socializations.validation','planting_socializations.validate_by',
									'planting_socializations.is_dell', 'planting_socializations.created_at')
									->where('planting_socializations.no_lahan','=',$valpohon->lahan_no)
									->first();

						$Desas = DB::table('desas')->where('kode_desa','=',$valpohon->village)->first();
						$nama_desa = '-';
						if($Desas){
							$nama_desa = $Desas->name;
						}
						
						$planting_period = DB::table('planting_period')->where('form_no','=',$GetSosialisasiDetail->form_no)->first();
						$distribution_time = '-';
						$distribution_location = '-';
						if($planting_period){
							// $distribution_time = $planting_period->distribution_time;
							$date=date_create($planting_period->distribution_time);
							$distribution_time = date_format($date,"d-F-Y");
							$distribution_location = $planting_period->distribution_location;
						}

						$nama_ff = $valpohon->nama_ff;
						$nama_petani = $valpohon->nama_petani;
						$ph_form_no = $valpohon->ph_form_no;
						$lahan_no = $valpohon->lahan_no;
						$total_holes = $valpohon->total_holes;
						

						$new_name =$valpohon->tree_name;
						if (strripos($valpohon->tree_name, "Crops") !== false) {
							$new_name = substr($valpohon->tree_name,0,-8);
						}

						// $dataxx = ['code' => $valphall->ph_form_no,'count' => count($GetPH),'nama_desa' => $nama_desa,
						// 'distribution_location' => $distribution_location,'distribution_time' => $distribution_time,
						// 'valpohon' => $new_name,'amount' => $valpohon->amount];
						// array_push($listxxx, $dataxx);

						if($valpohon->tree_category =='Pohon_Kayu'){
								$batas = $max_kayu;
								$pohon_kategori = 'Pohon_Kayu';

								if($valpohon->amount > $batas){
									$looping = true;
									if ($sisa != 0 && $previous_category==$valpohon->tree_category){
										$valsisainput = $max_kayu-$sisa;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$amount_loop = ceil($valpohon->amount - $valsisainput/$max_kayu);
										$mount_total_temp = $valpohon->amount - $valsisainput;
									}else{
										if($sisa != 0){
											$prv_ctg = $this->convertcategorytrees($previous_category);
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);
										}                                
										$amount_loop = ceil($valpohon->amount/$max_kayu);
										$mount_total_temp = $valpohon->amount;
									}  
								}else{
										$looping = false;
										$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
										array_push($listvaltemp, $datavaltemp);

										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);

										$datavaltemp = [];
										$listvaltemp=array();
								}
																																
						}else if($valpohon->tree_category =='Pohon_Buah'){
							$pohon_kategori = 'Pohon_Buah (MPTS)';
							if($valpohon->amount > $max_mpts){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_mpts-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_mpts);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{  
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                               
									$amount_loop = ceil($valpohon->amount/$max_mpts);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/6);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}else{
							$pohon_kategori = 'Crops';
							if($valpohon->amount > $max_crops){
								$looping = true;
								if ($sisa != 0 && $previous_category==$valpohon->tree_category){
									$valsisainput = $max_crops-$sisa;
									$datavaltemp = ['pohon' => $new_name,'amount' => $valsisainput];
									array_push($listvaltemp, $datavaltemp);

									$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
									'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
									'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
									array_push($listvalbag, $datavalbag);

									$amount_loop = ceil($valpohon->amount - $valsisainput/$max_crops);
									$mount_total_temp = $valpohon->amount - $valsisainput;
								}else{    
									if($sisa != 0){
										$prv_ctg = $this->convertcategorytrees($previous_category);
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}                             
									$amount_loop = ceil($valpohon->amount/$max_crops);
									$mount_total_temp = $valpohon->amount;
								} 
								// $amount_loop = ceil($valpohon->amount/5);
								// $mount_total_temp = $valpohon->amount;
							}else{
								$looping = false;
								$datavaltemp = ['pohon' => $new_name,'amount' => $valpohon->amount];
								array_push($listvaltemp, $datavaltemp);

								$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
								'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
								'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
								array_push($listvalbag, $datavalbag);

								$datavaltemp = [];
								$listvaltemp=array();
							}
						}
							
						if( $looping == true){
							$nn = 1;
							for ($x = 1; $x <= $amount_loop; $x++) {
								$datavaltemp = [];
								$listvaltemp=array();
								$sisa=0;

								if($nn>$amount_loop){
									break;
								}

								if($valpohon->tree_category =='Pohon_Kayu'){
									if($mount_total_temp > $max_kayu){
												
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_kayu];
										array_push($listvaltemp, $datavaltemp);
										
										$mount_total_temp = $mount_total_temp - $max_kayu;

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
									}else{
										if($mount_total_temp == 0){
											break;
										}
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_kayu){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else if($valpohon->tree_category =='Pohon_Buah'){
									if($mount_total_temp > $max_mpts){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_mpts];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_mpts;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);

										if($mount_total_temp == $max_mpts){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
									}

								}else{
									if($mount_total_temp > $max_crops){
										$datavaltemp = ['pohon' => $new_name,'amount' => $max_crops];
										array_push($listvaltemp, $datavaltemp);

										
										$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
										'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
										'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
										array_push($listvalbag, $datavalbag);
										$mount_total_temp = $mount_total_temp - $max_crops;
									}else{
										$datavaltemp = ['pohon' => $new_name,'amount' => $mount_total_temp];
										array_push($listvaltemp, $datavaltemp);
										if($mount_total_temp == $max_crops){
											$datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
											'pohon_kategori'=>$pohon_kategori,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
											'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];
											array_push($listvalbag, $datavalbag);

											$datavaltemp = [];
											$listvaltemp=array();
											$sisa=0;
										}else{
											$sisa = $mount_total_temp;
											$previous_category=$valpohon->tree_category;
										}
										
									}

								}

								$nn = $nn + 1;
							}
						}
					}

					// $prv_ctg = $this->convertcategorytrees($previous_category);
					// $datavalbag = ['ph_form_no'=>$ph_form_no,'nama_ff'=>$nama_ff,'nama_petani'=>$nama_petani,'distribution_time'=>$distribution_time,
					// 'pohon_kategori'=>$prv_ctg,'nama_desa'=>$nama_desa,'distribution_location'=>$distribution_location,
					// 'lahan_no'=>$lahan_no,'total_holes'=>$total_holes,'listvaltemp'=>$listvaltemp];;
					// array_push($listvalbag, $datavalbag);
					$GetLahanNoSosialisasi = DB::table('planting_socializations')
									->join('planting_period', 'planting_period.form_no', '=', 'planting_socializations.form_no')
									->where('planting_socializations.no_lahan','=',$valphall->lahan_no)
									->first();
									
					$distribution_time = '-';
					$distribution_location = '-';
					if($planting_period){
						$date=date_create($GetLahanNoSosialisasi->distribution_time);
						$distribution_time = date_format($date,"d-F-Y");
						$distribution_location = $GetLahanNoSosialisasi->distribution_location;
					}
					$nama_ff = $valphall->nama_ff;
				
					$datavalfix = [];
					$listvalfix=array();
					$ii = 1;
					$countlist = count($listvalbag);

					$xxx = $xxx+1;
					if($ff_code_previous == '-'){
						$ff_code_previous = $valphall->user_id;
					}

					if($valphall->user_id == $ff_code_previous){
						$qty_total = $qty_total+$countlist; 
					}else{
						$ff_code_previous = $valphall->user_id;
						$datafftemp = ['nama_ff'=>$nama_ff,'qty_total'=>$qty_total,'distribution_location'=>$distribution_location];
						array_push($listfftemp, $datafftemp);
					}
					if($xxx ==$countmax ){
						// var_dump('last');
						// $ff_code_previous = $valphall->user_id;
						$datafftemp = ['nama_ff'=>$nama_ff,'qty_total'=>$qty_total,'distribution_location'=>$distribution_location];
						array_push($listfftemp, $datafftemp);
						// var_dump($listfftemp);
					}
			
				}                   
                                    

				// var_dump($xxx);
				// var_dump($countmax);
				// var_dump($listfftemp);

				$nama_title = 'Cetak Excel Shipping Plan Report'; 
                $listvalbag = $listxxx;

                return view('cetakShippingPlan', compact('nama_title','listfftemp', 'distribution_time'));
            }else{
                $rslt =  $this->ResultReturn(404, 'doesnt match data', 'data tidak ada');
                return response()->json($rslt, 404);
            }
            // var_dump(count($GetLahanNotComplete));
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

	public function ConfigMaxValue(){

	}

	public function convertcategorytrees($val){
		if($val =='Pohon_Buah'){
			return 'Pohon_Buah (MPTS)';
		}else if($val =='Pohon_Kayu'){
			return 'Pohon_Kayu';
		}else{
			return 'Crops';
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
     *   path="/api/AddPlantingHole",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add PlantingHole",
     *   operationId="AddPlantingHole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add PlantingHole",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="total_holes", type="string", example="2021"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="farmer_signature", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon json decode tree_code dan amount"),
     *          ),
     *      )
     * )
     *
     */
    public function AddPlantingHole(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'lahan_no' => 'required|unique:planting_hole_surviellance', 
            'total_holes' => 'required', 
            'planting_year' => 'required',
            'farmer_signature' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{             
            
            $Lahan = DB::table('lahans')->where('lahan_no','=',$request->lahan_no)->first();
            
            // print_r($Lahan);
            if($Lahan){
                $year = Carbon::now()->format('Y');
                $ph_form_no = 'PH-'.$year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    PlantingHoleSurviellanceDetail::create([
                        'ph_form_no' => $ph_form_no,
                        'tree_code' => $val['tree_code'],
                        'amount' => $val['amount'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['amount'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['amount'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['amount'];
                    }
                }

                PlantingHoleSurviellance::create([
                    'ph_form_no' => $ph_form_no,
                    'planting_year' => $request->planting_year,
                    'total_holes' => $request->total_holes,
                    'farmer_signature' => $request->farmer_signature,
                    'gambar1' => $this->ReplaceNull($request->gambar1, 'string'),
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
                    'lahan_no' => $request->lahan_no,
                    'latitude' => $Lahan->latitude,
                    'longitude' => $Lahan->longitude,
                    'is_validate' => $validation,
                    'validate_by' => $validate_by,

                    'pohon_kayu' => $pohon_non_mpts,
                    'pohon_mpts' => $pohon_mpts,
                    'tanaman_bawah' => $pohon_bawah,
    
                    'user_id' => $request->user_id,
    
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
    
                    'is_dell' => 0
                ]);

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
     *   path="/api/AddPlantingHoleByFFNo",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add PlantingHole by FF",
     *   operationId="AddPlantingHoleByFFNo",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add PlantingHole by FF",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ff_no", type="string", example="FF0001"),
     *          ),
     *      )
     * )
     *
     */
    public function AddPlantingHoleByFFNo(Request $request){
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required',
            'ff_no' => 'required', 
            // 'total_holes' => 'required', 
            // 'planting_year' => 'required',
            // 'farmer_signature' => 'required',
            // 'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{      
			
			// $ph = DB::table('planting_hole_surviellance')->where('user_id','=',$request->ff_no)->get();

			// if(count($ph)!=0){
			// 	$rslt =  $this->ResultReturn(400, 'gagal', 'gagal');
			// 		return response()->json($rslt, 400);
			// }else{
				$sostam = DB::table('planting_socializations')->where('ff_no','=',$request->ff_no)->get();

				// var_dump($sostam);

				if(count($sostam)!=0){
					foreach($sostam as $valxxx){
						$phready = DB::table('planting_hole_surviellance')->where('lahan_no','=',$valxxx->no_lahan)->get();
						if(count($phready)==0){
							$Lahan = DB::table('lahans')->where('lahan_no','=',$valxxx->no_lahan)->first();

						// var_dump($valxxx->no_lahan);

							$year = Carbon::now()->format('Y');
							$ph_form_no = 'PH-'.$year.'-'.substr($valxxx->no_lahan,-10);
			
							$validation = 0;
							$validate_by = '-';
			
							$sostamdetail = DB::table('planting_details')->where('form_no','=',$valxxx->form_no)->get();

							$pohon_mpts = 0;
							$pohon_non_mpts = 0;
							$pohon_bawah = 0;
							$total = 0;
							foreach($sostamdetail as $val){
								PlantingHoleSurviellanceDetail::create([
									'ph_form_no' => $ph_form_no,
									'tree_code' => $val->tree_code,
									'amount' => $val->amount,
					
									'created_at'=>Carbon::now(),
									'updated_at'=>Carbon::now()
								]);
			
								$trees_get = DB::table('trees')->where('tree_code','=',$val->tree_code)->first();
			
								if( $trees_get->tree_category == "Pohon_Buah"){
									$pohon_mpts = $pohon_mpts + $val->amount;
								}else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
									$pohon_bawah = $pohon_bawah + $val->amount;
								}else{
									$pohon_non_mpts = $pohon_non_mpts + $val->amount;
								}

								$total = $total + $val->amount;
							}

							PlantingHoleSurviellance::create([
								'ph_form_no' => $ph_form_no,
								'planting_year' => $valxxx->planting_year,
								'total_holes' => $total,
								'farmer_signature' => $this->ReplaceNull($request->farmer_signature, 'string'),
								'gambar1' => $this->ReplaceNull($request->gambar1, 'string'),
								'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
								'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
								'lahan_no' => $valxxx->no_lahan,
								'latitude' => $Lahan->latitude,
								'longitude' => $Lahan->longitude,
								'is_validate' => $validation,
								'validate_by' => $validate_by,
			
								'pohon_kayu' => $pohon_non_mpts,
								'pohon_mpts' => $pohon_mpts,
								'tanaman_bawah' => $pohon_bawah,
				
								'user_id' => $request->ff_no,
				
								'created_at'=>Carbon::now(),
								'updated_at'=>Carbon::now(),
				
								'is_dell' => 0
							]);
			
							DB::commit();
						}

						

					}

					$rslt =  $this->ResultReturn(200, 'success', 'success');
					return response()->json($rslt, 200); 
				}else{
					$rslt =  $this->ResultReturn(400, 'gagal', 'gagal');
					return response()->json($rslt, 400);
				}
			// }

			

            
        }catch (\Exception $ex){
            DB::rollback();
            $rslt =  $this->ResultReturn(400, 'gagal',$ex);
            return response()->json($rslt, 400);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdatePlantingHole",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update PlantingHole",
     *   operationId="UpdatePlantingHole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update PlantingHole",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ph_form_no", type="string", example="PH-2021-0000001"),
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="total_holes", type="string", example="2021"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="farmer_signature", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePlantingHole(Request $request){
        $validator = Validator::make($request->all(), [
            'ph_form_no' => 'required',
            'user_id' => 'required',
            'lahan_no' => 'required', 
            'total_holes' => 'required', 
            'planting_year' => 'required',
            'farmer_signature' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        }  
        
        DB::beginTransaction();

        try{            
            
            $form_no_old = $request->ph_form_no;
            $Lahan = DB::table('lahans')->where('lahan_no','=',$request->lahan_no)->first();
            $planting_hole_surviellance = DB::table('planting_hole_surviellance')->where('ph_form_no','=',$form_no_old)->first();
            
            if($planting_hole_surviellance){
                $year = Carbon::now()->format('Y');
                // $form_no = 'PH-'.$year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                PlantingHoleSurviellance::where('ph_form_no', '=', $form_no_old)
                ->update([
                    // 'form_no' => $form_no,
                    'planting_year' => $request->planting_year,
                    'total_holes' => $request->total_holes,
                    'farmer_signature' => $request->farmer_signature,
                    'gambar1' => $this->ReplaceNull($request->gambar1, 'string'),
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
                    'lahan_no' => $request->lahan_no,
                    'latitude' => $Lahan->latitude,
                    'longitude' => $Lahan->longitude,
                    'is_validate' => $validation,
                    'validate_by' => $validate_by,
    
                    'user_id' => $request->user_id,
    
                    'updated_at'=>Carbon::now(),
    
                    // 'is_dell' => 0
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
     *   path="/api/UpdatePlantingHoleAll",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update PlantingHole All",
     *   operationId="UpdatePlantingHoleAll",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update PlantingHole All",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ph_form_no", type="string", example="PH-2021-0000001"),
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="total_holes", type="string", example="2021"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="farmer_signature", type="string", example="-"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon json decode tree_code dan amount"),
     *              @SWG\Property(property="gambar1", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePlantingHoleAll(Request $request){
        $validator = Validator::make($request->all(), [
            'ph_form_no' => 'required',
            'user_id' => 'required',
            'lahan_no' => 'required', 
            'total_holes' => 'required', 
            'planting_year' => 'required',
            'farmer_signature' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        }  
        
        DB::beginTransaction();

        try{            
            
            $form_no_old = $request->ph_form_no;
            $Lahan = DB::table('lahans')->where('lahan_no','=',$request->lahan_no)->first();
            $planting_hole_surviellance = DB::table('planting_hole_surviellance')->where('ph_form_no','=',$form_no_old)->first();
            
            if($planting_hole_surviellance){
                $year = Carbon::now()->format('Y');
                // $form_no = 'PH-'.$year.'-'.substr($request->lahan_no,-10);

                DB::table('planting_hole_details')->where('ph_form_no', $form_no_old)->delete();

                
                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    PlantingHoleSurviellanceDetail::create([
                        'ph_form_no' => $form_no_old,
                        'tree_code' => $val['tree_code'],
                        'amount' => $val['amount'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['amount'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['amount'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['amount'];
                    }
                }

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                PlantingHoleSurviellance::where('ph_form_no', '=', $form_no_old)
                ->update([
                    // 'form_no' => $form_no,
                    'planting_year' => $request->planting_year,
                    'total_holes' => $request->total_holes,
                    'farmer_signature' => $request->farmer_signature,
                    'gambar1' => $this->ReplaceNull($request->gambar1, 'string'),
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
                    'lahan_no' => $request->lahan_no,
                    'latitude' => $Lahan->latitude,
                    'longitude' => $Lahan->longitude,
                    'is_validate' => $validation,
                    'validate_by' => $validate_by,
                    'pohon_kayu' => $pohon_non_mpts,
                    'pohon_mpts' => $pohon_mpts,
                    'tanaman_bawah' => $pohon_bawah,
    
                    'user_id' => $request->user_id,
    
                    'updated_at'=>Carbon::now(),
    
                    // 'is_dell' => 0
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
     *   path="/api/UpdatePohonPlantingHole",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Pohon PlantingHole",
     *   operationId="UpdatePohonPlantingHole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Pohon PlantingHole",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ph_form_no", type="string", example="SO-2021-0000001"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon bosku"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePohonPlantingHole(Request $request){
        $validator = Validator::make($request->all(), [
            'ph_form_no' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{
             
            
            $form_no_old = $request->ph_form_no;
            $list_pohon = $request->list_pohon;
            $planting_hole_surviellance = DB::table('planting_hole_surviellance')->where('ph_form_no','=',$form_no_old)->first();
            
            if($planting_hole_surviellance){
                
                DB::table('planting_hole_details')->where('ph_form_no', $form_no_old)->delete();

                
                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    PlantingHoleSurviellanceDetail::create([
                        'ph_form_no' => $form_no_old,
                        'tree_code' => $val['tree_code'],
                        'amount' => $val['amount'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['amount'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['amount'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['amount'];
                    }
                }

                PlantingHoleSurviellance::where('ph_form_no', '=', $form_no_old)
                ->update([
                    // 'form_no' => $form_no,
                    'pohon_kayu' => $pohon_non_mpts,
                    'pohon_mpts' => $pohon_mpts,
                    'tanaman_bawah' => $pohon_bawah,
    
                    'updated_at'=>Carbon::now(),
    
                    // 'is_dell' => 0
                ]);

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
     *   path="/api/SoftDeletePlantingHole",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="SoftDelete PlantingHole",
     *   operationId="SoftDeletePlantingHole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="SoftDelete PlantingHole",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ph_form_no", type="string", example="SO-2021-0000001"),
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeletePlantingHole(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $form_no_old = $request->ph_form_no;
            $planting_hole_surviellance = DB::table('planting_hole_surviellance')->where('ph_form_no','=',$form_no_old)->first();
            
            if($planting_hole_surviellance){

                PlantingHoleSurviellance::where('ph_form_no', '=', $form_no_old)
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
     *   path="/api/ValidatePlantingHole",
	 *   tags={"PlantingHole"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Validate PlantingHole",
     *   operationId="ValidatePlantingHole",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Validate PlantingHole",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="ph_form_no", type="string", example="SO-2021-0000001"),
     *              @SWG\Property(property="validate_by", type="string", example="00-11010"),
     *          ),
     *      )
     * )
     *
     */
    public function ValidatePlantingHole(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'ph_form_no' => 'required',
                'validate_by' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $form_no_old = $request->ph_form_no;
            $planting_hole_surviellance = DB::table('planting_hole_surviellance')->where('ph_form_no','=',$form_no_old)->first();
            
            if($planting_hole_surviellance){

                PlantingHoleSurviellance::where('ph_form_no', '=', $form_no_old)
                ->update([    
                    'updated_at'=>Carbon::now(),
                    'validate_by' => $request->validate_by,    
                    'is_validate' => 1
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
