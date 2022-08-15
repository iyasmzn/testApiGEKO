<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormMinat extends Model
{
    protected $table = 'form_minats';
    protected $fillable = ['form_date', 'name', 'alamat', 'kode_desa', 'respond_to_programs', 'tree1', 'tree2', 'tree3', 'tree4', 'tree5', 'user_id','created_at', 'updated_at'];
}
