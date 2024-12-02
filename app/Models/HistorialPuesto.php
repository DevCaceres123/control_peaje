<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialPuesto extends Model
{
    use SoftDeletes;

    protected $table = 'historial_puesto';
    protected $dates = ['deleted_at'];
}
