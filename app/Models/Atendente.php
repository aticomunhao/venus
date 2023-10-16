<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atendente extends Model
{
    use HasFactory;

/**
 * Get the pessoa that owns the Atendente
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function pessoa(): BelongsTo
{
    return $this->belongsTo(Pessoa::class,'id_pessoa');
}

/**
 * Get the grupo that owns the Atendente
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function grupo(): BelongsTo
{
    return $this->belongsTo(Grupo::class,'id_grupo');
}



}
