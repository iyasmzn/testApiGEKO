<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FarmerTraining extends Model
{
    protected $table = 'farmer_trainings';

    protected $fillable = ['training_no', 'training_date', '1st_material', '2nd_material', 'organic_material', 'program_year', 'absent', 'mu_no', 'target_area', 'village', 'field_coordinator', 'ff_no', 'user_id', 'is_dell', 'deleted_by', 'verified_by', 'status', 'created_at', 'updated_at'];
}
