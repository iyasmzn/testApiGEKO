<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LahanTutupan extends Model
{
    protected $table = 'lahan_tutupans';
    protected $fillable = ['lahan_no', 'land_area', 'planting_area', 'planting_year', 'sisa_luasan', 'percentage_sisa_luasan', 'created_at', 'updated_at'];
}
