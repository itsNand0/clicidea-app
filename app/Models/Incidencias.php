<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencias extends Model
{
    protected $table = 'incidencia';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cliente_idCliente',
        'usuarioIncidencia',
        'asuntoIncidencia',
        'descriIncidencia',
        'contactoIncidencia',
        'EstadoIncidencia_idEstadoIncidencia',
        'fechaIncidencia',
        'adjuntoIncidencia',
        'Tecnico_idTecnico', // si es que lo vas a usar más adelante
    ];
}
