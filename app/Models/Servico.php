<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'barbearia_id',
        'nome',
        'descricao',
        'preco',
        'duracao_minutos',
    ];

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }
}
