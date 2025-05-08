<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    protected $table = 'comentarios';

    protected $primaryKey = 'idcomentario';

    protected $fillable = ['incidencia_id', 'usuario_id', 'contenido'];

    public function incidencia()
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
