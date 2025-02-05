<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEconomico extends Model
{
    protected $fillable = ['nome', 'dataDeCriacao', 'ultimaAtualizacao'];
}
