<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialRegistros extends Model
{
    use SoftDeletes;
    protected $table = 'historial_registros';

}
