<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario',
        'password',
        'ci',
        'nombres',
        'apellidos',
        'id_persona',
        'estado',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function nombres(): Attribute
    {
        return new Attribute(
            // set: fn($value) => mb_strtoupper($value),
            // get: fn($value) => mb_strtoupper($value),
        );
    }

    protected function apellidos(): Attribute
    {
        return new Attribute(
            // set: fn($value) => mb_strtoupper($value),
            // get: fn($value) => mb_strtoupper($value),
        );
    }

    //relacion revesa
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }


    public function puestos()
    {
        return $this->belongsToMany('App\Models\Puesto', 'historial_puesto', 'usuario_id', 'puesto_id')
            ->withPivot('created_at', 'updated_at','descripcion_edicion','estado')
            ->withTimestamps();
    }


    public function delete_tarifas(){
        return $this->hasMany('App\Models\DeleteTarifas','usuario_id');
    }
}
