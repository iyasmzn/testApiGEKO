<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManagementUnit extends Model
{
    protected $table = 'managementunits';
    protected $fillable = ['mu_no', 'name','active', 'created_at','updated_at'];
}
