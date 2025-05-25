<?php

namespace App\Models;

use App\Models\Incidencias;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cargos()
    {
        return $this->belongsToMany(Cargo::class, 'area_cargo', 'area_id', 'cargo_id');
    }
}
