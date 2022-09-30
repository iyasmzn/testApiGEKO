<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FarmerTrainingDetail extends Model
{
    protected $table = 'farmer_training_details';

    protected $fillable = ['training_no', 'date_training', 'farmer_no', 'created_at', 'updated_at'];
}
