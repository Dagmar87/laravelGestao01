<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa um Grupo Econômico no sistema.
 *
 * @property int $id
 * @property string $nome
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bandeira[] $bandeiras
 */
class GrupoEconomico extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
    ];

    /**
     * Obtém as bandeiras associadas a este grupo econômico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bandeiras(): HasMany
    {
        return $this->hasMany(Bandeira::class, 'grupo_economico_id');
    }

    /**
     * Escopo para buscar grupos econômicos por nome.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $nome
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNome($query, $nome)
    {
        return $query->where('nome', 'like', "%{$nome}%");
    }
}
