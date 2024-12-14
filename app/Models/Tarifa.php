<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $table = "tarifas";
    protected $fillable = ["nombre","precio", "descripcion", "estado"];
    public function delete_tarifas(){
        return $this->hasMany('App\Models\DeleteTarifas','registro_id');
    }
}
