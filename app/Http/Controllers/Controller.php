<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @SWG\Swagger(
 *     basePath="",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="API Documentation",
 *     )
 * ),
 * @SWG\SecurityScheme(
 *     type="apiKey",
 *     description="Login with email and password to get the authentication token",
 *     name="Authorization",
 *     in="header",
 *     securityDefinition="apiAuth",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function ResultReturn($rsltCode, $msgDesc, $dataResult){

        $stat = ['code'=>$rsltCode, 'description'=>$msgDesc];
        $data = ['status'=>$stat, 'result'=>$dataResult];
        
        $typeSuccess = false;
        if($rsltCode == 200){
            $typeSuccess = true;
        }

        $rslt = ['success'=>$typeSuccess, 'data'=>$data];

        return $rslt;
    }
    public function ReplaceNull($req, $type){
        $rslt;
        if(strlen($req)-substr_count($req, ' ') == 0){
            if($type == 'int'){
                $rslt= 0;
            }elseif($type == 'date'){
                $rslt= "0000-01-01";
            }else{$rslt='-';}            
        }else{
            $rslt=$req;
        }
        return $rslt;
    }
    public function SetDefaultNull($req){
        $rslt;
        if(strlen($req)-substr_count($req, ' ') == 0){
            $rslt=NULL;            
        }else{
            $rslt=$req;
        }
        return $rslt;
    }

    public function limitcheck($getLimit){
        $limit = 0;
        if($getLimit){
            $limit=$getLimit;
        }
        else{
            $limit=10;
        }        
        return $limit;
    }
    public function offsetcheck($limit, $getOffset){
        $offset = 0;
        if($getOffset){
            if($getOffset == 1){
                $offset = 0;
            }
            else{
                $offset = ($getOffset-1)*$limit; 
            }            
        }
        
        return $offset;
    }
    public function getCordinate($long, $lat){
        if(substr($long,0,1)== '-'){$nLong = -1;}else{$nLong = 1;}
        if(substr($lat,0,1)== '-'){$nLat = -1;}else{$nLat = 1;}

        $DDLatitude = intval($lat)*$nLat;
        $MMLatitude = intval(($lat-intval($lat))*60)*$nLat;                
        $SSLatitude = round((((($lat-intval($lat))*60)-intval((($lat-intval($lat))*60)))*60*$nLat),2);
        if(intval($lat)<0){$PositionLat='S';}else{$PositionLat='N';}

        $DDLongitude = intval($long)*$nLong;
        $MMLongitude = intval(($long-intval($long))*60)*$nLong;                
        $SSLongitude = round((((($long-intval($long))*60)-intval((($long-intval($long))*60)))*60*$nLong),2);
        if(intval($long)<0){$PositionLong='S';}else{$PositionLong='E';}

        $la = $SSLatitude/60;
        $lo = $SSLongitude/60;

        $SumLat = $MMLatitude+$la;
        $SumLong = $MMLongitude+$lo;

        $coordinateLat = $PositionLat.$DDLatitude." ".substr($SumLat,0,6);
        $coordinateLong = $PositionLong.$DDLongitude." ".substr($SumLong,0,7);
        return $coordinateLat.'  '.$coordinateLong;
    }
}
