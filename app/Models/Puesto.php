<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $table = "puestos";
    protected $fillable = ["nombre","estado"];

    protected function nombre(): Attribute
    {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value),
            get: fn($value) => mb_strtoupper($value),
        );
    }


}
