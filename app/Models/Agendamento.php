<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $table = 'agendamentos'; // opcional se já segue a convenção

    protected $fillable = [
        'cliente_id',
        'barbeiro_id',
        'barbearia_id',
        'servico_id',
        'data_hora',
        'status',
    ];

    // Relacionamentos (opcional, mas recomendado)
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    public function barbeiro()
    {
        return $this->belongsTo(Usuario::class, 'barbeiro_id');
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class, 'barbearia_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }
}
