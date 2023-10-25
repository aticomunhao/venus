<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sala extends Model
{
    use HasFactory;

    // PAI (envia para)

    // $table->foreign('id_sala')->references('id')->on('salas');
    public function sala(): HasMany
    {
        return $this->hasMany(Sala::class, 'id_sala');
    }
}
