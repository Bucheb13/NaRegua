<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioBarbeiro extends Model
{
    protected $table = 'horarios_barbeiros';

    protected $fillable = [
        'barbeiro_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];

    public function barbeiro()
    {
        return $this->belongsTo(Usuario::class, 'barbeiro_id');
    }
}
