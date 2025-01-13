<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = "tipo_vehiculos";
    protected $fillable = ["nombre", "estado"];

      /**
     * Mutador para asegurarse de que el nombre se guarde en mayúsculas.
     */
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }
}
