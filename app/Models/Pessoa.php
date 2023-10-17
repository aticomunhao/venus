<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pessoa extends Model
{
    use HasFactory;

    /**
     * Get the atendente associated with the Pessoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function atendente(): HasOne
    {
        return $this->hasOne(Atendente::class, 'id_pessoa');
    }

    /**
     * Get all of the grupo for the Pessoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grupo(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }



}
