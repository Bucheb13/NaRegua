<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'barbearia_id',
        'nome',
        'email',
        'senha',
        'tipo', // cliente, barbeiro, admin
        'telefone',
        'data_nascimento',
        'observacoes',
        'status',
        'especialidade',
        'hora_inicio',
        'hora_fim'
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Hash automático da senha quando criar ou atualizar
     */
    public function setSenhaAttribute($value)
    {
        if (!empty($value)) {
            if (!preg_match('/^\$2y\$/', $value)) {
                $this->attributes['senha'] = Hash::make($value);
            } else {
                $this->attributes['senha'] = $value;
            }
        }
    }

    /**
     * Indica para o Laravel que a coluna 'senha' é a senha do usuário
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // 🔹 Relação com a barbearia
    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class, 'barbearia_id');
    }

    // 🔹 Horários do barbeiro
    public function horarios()
    {
        return $this->hasMany(HorarioBarbeiro::class, 'barbeiro_id');
    }

    // 🔹 Agendamentos do usuário
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // 🔹 Vendas do usuário
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'cliente_id');
    }

    // 🔹 Escopos para filtrar clientes e barbeiros
    public function scopeClientes($query)
    {
        return $query->where('tipo', 'cliente');
    }

    public function scopeBarbeiros($query)
    {
        return $query->where('tipo', 'barbeiro');
    }
}
