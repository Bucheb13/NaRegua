<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Barbearia extends Model
{
    use HasFactory;

    protected $table = 'barbearias';

    protected $fillable = [
        'nome',
        'responsavel_nome',
        'cnpj',
        'telefone',
        'email',
        'endereco',
        'licenca_validade',
        'logo',
    ];

    // Cast para Carbon
    protected $casts = [
        'licenca_validade' => 'datetime',
    ];

    // A licença está ativa se a data de validade for hoje ou no futuro
    public function getLicencaAtivaAttribute(): bool
    {
        return $this->licenca_validade && ($this->licenca_validade->isFuture() || $this->licenca_validade->isToday());
    }

    // Retorna a data de vencimento formatada (apenas se expirada)
    public function getLicencaVencidaEmAttribute(): ?string
    {
        if (!$this->licenca_validade || $this->licenca_ativa) {
            return null;
        }

        return $this->licenca_validade->format('d/m/Y');
    }
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'barbearia_id');
    }
    
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'barbearia_id');
    }
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'barbearia_id', 'id');
    }

}
