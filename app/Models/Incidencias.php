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
        'Usuario_idUsuario',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_idCliente', 'idCliente');
    }

    public function estadoIncidencia()
    {
        return $this->belongsTo(Estadoincidencia::class, 'EstadoIncidencia_idEstadoIncidencia', 'idEstadoIncidencia');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'Usuario_idUsuario', 'id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class);
    }
}
