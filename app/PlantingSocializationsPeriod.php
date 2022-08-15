<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlantingSocializationsPeriod extends Model
{
    protected $table = 'planting_period';
    protected $fillable = ['form_no', 'pembuatan_lubang_tanam', 'distribution_time', 'distribution_location', 'planting_time', 'created_at','updated_at'];
}
