<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registro extends Model
{
    use SoftDeletes;

    public function delete_tarifas(){
        return $this->hasMany('App\Models\DeleteTarifas','registro_id');
    }

    
}
