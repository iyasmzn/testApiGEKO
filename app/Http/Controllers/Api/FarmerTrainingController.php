<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Desa;
use App\Kecamatan;
use App\ManagementUnit;
use App\TargetArea;
use App\FarmerTraining;
use App\FarmerTrainingDetail;
use App\Organic;
use App\TrainingMaterial;
use Carbon\Carbon;
use DB;

class FarmerTrainingController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/api/GetFarmerTrainingAll",
     *   tags={"FarmerTrainings"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Farmer Trainings All",
     *   operationId="GetFarmerTrainingAll",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="village",in="query", type="string"),
     *      @SWG\Parameter(name="user_id",in="query", required=true, type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetFarmerTrainingAll(Request $request){
        $userId = $request->user_id;
        $getvillage = $request->village;
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        if($getvillage){$village='%'.$getvillage.'%';}
        else{$village='%%';}
        try{
            $GetFarmerTrainingAll = FarmerTraining::where('user_id', '=', $userId)->where('village', 'Like', $village)->where('is_dell', '=', 0)->orderBy('village', 'ASC')->get();
            if(count($GetFarmerTrainingAll)!=0){
                $count = FarmerTraining::where('user_id', '=', $userId)->where('is_dell', '=', 0)->count();
                $data = ['count'=>$count, 'data'=>$GetFarmerTrainingAll];
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

    public function GetFarmerTrainingAllTempDelete(Request $request){
        // $userId = $request->user_id;
        // $getvillage = $request->village;
        // $limit = $this->limitcheck($request->limit);
        // $offset =  $this->offsetcheck($limit, $request->offset);
        // if($getvillage){$village='%'.$getvillage.'%';}
        // else{$name='%%';}
        try{
            $GetFarmerTrainingAll = FarmerTraining::where('is_dell', '=', 1)->where('absent', '=', '-')->orderBy('village', 'ASC')->get();
            if(count($GetFarmerTrainingAll)!=0){
                $count = FarmerTraining::where('is_dell', '=', 1)->where('absent', '=', '-')->count();
                $data = ['count'=>$count, 'data'=>$GetFarmerTrainingAll];
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
     *   path="/api/AddFarmerTraining",
	 *   tags={"FarmerTrainings"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Farmer Training",
     *   operationId="AddFarmerTraining",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Farmer Training",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="training_no", type="string", example="0909090909"),
     *              @SWG\Property(property="training_date", type="date", example="2021-03-20"),
     *              @SWG\Property(property="1st_material", type="string", example="Pelatihan Pertama"),
     *              @SWG\Property(property="2nd_material", type="date", example="Pelatihan kedua"),
     *              @SWG\Property(property="organic_material", type="string", example="Pelatihan Organik"),
     *              @SWG\Property(property="program_year", type="date", example="2022"),
     *              @SWG\Property(property="absent", type="integer", example="diisi gambar ya nanti"),
     *              @SWG\Property(property="address", type="string", example="Jl Cemara No 22, Kemiri, Salatiga"),
     *              @SWG\Property(property="village", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="field_coordinator", type="string", example="Nama FC"),
     *              @SWG\Property(property="ff_no", type="string", example="FF0000001"),
     *              @SWG\Property(property="mu_no", type="string", example="022"),   
     *              @SWG\Property(property="origin", type="string", example="lokal"),
     *              @SWG\Property(property="gender", type="string", example="male"),
     *              @SWG\Property(property="number_family_member", type="int", example="2"),   
     *              @SWG\Property(property="target_area", type="string", example="test"),
     *              @SWG\Property(property="status", type="int", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="U0002"),
     *          ),
     *      )
     * )
     *
     */
    public function AddFarmerTraining(Request $request){
        try{
            // date_default_timezone_set("Asia/Bangkok");

            $validator = Validator::make($request->all(), [
                'training_no' => 'required|max:255|unique:farmer_trainings',
                'training_date' => 'required',
                'first_material' => 'required|max:255',
                'second_material' => 'required|max:255',
                'organic_material' => 'required|max:255',
                'program_year' => 'required',
                'absent' => 'required',
                'village' => 'required|max:255',
                'field_coordinator' => 'required|max:255',
                'ff_no' => 'required|max:255',       
                'mu_no' => 'required|max:255',
                'target_area' => 'required|max:255',
                'status' => 'required|max:1',
                'user_id' => 'required|max:11'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $getLastIdFarmerTraining = FarmerTraining::orderBy('training_no','desc')->first(); 
            if($getLastIdFarmerTraining){
                $trainingno = 'T'.str_pad(((int)substr($getLastIdFarmer->training_no,-8) + 1), 8, '0', STR_PAD_LEFT);
            }else{
                $trainingno = 'T00000001';
            }
            $day = Carbon::now()->format('d');
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');

            $status = 0;
            if($absent != "-" && $program_year != "-")
            {
               $status = 1;
            }

            // var_dump('test');
            FarmerTraining::create([
                'training_no' => $trainingno,
                'training_date' => $request->training_date,
                'first_material' => $request->first_material,
                'second_material' => $request->second_material,
                'organic_material' => $request->organic_material,
                'program_year' => $request->program_year,
                'absent' => $request->absent,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'village' => $request->village,
                'field_coordinator' => $request->field_coordinator,
                'ff_no' => $request->ff_no,
                'user_id' => $request->user_id,
                'is_dell' => 0,
                'deleted_by' => '-',
                'verified_by' => $request->verified_by,
                'status' => 0,
          
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
     *   path="/api/UpdateFarmerTraining",
	 *   tags={"FarmerTrainings"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Farmer Training",
     *   operationId="UpdateFarmerTraining",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Farmer Training",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="farmer_no", type="string", example="F000001"),
     *              @SWG\Property(property="ktp_no", type="string", example="0909090909"),
     *              @SWG\Property(property="name", type="string", example="Budi Indra"),
     *              @SWG\Property(property="birthday", type="date", example="2000-10-20"),
     *              @SWG\Property(property="religion", type="string", example="Islam"),
     *               @SWG\Property(property="rt", type="integer", example="01"),
     *               @SWG\Property(property="rw", type="integer", example="02"),
     *              @SWG\Property(property="address", type="string", example="Jl Cemara No 22, Kemiri, Salatiga"),
     *              @SWG\Property(property="village", type="string", example="33.05.10.18"),
     *              @SWG\Property(property="marrital_status", type="string", example="Kawin"),
     *              @SWG\Property(property="phone", type="string", example="085777771111"),
     *              @SWG\Property(property="ethnic", type="string", example="Jawa"),   
     *              @SWG\Property(property="origin", type="string", example="lokal"),
     *              @SWG\Property(property="gender", type="string", example="male"),
     *              @SWG\Property(property="number_family_member", type="int", example="2"),   
     *              @SWG\Property(property="mu_no", type="string", example="024"),
     *              @SWG\Property(property="target_area", type="string", example="test"),
     *              @SWG\Property(property="active", type="int", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="U0002"),
     *              @SWG\Property(property="ktp_document", type="int", example="test"),              
     *              @SWG\Property(property="post_code", type="string", example="nullable"),
     *              @SWG\Property(property="group_no", type="string", example="nullable"),
     *              @SWG\Property(property="mou_no", type="string", example="nullable"),   
     *              @SWG\Property(property="main_income", type="int", example="nullable"),
     *              @SWG\Property(property="side_income", type="int", example="nullable"),
     *              @SWG\Property(property="main_job", type="string", example="nullable"),
     *              @SWG\Property(property="side_job", type="string", example="nullable"),
     *              @SWG\Property(property="education", type="string", example="nullable"),
     *              @SWG\Property(property="non_formal_education", type="string", example="nullable"),
     *              @SWG\Property(property="farmer_profile", type="string", example="nullable") 
     *          ),
     *      )
     * )
     *
     */
    public function UpdateFarmerTraining(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'training_no' => 'required|max:255|unique:farmer_trainings',
                'training_date' => 'required',
                'first_material' => 'required|max:255',
                'second_material' => 'required|max:255',
                'organic_material' => 'required|max:255',
                'program_year' => 'required',
                'absent' => 'required',
                'village' => 'required|max:255',
                'field_coordinator' => 'required|max:255',
                'ff_no' => 'required|max:255',             
                'mu_no' => 'required|max:255',
                'target_area' => 'required|max:255',
                'status' => 'required|max:1',
                'user_id' => 'required|max:11'              
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            FarmerTraining::where('training_no', '=', $request->training_no)
            ->update
            ([
                'training_no' => $trainingno,
                'training_date' => $request->training_date,
                'first_material' => $request->first_material,
                'second_material' => $request->second_material,
                'organic_material' => $request->organic_material,
                'program_year' => $request->program_year,
                'absent' => $request->absent,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'village' => $request->village,
                'field_coordinator' => $request->field_coordinator,
                'ff_no' => $request->ff_no,
                'user_id' => $request->user_id,
                'deleted_by' => '-',
                'verified_by' => $request->verified_by,        
                
                'updated_at'=>Carbon::now(),

                'is_dell' => 0
            ]);
            if($group_no != "-" && $main_job != "-" && $side_job != "-" && $education != "-" && $non_formal_education != "-" && $farmer_profile != "-" )
            {
                FarmerTraining::where('farmer_no', '=', $request->farmer_no)
                ->update
                (['status' => 1]);
            }
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    public function AddDetailFarmerTraining(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'training_no' => 'required',
                'date_training' => 'required', 
                'farmer_no' => 'required',
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            // var_dump($coordinate);
            // 'lahan_no', 'tree_code', 'amount', 'detail_year', 'user_id','created_at', 'updated_at'
            FarmerTrainingDetaill::create([
                'training_no' => $request->training_no,
                'date_training' => $request->date_training,
                'farmer_no' => $request->farmer_no,

                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/DeleteFarmerTrainingDetail",
	 *   tags={"Lahan"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Farmer Training Detail",
     *   operationId="DeleteFarmerTrainingDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Farmer Training Detail",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="1")
     *          ),
     *      )
     * )
     *
     */
    public function DeleteFarmerTrainingDetail(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
    
            DB::table('farmer_training_details')->where('id', $request->id)->delete();
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

        /**
     * @SWG\Post(
     *   path="/api/SoftDeleteFarmerTraining",
	 *   tags={"FarmerTrainings"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Soft Delete Farmer Training",
     *   operationId="SoftDeleteFarmer",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Soft Delete Farmer",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="2")
     *          ),
     *      )
     * )
     *
     */
    public function SoftDeleteFarmerTraining(Request $request){
        try{
            $validator = Validator::make($request->all(), [    
                'id' => 'required'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            FarmerTraining::where('id', '=', $request->id)
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

        /**
     * @SWG\Post(
     *   path="/api/DeleteFarmerTraining",
	 *   tags={"Farmers"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Farmer Training",
     *   operationId="DeleteFarmer",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Farmer Training",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="string", example="2")
     *          ),
     *      )
     * )
     *
     */
    public function DeleteFarmerTraining(Request $request){
        try{
            $validator = Validator::make($request->all(), [    
                'id' => 'required'
            ]);

            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            DB::table('farmer_trainings')->where('id', $request->id)->delete();
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200); 
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
}
