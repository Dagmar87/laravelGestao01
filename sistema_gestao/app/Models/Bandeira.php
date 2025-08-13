<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo que representa uma Bandeira no sistema.
 *
 * @property int $id
 * @property string $nome
 * @property int|null $grupo_economico_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\GrupoEconomico|null $grupoEconomico
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Unidade[] $unidades
 */
class Bandeira extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'grupo_economico_id',
    ];

    /**
     * Obtém o grupo econômico ao qual esta bandeira pertence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupoEconomico(): BelongsTo
    {
        return $this->belongsTo(GrupoEconomico::class, 'grupo_economico_id');
    }

    /**
     * Obtém as unidades associadas a esta bandeira.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unidades(): HasMany
    {
        return $this->hasMany(Unidade::class, 'bandeira_id');
    }

    /**
     * Formata o CNPJ para exibição.
     *
     * @return string
     */
    public function getNomeFormatadoAttribute(): string
    {
        return $this->nome;
    }

    /**
     * Escopo para buscar bandeiras por nome.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $nome
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNome($query, $nome)
    {
        return $query->where('nome', 'like', "%{$nome}%");
    }

    /**
     * Escopo para buscar bandeiras por grupo econômico.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $grupoEconomicoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereGrupoEconomico($query, $grupoEconomicoId)
    {
        return $query->where('grupo_economico_id', $grupoEconomicoId);
    }
}
