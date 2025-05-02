<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    protected $table = 'tecnico';
    protected $primaryKey = 'idTecnico';

    protected $fillable = [
        'nombreTecnico',
        'telefonoTecnico',
        'emailTecnico',
    ];
}
