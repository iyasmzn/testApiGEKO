<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organic extends Model
{
    protected $table = 'organics';

    protected $fillable = ['organic_no', 'organic_name', 'village', 'uom', 'organic_amount', 'status', 'created_by', 'verified_by', 'is_dell', 'deleted_by', 'created_at', 'updated_at'];
}
