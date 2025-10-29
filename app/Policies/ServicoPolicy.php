<?php

namespace App\Policies;

use App\Models\Servico;
use App\Models\Usuario;

class ServicoPolicy
{
    public function viewAny(Usuario $usuario)
    {
        return true;
    }

    public function view(Usuario $usuario, Servico $servico)
    {
        return $usuario->tipo === 'admin' || $usuario->barbearia_id === $servico->barbearia_id;
    }

    public function create(Usuario $usuario)
    {
        return in_array($usuario->tipo, ['admin', 'barbeiro']);
    }

    public function update(Usuario $usuario, Servico $servico)
    {
        return $usuario->tipo === 'admin' || ($usuario->tipo === 'barbeiro' && $usuario->barbearia_id === $servico->barbearia_id);
    }

    public function delete(Usuario $usuario, Servico $servico)
    {
        return $usuario->tipo === 'admin';
    }
}
