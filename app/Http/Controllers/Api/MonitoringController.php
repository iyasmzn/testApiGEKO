<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\Monitoring;
use App\MonitoringDetail;
use App\Monitoring2;
use App\Monitoring2Detail;

class MonitoringController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetMonitoringFF",
     *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring FF",
     *   operationId="GetMonitoringFF",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ff_no",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoringFF(Request $request){
        $ff_no = $request->ff_no;
        try{
           
                $GetmonitoringFF = DB::table('monitoring')
                    ->select('monitoring.id','monitoring.lahan_no',
                    'monitoring.monitoring_no','monitoring.planting_year','monitoring.planting_date',
                    'monitoring.is_validate','monitoring.validate_by','monitoring.lahan_condition',
                    'monitoring.qty_kayu','monitoring.qty_mpts','monitoring.qty_crops',
                    'monitoring.is_dell', 'monitoring.created_at',  'monitoring.user_id as ff_no',                    
                    'monitoring.gambar1','monitoring.gambar2','monitoring.gambar3',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                    ->where('monitoring.is_dell','=',0)
                    ->where('monitoring.user_id','=',$ff_no)
                    ->get();

                if(count($GetmonitoringFF)!=0){ 
                    $count = DB::table('monitoring')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                        ->where('monitoring.is_dell','=',0)
                        ->where('monitoring.user_id','=',$ff_no)
                        ->count();
                    
                    $data = ['count'=>$count, 'data'=>$GetmonitoringFF];
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
     *   path="/api/GetMonitoringAdmin",
     *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring Admin",
     *   operationId="GetMonitoringAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="typegetdata",in="query",required=true, type="string"),
     *      @SWG\Parameter(name="ff",in="query",required=true, type="string"),
     * )
     */
    public function GetMonitoringAdmin(Request $request){
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
                    $GetPH = DB::table('monitoring')
                    ->select('monitoring.id','monitoring.lahan_no','monitoring.farmer_no',
                    'monitoring.monitoring_no','monitoring.planting_year','monitoring.planting_date',
                    'monitoring.is_validate','monitoring.validate_by','monitoring.lahan_condition',
                    'monitoring.qty_kayu', 'monitoring.qty_mpts', 'monitoring.qty_crops',
                    'monitoring.is_dell', 'monitoring.created_at', 'monitoring.user_id',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                    ->where('monitoring.is_dell','=',0)                                        
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    // ->where('monitoring.user_id','=',$ff_no)
                    ->get();

                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetPH = DB::table('monitoring')
                    ->select('monitoring.id','monitoring.lahan_no','monitoring.farmer_no',
                    'monitoring.monitoring_no','monitoring.planting_year','monitoring.planting_date',
                    'monitoring.is_validate','monitoring.validate_by','monitoring.lahan_condition',
                    'monitoring.qty_kayu', 'monitoring.qty_mpts', 'monitoring.qty_crops',
                    'monitoring.is_dell', 'monitoring.created_at', 'monitoring.user_id',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                    ->where('monitoring.is_dell','=',0)
                    ->wherein('monitoring.user_id',$ffdecode)
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
                    $dataval = ['id'=>$val->id,'lahan_no'=>$val->lahan_no, 'monitoring_no'=>$val->monitoring_no,
                    'planting_year'=>$val->planting_year, 'ff_no' => $val->user_id, 'is_validate' => $val->is_validate, 
                    'nama_ff'=>$val->nama_ff,'validate_by'=>$val->validate_by,  'nama_petani'=>$val->nama_petani, 'lahan_condition'=>$val->lahan_condition,
                    'status' => $status, 'is_dell' => $val->is_dell, 'created_at' => $val->created_at];
                    array_push($listval, $dataval);
                }

                
                // var_dump($listval);

                if(count($listval)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('monitoring')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                        ->where('monitoring.is_dell','=',0)                                       
                        ->where('lahans.mu_no','like',$mu)
                        ->where('lahans.target_area','like',$ta)
                        ->where('lahans.village','like',$village)
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$ff));

                        $count = DB::table('monitoring')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                        ->where('monitoring.is_dell','=',0) 
                        ->wherein('monitoring.user_id',$ffdecode)
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
     *   path="/api/GetMonitoringDetail",
     *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring Detail",
     *   operationId="GetMonitoringDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="monitoring_no",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoringDetail(Request $request){
        $monitoring_no = $request->monitoring_no;
        try{
           
                $GetMonitoringDetail = DB::table('monitoring')
                    ->select('monitoring.id','monitoring.lahan_no','monitoring.farmer_no',
                    'monitoring.monitoring_no','monitoring.planting_year','monitoring.planting_date',
                    'monitoring.is_validate','monitoring.validate_by','monitoring.lahan_condition',
                    'monitoring.qty_kayu','monitoring.qty_mpts','monitoring.qty_crops',
                    'monitoring.gambar1','monitoring.gambar2','monitoring.gambar3',
                    'monitoring.is_dell', 'monitoring.created_at',  'monitoring.user_id as ff_no',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'monitoring.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring.user_id')
                    ->where('monitoring.monitoring_no','=',$monitoring_no)
                    ->first();


                if($GetMonitoringDetail){ 
                    $GetMonitoringDetailList = DB::table('monitoring_detail')
                        ->select('monitoring_detail.id',
                        'monitoring_detail.monitoring_no','monitoring_detail.tree_code','trees.tree_category',
                        'monitoring_detail.qty','monitoring_detail.qty as amount', 'monitoring_detail.status', 'monitoring_detail.condition', 
                        'monitoring_detail.planting_date','monitoring_detail.created_at',
                        'trees.tree_name as tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'monitoring_detail.tree_code')
                        ->where('monitoring_detail.monitoring_no','=',$monitoring_no)
                        ->get();
                    
                    $count_list_pohon = count($GetMonitoringDetailList); 
                        
                    // var_dump($GetMonitoringDetail);
                    
                    $data = ['list_detail'=>$GetMonitoringDetailList,'count_list_pohon'=>$count_list_pohon, 'data'=>$GetMonitoringDetail];
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
     *   path="/api/GetMonitoringDetailFFNo",
     *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring Detail FFNo",
     *   operationId="GetMonitoringDetailFFNo",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoringDetailFFNo(Request $request){
        $user_id = $request->user_id;
        try{
           
                $GetPHDetail = DB::table('monitoring')
                    ->where('monitoring.user_id','=',$user_id)
                    ->first();

                if($GetPHDetail){ 
                    $GetPHDetailList = DB::table('monitoring_detail')
                        ->select('monitoring_detail.id',
                        'monitoring_detail.monitoring_no','monitoring_detail.tree_code','trees.tree_category',
                        'monitoring_detail.qty', 'monitoring_detail.status', 'monitoring_detail.condition', 
                        'monitoring_detail.planting_date','monitoring_detail.created_at',
                        'trees.tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'monitoring_detail.tree_code')
                        ->leftjoin('monitoring', 'monitoring.monitoring_no', '=', 'monitoring_detail.monitoring_no')
                        // ->where('planting_hole_surviellance.is_dell','=',0)
                        ->where('monitoring.user_id','=',$user_id)
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

    /**
     * @SWG\Get(
     *   path="/api/GetMonitoringTest",
     *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="GetMonitoringTest",
     *   operationId="GetMonitoringTest",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="monitoring_no",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoringTest(Request $request){
        $monitoring_no = $request->monitoring_no;
        try{
           
                $GetMonitoringDetail = DB::table('planting_socializations')
                    ->pluck('form_no');


                if($GetMonitoringDetail){                         
                    $data = [ 'datadetail'=>$GetMonitoringDetail];
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
     * @SWG\Post(
     *   path="/api/AddMonitoring",
	 *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Monitoring",
     *   operationId="AddMonitoring",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Monitoring",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="planting_date", type="string", example="2021-10-10"),
     *              @SWG\Property(property="lahan_condition", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="-"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar3", type="string", example="Nullable"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon json decode tree_code, qty, status dll"),
     *          ),
     *      )
     * )
     *
     */
    public function AddMonitoring(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'lahan_no' => 'required|unique:monitoring', 
            'farmer_no' => 'required', 
            'planting_date' => 'required', 
            'planting_year' => 'required',
            'lahan_condition' => 'required',
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
                $monitoring_no = 'MO1-'.$request->planting_year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;
                
                // var_dump ($request->list_pohon);
                // 'monitoring_no', 'tree_code', 'qty','status','condition','planting_date',
                foreach($request->list_pohon as $val){
                    MonitoringDetail::create([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }
                // var_dump ($pohon_mpts);
            //     'monitoring_no', 'planting_year','planting_date', 'farmer_no', 'lahan_no',
            //     'qty_kayu', 'qty_mpts',  'qty_crops',  'lahan_condition',  'user_id', 
            //    'validation', 'validate_by', 'created_at','updated_at','is_dell'
                Monitoring::create([
                    'monitoring_no' => $monitoring_no,
                    'planting_year' => $request->planting_year,
                    'planting_date' => $request->planting_date,
                    'farmer_no' => $request->farmer_no,
                    'lahan_no' => $request->lahan_no,
                    'lahan_condition' => $request->lahan_condition,
                    'gambar1' => $request->gambar1,
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
                    'is_validate' => $validation,
                    'validate_by' => $validate_by,

                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
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
     *   path="/api/UpdateMonitoring",
	 *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Monitoring",
     *   operationId="UpdateMonitoring",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Monitoring",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="planting_date", type="string", example="2021-10-10"),
     *              @SWG\Property(property="lahan_condition", type="string", example="-"),
     *              @SWG\Property(property="list_pohon", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="-"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar3", type="string", example="Nullable"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdateMonitoring(Request $request){
        $validator = Validator::make($request->all(), [
            'monitoring_no' => 'required',
            'user_id' => 'required',
            'lahan_no' => 'required', 
            'farmer_no' => 'required', 
            'planting_date' => 'required', 
            'planting_year' => 'required',
            'lahan_condition' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        }  
        
        DB::beginTransaction();

        try{            
            
            $monitoring_no = $request->monitoring_no;
            // $Lahan = DB::table('lahans')->where('lahan_no','=',$request->lahan_no)->first();
            $monitoring = DB::table('monitoring')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){
                $year = Carbon::now()->format('Y');
                // $form_no = 'PH-'.$year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                
                DB::table('monitoring_detail')->where('monitoring_no', $monitoring_no)->delete();

                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    MonitoringDetail::create([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }

                

                Monitoring::where('monitoring_no', '=', $monitoring_no)
                ->update([
                    'planting_year' => $request->planting_year,
                    'planting_date' => $request->planting_date,
                    'farmer_no' => $request->farmer_no,
                    'lahan_no' => $request->lahan_no,
                    'lahan_condition' => $request->lahan_condition,
                    'gambar1' => $request->gambar1,
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),

                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
                    'user_id' => $request->user_id,
    
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
     *   path="/api/UpdatePohonMonitoring",
	 *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Pohon Monitoring",
     *   operationId="UpdatePohonMonitoring",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Pohon Monitoring",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon tree_code, qty, status dll"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePohonMonitoring(Request $request){
        $validator = Validator::make($request->all(), [
            'monitoring_no' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{
             
            
            $monitoring_no = $request->monitoring_no;
            $list_pohon = $request->list_pohon;
            $monitoring = DB::table('monitoring')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){
                
                DB::table('monitoring_detail')->where('monitoring_no', $monitoring_no)->delete();

                
                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    MonitoringDetail::where('monitoring_no', '=', $monitoring_no)
                    ->update([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }

                Monitoring::where('monitoring_no', '=', $monitoring_no)
                ->update([
                    // 'form_no' => $form_no,
                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
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
     *   path="/api/SoftDeleteMonitoring",
	 *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="SoftDelete Monitoring",
     *   operationId="SoftDeleteMonitoring",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="SoftDelete Monitoring",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeleteMonitoring(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'monitoring_no' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $monitoring_no = $request->monitoring_no;
            $monitoring = DB::table('monitoring')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){

                Monitoring::where('monitoring_no', '=', $monitoring_no)
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
     *   path="/api/ValidateMonitoring",
	 *   tags={"Monitoring"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Validate Monitoring",
     *   operationId="ValidateMonitoring",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Validate Monitoring",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="validate_by", type="string", example="00-11010"),
     *          ),
     *      )
     * )
     *
     */
    public function ValidateMonitoring(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'monitoring_no' => 'required',
                'validate_by' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $monitoring_no = $request->monitoring_no;
            $monitoring = DB::table('monitoring')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){

                Monitoring::where('monitoring_no', '=', $monitoring_no)
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

    /**
     * @SWG\Get(
     *   path="/api/GetMonitoring2FF",
     *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring 2 FF",
     *   operationId="GetMonitoring2FF",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="ff_no",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoring2FF(Request $request){
        $ff_no = $request->ff_no;
        try{
           
                $GetmonitoringFF = DB::table('monitoring_2')
                    ->select('monitoring_2.id','monitoring_2.lahan_no',
                    'monitoring_2.monitoring_no','monitoring_2.planting_year','monitoring_2.planting_date',
                    'monitoring_2.is_validate','monitoring_2.validate_by','monitoring_2.lahan_condition',
                    'monitoring_2.qty_kayu','monitoring_2.qty_mpts','monitoring_2.qty_crops',
                    'monitoring_2.is_dell', 'monitoring_2.created_at',  'monitoring_2.user_id as ff_no',                    
                    'monitoring_2.gambar1','monitoring_2.gambar2','monitoring_2.gambar3',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                    ->where('monitoring_2.is_dell','=',0)
                    ->where('monitoring_2.user_id','=',$ff_no)
                    ->get();

                if(count($GetmonitoringFF)!=0){ 
                    $count = DB::table('monitoring_2')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                        ->where('monitoring_2.is_dell','=',0)
                        ->where('monitoring_2.user_id','=',$ff_no)
                        ->count();
                    
                    $data = ['count'=>$count, 'data'=>$GetmonitoringFF];
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
     *   path="/api/GetMonitoring2Admin",
     *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring 2 Admin",
     *   operationId="GetMonitoring2Admin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="typegetdata",in="query",required=true, type="string"),
     *      @SWG\Parameter(name="ff",in="query",required=true, type="string"),
     * )
     */
    public function GetMonitoring2Admin(Request $request){
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
                    $GetPH = DB::table('monitoring_2')
                    ->select('monitoring_2.id','monitoring_2.lahan_no','monitoring_2.farmer_no',
                    'monitoring_2.monitoring_no','monitoring_2.planting_year','monitoring_2.planting_date',
                    'monitoring_2.is_validate','monitoring_2.validate_by','monitoring_2.lahan_condition',
                    'monitoring_2.qty_kayu', 'monitoring_2.qty_mpts', 'monitoring_2.qty_crops',
                    'monitoring_2.is_dell', 'monitoring_2.created_at', 'monitoring_2.user_id',
                    'lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.jarak_lahan','lahans.opsi_pola_tanam','lahans.access_to_lahan',
                    'lahans.planting_area','lahans.land_area','lahans.pohon_kayu','lahans.pohon_mpts','lahans.tanaman_bawah',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                    ->where('monitoring_2.is_dell','=',0)                                        
                    ->where('lahans.mu_no','like',$mu)
                    ->where('lahans.target_area','like',$ta)
                    ->where('lahans.village','like',$village)
                    // ->where('monitoring.user_id','=',$ff_no)
                    ->get();

                }else{
                    $ffdecode = (explode(",",$ff));

                    $GetPH = DB::table('monitoring_2')
                    ->select('monitoring_2.id','monitoring_2.lahan_no','monitoring_2.farmer_no',
                    'monitoring_2.monitoring_no','monitoring_2.planting_year','monitoring_2.planting_date',
                    'monitoring_2.is_validate','monitoring_2.validate_by','monitoring_2.lahan_condition',
                    'monitoring_2.qty_kayu', 'monitoring_2.qty_mpts', 'monitoring_2.qty_crops',
                    'monitoring_2.is_dell', 'monitoring_2.created_at', 'monitoring_2.user_id',
                    'lahans.longitude','lahans.latitude','lahans.coordinate',
                    'lahans.jarak_lahan','lahans.opsi_pola_tanam','lahans.access_to_lahan',
                    'lahans.planting_area','lahans.land_area','lahans.pohon_kayu','lahans.pohon_mpts','lahans.tanaman_bawah',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                    ->where('monitoring_2.is_dell','=',0)   
                    ->wherein('monitoring_2.user_id',$ffdecode)
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
                    $dataval = ['id'=>$val->id,'lahan_no'=>$val->lahan_no, 'monitoring_no'=>$val->monitoring_no,
                    'planting_year'=>$val->planting_year, 'ff_no' => $val->user_id, 'is_validate' => $val->is_validate, 
                    'nama_ff'=>$val->nama_ff,'validate_by'=>$val->validate_by,  'nama_petani'=>$val->nama_petani, 'lahan_condition'=>$val->lahan_condition,
                    'longitude'=>$val->longitude,'latitude'=>$val->latitude,'coordinate'=>$val->coordinate,
                    'planting_area'=>$val->planting_area,'land_area'=>$val->land_area,'pohon_kayu'=>$val->pohon_kayu,
                    'pohon_mpts'=>$val->pohon_mpts,'tanaman_bawah'=>$val->tanaman_bawah,
                    'jarak_lahan'=>$val->jarak_lahan,'opsi_pola_tanam'=>$val->opsi_pola_tanam,'access_to_lahan'=>$val->access_to_lahan,
                    'status' => $status, 'is_dell' => $val->is_dell, 'created_at' => $val->created_at];
                    array_push($listval, $dataval);
                }

                
                // var_dump($listval);

                if(count($listval)!=0){ 
                    if($typegetdata == 'all'){
                        $count = DB::table('monitoring_2')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                        ->where('monitoring_2.is_dell','=',0)                                       
                        ->where('lahans.mu_no','like',$mu)
                        ->where('lahans.target_area','like',$ta)
                        ->where('lahans.village','like',$village)
                        ->count();
                    }else{
                        $ffdecode = (explode(",",$ff));

                        $count = DB::table('monitoring_2')
                        ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                        ->leftjoin('farmers', 'farmers.farmer_no', '=', 'lahans.farmer_no')
                        ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                        ->where('monitoring_2.is_dell','=',0) 
                        ->wherein('monitoring_2.user_id',$ffdecode)
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
     *   path="/api/GetMonitoring2Detail",
     *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring 2 Detail",
     *   operationId="GetMonitoring2Detail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="monitoring_no",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoring2Detail(Request $request){
        $monitoring_no = $request->monitoring_no;
        try{
           
                $GetMonitoringDetail = DB::table('monitoring_2')
                    ->select('monitoring_2.id','monitoring_2.lahan_no','monitoring_2.farmer_no',
                    'monitoring_2.monitoring_no','monitoring_2.planting_year','monitoring_2.planting_date',
                    'monitoring_2.is_validate','monitoring_2.validate_by','monitoring_2.lahan_condition',
                    'monitoring_2.qty_kayu','monitoring_2.qty_mpts','monitoring_2.qty_crops',
                    'monitoring_2.gambar1','monitoring_2.gambar2','monitoring_2.gambar3',
                    'monitoring_2.is_dell', 'monitoring_2.created_at',  'monitoring_2.user_id as ff_no',
                    'farmers.name as nama_petani', 'field_facilitators.name as nama_ff', 'monitoring_2.user_id')
                    ->leftjoin('lahans', 'lahans.lahan_no', '=', 'monitoring_2.lahan_no')
                    ->leftjoin('farmers', 'farmers.farmer_no', '=', 'monitoring_2.farmer_no')
                    ->leftjoin('field_facilitators', 'field_facilitators.ff_no', '=', 'monitoring_2.user_id')
                    ->where('monitoring_2.monitoring_no','=',$monitoring_no)
                    ->first();


                if($GetMonitoringDetail){ 
                    $GetMonitoringDetailList = DB::table('monitoring_2_detail')
                        ->select('monitoring_2_detail.id',
                        'monitoring_2_detail.monitoring_no','monitoring_2_detail.tree_code','trees.tree_category',
                        'monitoring_2_detail.qty','monitoring_2_detail.qty as amount', 'monitoring_2_detail.status', 'monitoring_2_detail.condition', 
                        'monitoring_2_detail.planting_date','monitoring_2_detail.created_at',
                        'trees.tree_name as tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'monitoring_2_detail.tree_code')
                        ->where('monitoring_2_detail.monitoring_no','=',$monitoring_no)
                        ->get();
                    
                    $count_list_pohon = count($GetMonitoringDetailList); 
                        
                    // var_dump($GetMonitoringDetail);
                    
                    $data = ['list_detail'=>$GetMonitoringDetailList,'count_list_pohon'=>$count_list_pohon, 'data'=>$GetMonitoringDetail];
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
     *   path="/api/GetMonitoring2DetailFFNo",
     *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Monitoring 2 Detail FFNo",
     *   operationId="GetMonitoring2DetailFFNo",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     * )
     */
    public function GetMonitoring2DetailFFNo(Request $request){
        $user_id = $request->user_id;
        try{
           
                $GetPHDetail = DB::table('monitoring_2')
                    ->where('monitoring_2.user_id','=',$user_id)
                    ->first();

                if($GetPHDetail){ 
                    $GetPHDetailList = DB::table('monitoring_2_detail')
                        ->select('monitoring_2_detail.id',
                        'monitoring_2_detail.monitoring_no','monitoring_2_detail.tree_code','trees.tree_category',
                        'monitoring_2_detail.qty', 'monitoring_2_detail.status', 'monitoring_2_detail.condition', 
                        'monitoring_2_detail.planting_date','monitoring_2_detail.created_at',
                        'trees.tree_name')
                        ->leftjoin('trees', 'trees.tree_code', '=', 'monitoring_2_detail.tree_code')
                        ->leftjoin('monitoring_2', 'monitoring_2.monitoring_no', '=', 'monitoring_2_detail.monitoring_no')
                        // ->where('planting_hole_surviellance.is_dell','=',0)
                        ->where('monitoring_2.user_id','=',$user_id)
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

    /**
     * @SWG\Post(
     *   path="/api/ValidateMonitoring2",
	 *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Validate Monitoring 2",
     *   operationId="ValidateMonitoring2",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Validate Monitoring 2",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="validate_by", type="string", example="00-11010"),
     *          ),
     *      )
     * )
     *
     */
    public function ValidateMonitoring2(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'monitoring_no' => 'required',
                'validate_by' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $monitoring_no = $request->monitoring_no;
            $monitoring = DB::table('monitoring_2')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){

                Monitoring2::where('monitoring_no', '=', $monitoring_no)
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

    /**
     * @SWG\Post(
     *   path="/api/AddMonitoring2",
	 *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Monitoring 2",
     *   operationId="AddMonitoring2",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Monitoring 2",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="planting_date", type="string", example="2021-10-10"),
     *              @SWG\Property(property="lahan_condition", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="-"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar3", type="string", example="Nullable"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon json decode tree_code, qty, status dll"),
     *          ),
     *      )
     * )
     *
     */
    public function AddMonitoring2(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'lahan_no' => 'required|unique:monitoring', 
            'farmer_no' => 'required', 
            'planting_date' => 'required', 
            'planting_year' => 'required',
            'lahan_condition' => 'required',
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
                $monitoring_no = 'MO2-'.$request->planting_year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;
                
                // var_dump ($request->list_pohon);
                // 'monitoring_no', 'tree_code', 'qty','status','condition','planting_date',
                foreach($request->list_pohon as $val){
                    Monitoring2Detail::create([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }
                // var_dump ($pohon_mpts);
            //     'monitoring_no', 'planting_year','planting_date', 'farmer_no', 'lahan_no',
            //     'qty_kayu', 'qty_mpts',  'qty_crops',  'lahan_condition',  'user_id', 
            //    'validation', 'validate_by', 'created_at','updated_at','is_dell'
                Monitoring2::create([
                    'monitoring_no' => $monitoring_no,
                    'planting_year' => $request->planting_year,
                    'planting_date' => $request->planting_date,
                    'farmer_no' => $request->farmer_no,
                    'lahan_no' => $request->lahan_no,
                    'lahan_condition' => $request->lahan_condition,
                    'gambar1' => $request->gambar1,
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),
                    'is_validate' => $validation,
                    'validate_by' => $validate_by,

                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
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
     *   path="/api/UpdateMonitoring2",
	 *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Monitoring 2",
     *   operationId="UpdateMonitoring2",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Monitoring 2",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="user_id", type="string", example="FF0001"),
     *              @SWG\Property(property="lahan_no", type="string", example="L0000001"),
     *              @SWG\Property(property="farmer_no", type="string", example="F0000001"),
     *              @SWG\Property(property="planting_year", type="string", example="2021"),
     *              @SWG\Property(property="planting_date", type="string", example="2021-10-10"),
     *              @SWG\Property(property="lahan_condition", type="string", example="-"),
     *              @SWG\Property(property="list_pohon", type="string", example="-"),
     *              @SWG\Property(property="gambar1", type="string", example="-"),
     *              @SWG\Property(property="gambar2", type="string", example="Nullable"),
     *              @SWG\Property(property="gambar3", type="string", example="Nullable"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdateMonitoring2(Request $request){
        $validator = Validator::make($request->all(), [
            'monitoring_no' => 'required',
            'user_id' => 'required',
            'lahan_no' => 'required', 
            'farmer_no' => 'required', 
            'planting_date' => 'required', 
            'planting_year' => 'required',
            'lahan_condition' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        }  
        
        DB::beginTransaction();

        try{            
            
            $monitoring_no = $request->monitoring_no;
            // $Lahan = DB::table('lahans')->where('lahan_no','=',$request->lahan_no)->first();
            $monitoring = DB::table('monitoring_2')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){
                $year = Carbon::now()->format('Y');
                // $form_no = 'PH-'.$year.'-'.substr($request->lahan_no,-10);

                $validation = 0;
                $validate_by = '-';
                if($request->validate_by){
                    $validation = 1;
                    $validate_by = $request->validate_by;
                }

                
                DB::table('monitoring_2_detail')->where('monitoring_no', $monitoring_no)->delete();

                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    Monitoring2Detail::create([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }

                

                Monitoring2::where('monitoring_no', '=', $monitoring_no)
                ->update([
                    'planting_year' => $request->planting_year,
                    'planting_date' => $request->planting_date,
                    'farmer_no' => $request->farmer_no,
                    'lahan_no' => $request->lahan_no,
                    'lahan_condition' => $request->lahan_condition,
                    'gambar1' => $request->gambar1,
                    'gambar2' => $this->ReplaceNull($request->gambar2, 'string'),
                    'gambar3' => $this->ReplaceNull($request->gambar3, 'string'),

                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
                    'user_id' => $request->user_id,
    
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
     *   path="/api/UpdatePohonMonitoring2",
	 *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Pohon Monitoring 2",
     *   operationId="UpdatePohonMonitoring2",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Pohon Monitoring 2",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *              @SWG\Property(property="list_pohon", type="string", example="array pohon tree_code, qty, status dll"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdatePohonMonitoring2(Request $request){
        $validator = Validator::make($request->all(), [
            'monitoring_no' => 'required',
            'list_pohon' => 'required',
        ]);

        if($validator->fails()){
            $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
            return response()->json($rslt, 400);
        } 

        DB::beginTransaction();

        try{
             
            
            $monitoring_no = $request->monitoring_no;
            $list_pohon = $request->list_pohon;
            $monitoring = DB::table('monitoring_2')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){
                
                DB::table('monitoring_2_detail')->where('monitoring_no', $monitoring_no)->delete();

                
                $pohon_mpts = 0;
                $pohon_non_mpts = 0;
                $pohon_bawah = 0;

                foreach($request->list_pohon as $val){
                    Monitoring2Detail::where('monitoring_no', '=', $monitoring_no)
                    ->update([
                        'monitoring_no' => $monitoring_no,
                        'tree_code' => $val['tree_code'],
                        'qty' => $val['qty'],
                        'status' => $val['status'],
                        'condition' => $val['condition'],
                        'planting_date' => $val['planting_date'],
        
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);

                    $trees_get = DB::table('trees')->where('tree_code','=',$val['tree_code'])->first();

                    if( $trees_get->tree_category == "Pohon_Buah"){
                        $pohon_mpts = $pohon_mpts + $val['qty'];
                    }else if($trees_get->tree_category == "Tanaman_Bawah_Empon"){
                        $pohon_bawah = $pohon_bawah + $val['qty'];
                    }else{
                        $pohon_non_mpts = $pohon_non_mpts + $val['qty'];
                    }
                }

                Monitoring2::where('monitoring_no', '=', $monitoring_no)
                ->update([
                    // 'form_no' => $form_no,
                    'qty_kayu' => $pohon_non_mpts,
                    'qty_mpts' => $pohon_mpts,
                    'qty_crops' => $pohon_bawah,
    
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
     *   path="/api/SoftDeleteMonitoring2",
	 *   tags={"Monitoring2"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="SoftDelete Monitoring 2",
     *   operationId="SoftDeleteMonitoring2",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="SoftDelete Monitoring 2",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="monitoring_no", type="string", example="MO1-2021-0000001"),
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeleteMonitoring2(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'monitoring_no' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }  
            
            $monitoring_no = $request->monitoring_no;
            $monitoring = DB::table('monitoring_2')->where('monitoring_no','=',$monitoring_no)->first();
            
            if($monitoring){

                Monitoring2::where('monitoring_no', '=', $monitoring_no)
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
}
