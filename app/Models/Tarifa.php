<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $table = "tarifas";
    protected $fillable = ["nombre","precio", "descripcion", "estado"];



      /**
     * Mutador para asegurarse de que el nombre se guarde en mayúsculas.
     */
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function delete_tarifas(){
        return $this->hasMany('App\Models\DeleteTarifas','registro_id');
    }
}
