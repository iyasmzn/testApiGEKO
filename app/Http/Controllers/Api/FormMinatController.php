<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\FormMinat;

class FormMinatController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetFormMinatAllAdmin",
     *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Form Minat Admin",
     *   operationId="GetFormMinatAllAdmin",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="name",in="query", type="string"),
     *       @SWG\Parameter(name="typegetdata",in="query", type="string"),
     *      @SWG\Parameter(name="fc",in="query", type="string"),
     * )
     */
    public function GetFormMinatAllAdmin(Request $request){
        $typegetdata = $request->typegetdata;
        $fc = $request->fc;
        $getname = $request->name;
        if($getname){$name='%'.$getname.'%';}
        else{$name='%%';}
        try{
            if($typegetdata == 'all' || $typegetdata == 'several'){
                $fcdecode = (explode(",",$fc));
                
                if($typegetdata == 'all'){
                    $GetFormMinatAll = DB::table('form_minats')->select('form_minats.id',\DB::raw('SUBSTRING(form_minats.form_date, 1, 4) as form_date'),'form_minats.form_date as form_date_all','form_minats.name','form_minats.alamat',
                    'form_minats.respond_to_programs','form_minats.kode_desa','desas.name as namaDesa','form_minats.tree1',
                    'form_minats.tree2','form_minats.tree3','form_minats.tree4','form_minats.tree5')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'form_minats.kode_desa')
                    ->where('form_minats.name', 'Like', $name)->orderBy('form_minats.name', 'ASC')->get();
                }else{
                    
                    $GetFormMinatAll = DB::table('form_minats')->select('form_minats.id',\DB::raw('SUBSTRING(form_minats.form_date, 1, 4) as form_date'),'form_minats.form_date as form_date_all','form_minats.name','form_minats.alamat',
                    'form_minats.respond_to_programs','form_minats.kode_desa','desas.name as namaDesa','form_minats.tree1',
                    'form_minats.tree2','form_minats.tree3','form_minats.tree4','form_minats.tree5')
                    ->leftjoin('desas', 'desas.kode_desa', '=', 'form_minats.kode_desa')
                    ->wherein('form_minats.user_id', $fcdecode)
                    ->where('form_minats.name', 'Like', $name)->orderBy('form_minats.name', 'ASC')->get();
                }

                if(count($GetFormMinatAll)!=0){
                    if($typegetdata == 'all'){
                        $count = DB::table('form_minats')->where('name', 'Like', $name)->count();
                    }else{
                        $count = DB::table('form_minats')->where('name', 'Like', $name)
                        ->wherein('user_id', $fcdecode)->count();
                    }
                    $data = ['count'=>$count, 'data'=>$GetFormMinatAll];
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
             
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/GetFormMinatAll",
     *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Form Minat",
     *   operationId="GetFormMinatAll",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="name",in="query", type="string"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     * )
     */
    public function GetFormMinatAll(Request $request){
        $userId = $request->user_id;
        $getname = $request->name;
        if($getname){$name='%'.$getname.'%';}
        else{$name='%%';}
        try{
            $GetFormMinatAll = DB::table('form_minats')->select('form_minats.id','form_minats.form_date','form_minats.name','form_minats.alamat',
            'form_minats.respond_to_programs','form_minats.kode_desa','desas.name as namaDesa')
            ->leftjoin('desas', 'desas.kode_desa', '=', 'form_minats.kode_desa')
            ->where('form_minats.user_id', '=', $userId)->where('form_minats.name', 'Like', $name)->orderBy('form_minats.name', 'ASC')->get();
            if(count($GetFormMinatAll)!=0){
                $count = DB::table('form_minats')->where('user_id', '=', $userId)->where('name', 'Like', $name)->count();
                $data = ['count'=>$count, 'data'=>$GetFormMinatAll];
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
     *   path="/api/GetFormMinatDetail",
     *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Form Minat Detail",
     *   operationId="GetFormMinatDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="id",in="query", required=true, type="string"),
     * )
     */
    public function GetFormMinatDetail(Request $request){
        $id = $request->id;
        try{
            $GetFormMinatAll = DB::table('form_minats')->select('form_minats.id',\DB::raw('SUBSTRING(form_minats.form_date, 1, 7) as form_date'),'form_minats.name','form_minats.alamat',
            'form_minats.respond_to_programs','form_minats.kode_desa','desas.name as namaDesa','form_minats.tree1',
            'form_minats.tree2','form_minats.tree3','form_minats.tree4','form_minats.tree5')
            ->leftjoin('desas', 'desas.kode_desa', '=', 'form_minats.kode_desa')
            ->where('form_minats.id', '=', $id)->first();
            if($GetFormMinatAll){
                $namaPohon  = ""; $n=1;
                
                foreach ($GetFormMinatAll as $var => $val) {                    
                    if(substr($var,0,4) == 'tree'){
                      $getTrees = DB::table('trees')->where('tree_code', '=', $val)->first();
                    //   var_dump($getTrees->tree_name);
                        if($getTrees){
                            if($n==1){
                            $namaPohon = $getTrees->tree_name;
                        }else{
                            $namaPohon = $namaPohon.', ' .$getTrees->tree_name;
                        }
                            $n+=1;
                        }
                    }                    
                }
                $data = ['id'=>$GetFormMinatAll->id,'form_date'=>$GetFormMinatAll->form_date,'name'=>$GetFormMinatAll->name,
                'alamat'=>$GetFormMinatAll->alamat,'kode_desa'=>$GetFormMinatAll->kode_desa,'respond_to_programs'=>$GetFormMinatAll->respond_to_programs,
                'namaDesa'=>$GetFormMinatAll->namaDesa,'tree1'=>$GetFormMinatAll->tree1,'tree2'=>$GetFormMinatAll->tree2,
                'tree3'=>$GetFormMinatAll->tree3,'tree4'=>$GetFormMinatAll->tree4,'tree5'=>$GetFormMinatAll->tree5,'namaPohon'=>$namaPohon];                
                // $data = $GetFormMinatAll;
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
     *   path="/api/AddFormMinat",
	 *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Form Minat",
     *   operationId="AddFormMinat",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Form Minat",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="form_date", type="string", example="2021-03-20"),
     *              @SWG\Property(property="name", type="string", example="Budi Indra"),
     *              @SWG\Property(property="alamat", type="date", example="Jl Cemara No 22, Kemiri, Salatiga"),
     *              @SWG\Property(property="kode_desa", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="respond_to_programs", type="string", example="berminat/ragu-ragu/belum_berminat"),
     *              @SWG\Property(property="tree1", type="string", example="nullable"),
     *              @SWG\Property(property="tree2", type="string", example="nullable"),
     *              @SWG\Property(property="tree3", type="string", example="nullable"),
     *              @SWG\Property(property="tree4", type="string", example="nullable"),
     *              @SWG\Property(property="tree5", type="string", example="nullable"),
     *              @SWG\Property(property="user_id", type="string", example="U0002"),
     *          ),
     *      )
     * )
     *
     */
    public function AddFormMinat(Request $request){
        try{
            // date_default_timezone_set("Asia/Bangkok");

            $validator = Validator::make($request->all(), [
                'form_date' => 'required',
                'name' => 'required|max:255',
                'alamat' => 'required',
                'kode_desa' => 'required|max:255',
                'respond_to_programs' => 'required',
                'user_id' => 'required|max:11',            
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            // $farmercount = Farmer::count();
            // $farmerno = 'F'.str_pad($farmercount+1, 8, '0', STR_PAD_LEFT);

            FormMinat::create([
                'name' => $request->name,
                'form_date' => $request->form_date,
                'alamat' => $request->alamat,
                'kode_desa' => $request->kode_desa,
                'respond_to_programs' => $request->respond_to_programs,
                'user_id' => $request->user_id,
                
                'tree1' => $this->ReplaceNull($request->tree1, 'string'),
                'tree2' => $this->ReplaceNull($request->tree2, 'string'),
                'tree3' => $this->ReplaceNull($request->tree3, 'string'),
                'tree4' => $this->ReplaceNull($request->tree4, 'string'),
                'tree5' => $this->ReplaceNull($request->tree5, 'string'),           
                
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateFormMinat",
	 *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Form Minat",
     *   operationId="UpdateFormMinat",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Form Minat",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1"),
     *              @SWG\Property(property="form_date", type="string", example="2021-03-20"),
     *              @SWG\Property(property="name", type="string", example="Budi Indra"),
     *              @SWG\Property(property="alamat", type="date", example="Jl Cemara No 22, Kemiri, Salatiga"),
     *              @SWG\Property(property="kode_desa", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="respond_to_programs", type="string", example="berminat/ragu-ragu/belum_berminat"),
     *              @SWG\Property(property="tree1", type="string", example="nullable"),
     *              @SWG\Property(property="tree2", type="string", example="nullable"),
     *              @SWG\Property(property="tree3", type="string", example="nullable"),
     *              @SWG\Property(property="tree4", type="string", example="nullable"),
     *              @SWG\Property(property="tree5", type="string", example="nullable"),
     *              @SWG\Property(property="user_id", type="string", example="U0002"),
     *          ),
     *      )
     * )
     *
     */
    public function UpdateFormMinat(Request $request){
        try{
            // date_default_timezone_set("Asia/Bangkok");

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'form_date' => 'required',
                'name' => 'required|max:255',
                'alamat' => 'required',
                'kode_desa' => 'required|max:255',
                'respond_to_programs' => 'required',
                'user_id' => 'required|max:11',            
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            // $farmercount = Farmer::count();
            // $farmerno = 'F'.str_pad($farmercount+1, 8, '0', STR_PAD_LEFT);

            FormMinat::where('id', '=', $request->id)
            ->update([
                'name' => $request->name,
                'form_date' => $request->form_date,
                'alamat' => $request->alamat,
                'kode_desa' => $request->kode_desa,
                'respond_to_programs' => $request->respond_to_programs,
                'user_id' => $request->user_id,
                
                'tree1' => $this->ReplaceNull($request->tree1, 'string'),
                'tree2' => $this->ReplaceNull($request->tree2, 'string'),
                'tree3' => $this->ReplaceNull($request->tree3, 'string'),
                'tree4' => $this->ReplaceNull($request->tree4, 'string'),
                'tree5' => $this->ReplaceNull($request->tree5, 'string'),           
                
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteFormMinat",
	 *   tags={"FormMinat"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Form Minat",
     *   operationId="DeleteFormMinat",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Form Minat",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="integer", example=1)
     *          ),
     *      )
     * )
     *
     */
    public function DeleteFormMinat(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|max:255'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
    
            DB::table('form_minats')->where('id', $request->id)->delete();
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
}
