<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estadoincidencia extends Model
{
    protected $table = 'estadoincidencia';
    protected $primaryKey = 'idEstadoIncidencia';

    protected $fillable = [
        'descriEstadoIncidencia',
    ];
}
