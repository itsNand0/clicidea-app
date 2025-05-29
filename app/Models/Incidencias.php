<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencias extends Model
{
    protected $table = 'incidencia';
    protected $primaryKey = 'idincidencia';
    public $timestamps = false;

    protected $fillable = [
        'cliente_idcliente',
        'usuarioincidencia',
        'asuntoincidencia',
        'descrincidencia',
        'contactoincidencia',
        'estadoincidencia_idestadoincidencia',
        'fechaIncidencia',
        'adjuntoincidencia',
        'usuario_idusuario',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_idcliente', 'idcliente');
    }

    public function estadoIncidencia()
    {
        return $this->belongsTo(Estadoincidencia::class, 'estadoincidencia_idestadoincidencia', 'idestadoincidencia');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_idusuario', 'id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'Cargo_idCargo', 'id');
    }
}
