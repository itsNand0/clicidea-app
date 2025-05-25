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

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
