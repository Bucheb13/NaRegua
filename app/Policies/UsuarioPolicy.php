<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsuarioPolicy
{
    use HandlesAuthorization;

    /**
     * Todos podem acessar o index, mas filtraremos no controller.
     */
    public function viewAny(Usuario $user)
    {
        return true;
    }

    /**
     * Permite visualizar um usuário específico.
     */
    public function view(Usuario $user, Usuario $usuario)
    {
        // Admin vê tudo, barbeiro só vê usuários da própria barbearia, cliente não vê
        if ($user->tipo === 'admin') {
            return true;
        }

        if ($user->tipo === 'barbeiro') {
            return $user->barbearia_id === $usuario->barbearia_id;
        }

        return false; // cliente não vê
    }

    /**
     * Permite criar usuários.
     */
    public function create(Usuario $user)
    {
        // Admin pode criar em qualquer barbearia
        if ($user->tipo === 'admin') {
            return true;
        }

        // Barbeiro só pode criar usuários da própria barbearia
        if ($user->tipo === 'barbeiro') {
            return true;
        }

        // Cliente não pode criar nada
        return false;
    }

    /**
     * Permite atualizar usuários.
     */
    public function update(Usuario $user, Usuario $usuario)
    {
        // Admin pode atualizar qualquer usuário
        if ($user->tipo === 'admin') {
            return true;
        }

        // Barbeiro só pode atualizar usuários da própria barbearia
        if ($user->tipo === 'barbeiro') {
            return $user->barbearia_id === $usuario->barbearia_id;
        }

        return false; // cliente não pode atualizar
    }

    /**
     * Permite deletar usuários.
     */
    public function delete(Usuario $user, Usuario $usuario)
    {
        // Somente admin pode deletar
        return $user->tipo === 'admin';
    }
}
