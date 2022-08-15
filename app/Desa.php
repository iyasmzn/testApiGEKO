<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desas';
    protected $fillable = ['kode_desa', 'name', 'kode_kecamatan','kode_ta','post_code' ,'created_at','updated_at'];
}
