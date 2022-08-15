<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\FieldFacilitator;

class FieldFacilitatorController extends Controller
{

    /**
     * @SWG\Get(
     *   path="/api/GetFieldFacilitatorAllWeb",
     *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Field Facilitator All Web Admin",
     *   operationId="GetFieldFacilitatorAllWeb",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="fc_no",in="query", type="string"),
     * )
     */
    public function GetFieldFacilitatorAllWeb(Request $request){
        $getAll = false;
        $getfcno = $request->fc_no;
        if($getfcno ){$fc_no = (explode(",",$getfcno));}
        else{ $fc_no='%%';$getAll = true;}
        try{
           
            if($getAll == true){
                // var_dump($fc_no);
                $getffall= DB::table('field_facilitators')
                ->select('field_facilitators.id', 'field_facilitators.ff_no','field_facilitators.fc_no',
                'employees.name as namaFC', 'field_facilitators.name as namaFF', 'field_facilitators.gender', 'field_facilitators.address', 'field_facilitators.village','field_facilitators.kecamatan')
                ->leftjoin('employees', 'employees.nik', '=', 'field_facilitators.fc_no')
                ->orderBy('field_facilitators.name', 'ASC')
                ->get();           
            }else{
                // var_dump($fc_no);
                $getffall= DB::table('field_facilitators')
                    ->select('field_facilitators.id', 'field_facilitators.ff_no','field_facilitators.fc_no',
                    'employees.name as namaFC', 'field_facilitators.name as namaFF', 'field_facilitators.gender', 'field_facilitators.address', 'field_facilitators.village','field_facilitators.kecamatan')
                    ->leftjoin('employees', 'employees.nik', '=', 'field_facilitators.fc_no')
                    ->wherein('field_facilitators.fc_no', $fc_no)
                    ->orderBy('field_facilitators.name', 'ASC')
                    ->get();
            }
            // var_dump($GetFieldFacilitator);
            if(count($getffall)!=0){
                if($getAll == true){
                    $count= DB::table('field_facilitators')
                    ->leftjoin('employees', 'employees.nik', '=', 'field_facilitators.fc_no')
                    ->orderBy('field_facilitators.name', 'ASC')
                    ->count();
                }else{
                    $count= DB::table('field_facilitators')
                    ->leftjoin('employees', 'employees.nik', '=', 'field_facilitators.fc_no')
                    ->wherein('field_facilitators.fc_no', $fc_no)
                    ->orderBy('field_facilitators.name', 'ASC')
                    ->count();
                }
                $data = ['count'=>$count, 'data'=>$getffall];
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
     *   path="/api/GetFieldFacilitatorAll",
     *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Field Facilitator All Admin",
     *   operationId="GetFieldFacilitatorAll",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="name",in="query", type="string"),
     *      @SWG\Parameter(name="fc_no",in="query", type="string"),
     * )
     */
    public function GetFieldFacilitatorAll(Request $request){
        $getname = $request->name;
        if($getname){$name='%'.$getname.'%';}
        else{$name='%%';}

        $getAll = false;
        $getfcno = $request->fc_no;
        if($getfcno ){$fc_no = (explode(",",$getfcno));}
        else{ $fc_no='%%';$getAll = true;}
        try{
           
            if($getAll == true){
                // var_dump($fc_no);
                $GetFieldFacilitator = FieldFacilitator::select('id', 'ff_no','fc_no', 'name', 'gender', 'ktp_no','address','active', 'created_at')->where('fc_no', 'Like', $fc_no)->where('name', 'Like', $name)->orderBy('name', 'ASC')->get();           
            }else{
                // var_dump($fc_no);
                $GetFieldFacilitator = FieldFacilitator::select('id', 'ff_no','fc_no', 'name', 'gender', 'ktp_no','address','active', 'created_at')->wherein('fc_no', $fc_no)->where('name', 'Like', $name)->orderBy('name', 'ASC')->get();
            }
            // var_dump($GetFieldFacilitator);
            if(count($GetFieldFacilitator)!=0){
                if($getAll == true){
                    $count = FieldFacilitator::where('fc_no', 'Like', $fc_no)->where('name', 'Like', $name)->count();
                }else{
                    $count = FieldFacilitator::wherein('fc_no', $fc_no)->where('name', 'Like', $name)->count();
                }
                $data = ['count'=>$count, 'data'=>$GetFieldFacilitator];
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
     *   path="/api/GetFieldFacilitator",
     *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Field Facilitator",
     *   operationId="GetFieldFacilitator",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="name",in="query", type="string"),
     *      @SWG\Parameter(name="limit",in="query", type="integer"),
     *      @SWG\Parameter(name="offset",in="query", type="integer"),
     * )
     */
    public function GetFieldFacilitator(Request $request){
        $limit = $this->limitcheck($request->limit);
        $offset =  $this->offsetcheck($limit, $request->offset);
        $getname = $request->name;
        if($getname){$name='%'.$getname.'%';}
        else{$name='%%';}
        try{
            $GetFieldFacilitator = FieldFacilitator::select('id', 'ff_no', 'name', 'gender', 'ktp_no','address','active', 'created_at')->where('name', 'Like', $name)->orderBy('name', 'ASC')->limit($limit)->offset($offset)->get();
            // var_dump($GetFieldFacilitator);
            if(count($GetFieldFacilitator)!=0){
                $count = FieldFacilitator::count();
                $data = ['count'=>$count, 'data'=>$GetFieldFacilitator];
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
     *   path="/api/GetFieldFacilitatorDetail",
     *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Get Field Facilitator Detail",
     *   operationId="GetFieldFacilitatorDetail",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(name="id",in="query", type="string")
     * )
     */
    public function GetFieldFacilitatorDetail(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
            $GetFieldFacilitatorDetail = 
            DB::table('field_facilitators')
            ->select('field_facilitators.id','field_facilitators.ff_no','field_facilitators.fc_no',
            'field_facilitators.name','field_facilitators.birthday','field_facilitators.religion',
            'field_facilitators.gender','field_facilitators.marrital','field_facilitators.join_date',
            'field_facilitators.ktp_no','field_facilitators.phone','field_facilitators.address',
            'field_facilitators.village','field_facilitators.kecamatan','field_facilitators.city',
            'field_facilitators.province','field_facilitators.post_code','field_facilitators.mu_no',
            'field_facilitators.working_area','field_facilitators.target_area','field_facilitators.bank_account',
            'field_facilitators.bank_branch','field_facilitators.bank_name','field_facilitators.ff_photo',
            'field_facilitators.ff_photo_path','field_facilitators.active','field_facilitators.user_id',
            'desas.name as namaWorkingArea','target_areas.name as namaTA','managementunits.name as namaMU')
            ->leftjoin('desas', 'desas.kode_desa', '=', 'field_facilitators.working_area')
            ->leftjoin('target_areas', 'target_areas.area_code', '=', 'field_facilitators.target_area')
            ->leftjoin('managementunits', 'managementunits.mu_no', '=', 'field_facilitators.mu_no')
            ->where('field_facilitators.id', '=', $request->id)
            // FieldFacilitator::where('id', '=', $request->id)
            ->first();
            if($GetFieldFacilitatorDetail){
                $rslt =  $this->ResultReturn(200, 'success', $GetFieldFacilitatorDetail);
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
     *   path="/api/AddFieldFacilitator",
	 *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Add Field Facilitator",
     *   operationId="AddFieldFacilitator",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Add Field Facilitator",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="fc_no", type="string", example="11_111"),
     *              @SWG\Property(property="name", type="string", example="Mangga"),
     *              @SWG\Property(property="birthday", type="date", example="1990-01-30"),
     *              @SWG\Property(property="religion", type="string", example="islam"),
     *              @SWG\Property(property="gender", type="string", example="male/female"),
     *              @SWG\Property(property="ktp_no", type="string", example="33101700020001"),
     *              @SWG\Property(property="address", type="string", example="Jl Cemara 11"),
     *              @SWG\Property(property="village", type="string", example="32.04.30.01"),
     *              @SWG\Property(property="kecamatan", type="string", example="32.04.30.01"),
     *              @SWG\Property(property="city", type="string", example="32.04"),
     *              @SWG\Property(property="province", type="string", example="JT"),
     *              @SWG\Property(property="working_area", type="string", example="390302"),
     *              @SWG\Property(property="mu_no", type="string", example="023"),
     *              @SWG\Property(property="target_area", type="string", example="120200100000"),
     *              @SWG\Property(property="active", type="string", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="020"),
     *              @SWG\Property(property="marrital", type="string", example="Nullable"),
     *              @SWG\Property(property="join_date", type="date", example="Nullable"),
     *              @SWG\Property(property="phone", type="string", example="Nullable"),
     *              @SWG\Property(property="post_code", type="string", example="Nullable"),
     *              @SWG\Property(property="bank_account", type="string", example="Nullable"),
     *              @SWG\Property(property="bank_branch", type="date", example="Nullable"),
     *              @SWG\Property(property="bank_name", type="string", example="Nullable"),
     *              @SWG\Property(property="ff_photo", type="string", example="Nullable"),
     *              @SWG\Property(property="ff_photo_path", type="string", example="Nullable")
     *          ),
     *      )
     * )
     *
     */
    public function AddFieldFacilitator(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fc_no' => 'required|max:255',
                'name' => 'required|max:255',
                'birthday' => 'required|max:255',
                'religion' => 'required|max:255',
                'gender' => 'required|max:255',
                'ktp_no' => 'required|max:255',
                'address' => 'required|max:255',
                'village' => 'required|max:255',
                'kecamatan' => 'required',
                'city' => 'required',
                'province' => 'required',
                'working_area' => 'required',
                'mu_no' => 'required',
                'target_area' => 'required',
                'active' => 'required',
                'user_id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }

            $getLastIdFieldFacilitator = FieldFacilitator::where('ff_no', 'Like', 'F%')
                                        ->orderBy('ff_no','desc')->first(); 

            $getYearNow = Carbon::now()->format('Y');
            if($getLastIdFieldFacilitator){
                $ff_no = 'FF'.str_pad(((int)substr($getLastIdFieldFacilitator->ff_no,-8) + 1), 8, '0', STR_PAD_LEFT);
            }else{
                $ff_no = 'FF00000001';
            }
            
    
            $createFF = FieldFacilitator::create([
                'ff_no' => $ff_no,
                'fc_no' => $request->fc_no,
                'name' => $request->name,
                'birthday' => $request->birthday,
                'religion' => $request->religion,
                'ktp_no' => $request->ktp_no,
                'address' => $request->address,
                'village' => $request->village,
                'kecamatan' => $request->kecamatan,
                'city' => $request->city,
                'province' => $request->province,
                'working_area' => $request->working_area,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'active' => $request->active,
                'user_id' => $request->user_id,
                

                'marrital' => $this->ReplaceNull($request->marrital, 'string'),
                'join_date' => $this->ReplaceNull($request->join_date, 'date'),
                'phone' => $this->ReplaceNull($request->phone, 'string'),
                'post_code' => $this->ReplaceNull($request->post_code, 'string'),
                'bank_account' => $this->ReplaceNull($request->bank_account, 'string'),
                'bank_branch' => $this->ReplaceNull($request->bank_branch, 'string'),
                'bank_name' => $this->ReplaceNull($request->bank_name, 'string'),
                'ff_photo' => $this->ReplaceNull($request->ff_photo, 'string'),
                'ff_photo_path' => $this->ReplaceNull($request->ff_photo_path, 'string'),

                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ]);
            $rslt =  $this->ResultReturn(200, 'success', [
                    'message' => 'success',
                    'ff_id' => $createFF->id
                ]);
            return response()->json($rslt, 200);
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }

    /**
     * @SWG\Post(
     *   path="/api/UpdateFieldFacilitator",
	 *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Update Field Facilitator",
     *   operationId="UpdateFieldFacilitator",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Update Field Facilitator",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="integer", example=1),
     *              @SWG\Property(property="fc_no", type="string", example="11_111"),
     *              @SWG\Property(property="name", type="string", example="Mangga"),
     *              @SWG\Property(property="birthday", type="date", example="1990-01-30"),
     *              @SWG\Property(property="religion", type="string", example="islam"),
     *              @SWG\Property(property="gender", type="string", example="male/female"),
     *              @SWG\Property(property="ktp_no", type="string", example="33101700020001"),
     *              @SWG\Property(property="address", type="string", example="Jl Cemara 11"),
     *              @SWG\Property(property="village", type="string", example="32.04.30.01"),
     *              @SWG\Property(property="kecamatan", type="string", example="32.04.30.01"),
     *              @SWG\Property(property="city", type="string", example="32.04"),
     *              @SWG\Property(property="province", type="string", example="JT"),
     *              @SWG\Property(property="working_area", type="string", example="390302"),
     *              @SWG\Property(property="mu_no", type="string", example="023"),
     *              @SWG\Property(property="target_area", type="string", example="120200100000"),
     *              @SWG\Property(property="active", type="string", example="1"),
     *              @SWG\Property(property="user_id", type="string", example="023"),
     *              @SWG\Property(property="marrital", type="string", example="Nullable"),
     *              @SWG\Property(property="join_date", type="date", example="Nullable"),
     *              @SWG\Property(property="phone", type="string", example="Nullable"),
     *              @SWG\Property(property="post_code", type="string", example="Nullable"),
     *              @SWG\Property(property="bank_account", type="string", example="Nullable"),
     *              @SWG\Property(property="bank_branch", type="date", example="Nullable"),
     *              @SWG\Property(property="bank_name", type="string", example="Nullable"),
     *              @SWG\Property(property="ff_photo", type="string", example="Nullable"),
     *              @SWG\Property(property="ff_photo_path", type="string", example="Nullable")
     *          ),
     *      )
     * )
     *
     */
    public function UpdateFieldFacilitator(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'fc_no' => 'required',
                'name' => 'required|max:255',
                'birthday' => 'required|max:255',
                'religion' => 'required|max:255',
                'gender' => 'required|max:255',
                'ktp_no' => 'required|max:255',
                'address' => 'required|max:255',
                'village' => 'required|max:255',
                'kecamatan' => 'required|max:255',
                'city' => 'required',
                'province' => 'required',
                'working_area' => 'required|max:255',
                'mu_no' => 'required',
                'target_area' => 'required',
                'active' => 'required',
                'user_id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
    
            FieldFacilitator::where('id', '=', $request->id)
            ->update([
                'fc_no' => $request->fc_no,
                'name' => $request->name,
                'birthday' => $request->birthday,
                'religion' => $request->religion,
                'ktp_no' => $request->ktp_no,
                'address' => $request->address,
                'village' => $request->village,
                'kecamatan' => $request->kecamatan,
                'city' => $request->city,
                'province' => $request->province,
                'working_area' => $request->working_area,
                'mu_no' => $request->mu_no,
                'target_area' => $request->target_area,
                'active' => $request->active,
                'user_id' => $request->user_id,
                

                'marrital' => $this->ReplaceNull($request->marrital, 'string'),
                'join_date' => $this->ReplaceNull($request->join_date, 'date'),
                'phone' => $this->ReplaceNull($request->phone, 'string'),
                'post_code' => $this->ReplaceNull($request->post_code, 'string'),
                'bank_account' => $this->ReplaceNull($request->bank_account, 'string'),
                'bank_branch' => $this->ReplaceNull($request->bank_branch, 'string'),
                'bank_name' => $this->ReplaceNull($request->bank_name, 'string'),
                'ff_photo' => $this->ReplaceNull($request->ff_photo, 'string'),
                'ff_photo_path' => $this->ReplaceNull($request->ff_photo_path, 'string'),

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
     *   path="/api/DeleteFieldFacilitator",
	 *   tags={"FieldFacilitator"},
     *   security={
	 *     {"apiAuth": {}},
	 *   },
     *   summary="Delete Field Facilitator",
     *   operationId="DeleteFieldFacilitator",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="Unauthenticated"),
     *   @SWG\Response(response=404, description="Not Found"),
     *   @SWG\Response(response=500, description="internal server error"),
	 *		@SWG\Parameter(
     *          name="Parameters",
     *          in="body",
	 *			description="Delete Field Facilitator",
     *          required=true, 
     *          type="string",
	 *   		@SWG\Schema(
     *              @SWG\Property(property="id", type="integer", example=1)
     *          ),
     *      )
     * )
     *
     */
    public function DeleteFieldFacilitator(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
    
            if($validator->fails()){
                $rslt =  $this->ResultReturn(400, $validator->errors()->first(), $validator->errors()->first());
                return response()->json($rslt, 400);
            }
    
            DB::table('field_facilitators')->where('id', $request->id)->delete();
            $rslt =  $this->ResultReturn(200, 'success', 'success');
            return response()->json($rslt, 200);
        }catch (\Exception $ex){
            return response()->json($ex);
        }
    }
}
