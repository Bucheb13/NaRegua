<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'nome',
        'descricao',
        'preco',
        'quantidade_estoque',
    ];

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }
}
