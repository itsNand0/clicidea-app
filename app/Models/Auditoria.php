<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';
    protected $primaryKey = 'id ';

    protected $fillable = [
        'accion',
        'modelo',
        'modelo_id',
        'cambios',
        'usuario_id',
    ];

    protected $casts = [
        'cambios' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
    
}
