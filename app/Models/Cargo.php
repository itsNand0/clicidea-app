<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_id',
        'nombre_cargo',
    ];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_cargo', 'cargo_id', 'area_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
