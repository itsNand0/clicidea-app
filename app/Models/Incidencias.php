<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencias extends Model
{
    protected $table = 'incidencia';
    protected $primaryKey = 'idIncidencia';
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
        'Tecnico_idTecnico',
        'Area_idArea',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_idCliente', 'idCliente');
    }

    public function tecnico()
    {
        return $this->belongsTo(Tecnico::class, 'Tecnico_idTecnico', 'idTecnico');
    }

    public function estadoIncidencia()
    {
        return $this->belongsTo(Estadoincidencia::class, 'EstadoIncidencia_idEstadoIncidencia', 'idEstadoIncidencia');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'Area_idArea', 'id');
    }
}
