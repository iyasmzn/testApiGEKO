<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TreeLocation extends Model
{
    protected $table = 'tree_locations';
    protected $fillable = ['tree_code', 'mu_no','region', 'planting_year', 'created_at','updated_at'];
}
