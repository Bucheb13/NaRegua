<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'cliente_id',
        'valor_total',
        'data_venda',
    ];

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }
}
