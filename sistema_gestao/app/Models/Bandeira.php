<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bandeira extends Model
{
    protected $fillable = ['nome', 'grupo_economico_id'];
}
