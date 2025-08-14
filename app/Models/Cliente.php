<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{   
    public $timestamps = false;
    protected $table = 'cliente';
    protected $primaryKey = 'idcliente';
    public $incrementing = true;

    protected $fillable = [
        'atm_id',
        'nombre',
        'zona',
    ];
}
