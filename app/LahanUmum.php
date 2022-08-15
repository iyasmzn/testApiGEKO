<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LahanUmum extends Model
{
    protected $table = 'lahan_umums';
    protected $fillable = ['lahan_no', 'mu_no','target_area',  'village', 'pic_lahan', 'ktp_no', 'address', 'mou_no', 'luas_lahan', 'luas_tanam', 'pattern_planting', 'access_lahan', 'jarak_lahan', 'status', 'longitude', 'latitude', 'distribution_date', 'user_id', 'complete_data','is_verified', 'verified_by', 'created_at', 'updated_at', 'photo1', 'photo2', 'photo3', 'photo4', 'active', 'coordinate', 'tutupan_lahan', 'is_dell', 'description'];
}
