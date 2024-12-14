<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeleteTarifas extends Model
{
    protected $table="delete_tarifas";

    public function user(){
        return $this->belongsTo('App\Models\User','usuario_id');
    }

    public function registro(){
        return $this->belongsTo('App\Models\Registro','registro_id');
    }

    public function tarifa(){
        return $this->belongsTo('App\Models\Tarifa','tarifa_id');
    }
}
