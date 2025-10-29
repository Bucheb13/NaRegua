<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Barbearia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Lista usuários (clientes ou barbeiros) com filtro por tipo e barbearia.
     */
    public function index(Request $request)
    {
        $usuarioLogado = Auth::user();

        if ($usuarioLogado->tipo === 'cliente') {
            abort(403, 'Acesso negado');
        }

        // Filtro de barbearia (para admin)
        if ($usuarioLogado->tipo === 'admin') {
            $barbearias = Barbearia::all();
            $barbeariaSelecionada = $request->barbearia_id ?? null;

            // Admin vê todos os usuários (clientes e barbeiros)
            $usuarios = Usuario::with('barbearia')
                ->when($barbeariaSelecionada, fn($query) => $query->where('barbearia_id', $barbeariaSelecionada))
                ->get();

        } else {
            // Barbeiro vê clientes e barbeiros da própria barbearia, mas não admins
            $barbearias = collect([$usuarioLogado->barbearia]);
            $barbeariaSelecionada = $usuarioLogado->barbearia_id;

            $usuarios = Usuario::with('barbearia')
                ->where('barbearia_id', $usuarioLogado->barbearia_id)
                ->whereIn('tipo', ['cliente','barbeiro'])
                ->get();
        }

        // Garante que a variável exista para a view
        $barbeariaSelecionada = $barbeariaSelecionada ?? null;

        return view('pages.usuarios.index', compact(
            'usuarios',
            'barbearias',
            'usuarioLogado',
            'barbeariaSelecionada'
        ));
    }

    /**
     * Formulário para criar novo usuário
     */
    public function create()
    {
        $usuarioLogado = Auth::user();

        if ($usuarioLogado->tipo === 'cliente') {
            abort(403, 'Acesso negado');
        }

        $barbearias = $usuarioLogado->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuarioLogado->barbearia]);

        return view('pages.usuarios.create', compact('barbearias', 'usuarioLogado'));
    }

    /**
     * Salva novo usuário (cliente ou barbeiro)
     */
    public function store(Request $request)
{
    $usuarioLogado = Auth::user();

    if ($usuarioLogado->tipo === 'cliente') {
        abort(403, 'Acesso negado');
    }

    // --- REGRA 1: bloquear criação de admin por barbeiro/cliente
    if ($usuarioLogado->tipo !== 'admin' && $request->tipo === 'admin') {
        return back()->withErrors(['tipo' => 'Você não tem permissão para criar um usuário admin.'])->withInput();
    }

    // Validação de dados
    $request->validate([
        'nome' => 'required|string|max:255',
        'email' => 'required|email|unique:usuarios,email', // se quiser nullable eu ajusto depois
        'tipo'  => 'required|in:admin,barbeiro,cliente',
        'telefone' => 'nullable|string|max:20',
        'barbearia_id' => 'nullable|exists:barbearias,id',
        'senha' => 'nullable|min:6',
    ]);

    // --- REGRA 2: senha default se vazia
    $senha = $request->password ? $request->password : '123456';

    // --- Verifica barbearia correta
    $barbeariaId = $request->barbearia_id;
    if ($usuarioLogado->tipo !== 'admin') {
        // Se não for admin, força barbearia do logado
        $barbeariaId = $usuarioLogado->barbearia_id;
    }

    $usuario = Usuario::create([
        'nome' => $request->nome,
        'email' => $request->email,
        'telefone' => $request->telefone,
        'tipo' => $request->tipo,
        'barbearia_id' => $barbeariaId,
        'senha' => bcrypt($senha),  // HASH correto
    ]);

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'telefone' => $usuario->telefone,
            'email' => $usuario->email,
        ], 201);
    }

    return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
}


    /**
     * Formulário de edição
     */
    public function edit(Usuario $usuario)
    {
        $usuarioLogado = Auth::user();

        if ($usuarioLogado->tipo === 'cliente') {
            abort(403, 'Acesso negado');
        }

        if ($usuarioLogado->tipo === 'barbeiro' && $usuario->barbearia_id !== $usuarioLogado->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        $barbearias = $usuarioLogado->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuarioLogado->barbearia]);

        return view('pages.usuarios.edit', compact('usuario', 'barbearias', 'usuarioLogado'));
    }

    /**
     * Atualiza usuário existente
     */
    public function update(Request $request, Usuario $usuario)
    {
        $usuarioLogado = Auth::user();

        if ($usuarioLogado->tipo === 'cliente') {
            abort(403, 'Acesso negado');
        }

        if ($usuarioLogado->tipo === 'barbeiro' && $usuario->barbearia_id !== $usuarioLogado->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'tipo' => 'required|in:admin,barbeiro,cliente',
            'telefone' => 'nullable|string|max:20',
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'senha' => 'nullable|min:6',
        ]);

        $usuario->nome = $request->nome;
        $usuario->email = $request->email;
        $usuario->telefone = $request->telefone;

        if ($usuarioLogado->tipo === 'admin') {
            $usuario->tipo = $request->tipo;
            $usuario->barbearia_id = $request->barbearia_id;
        }

        if ($request->filled('senha')) {
            $usuario->senha = $request->senha; // mutator encripta automaticamente

        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Exclui usuário (apenas admin)
     */
    public function destroy(Usuario $usuario)
    {
        $usuarioLogado = Auth::user();

        if ($usuarioLogado->tipo !== 'admin') {
            abort(403, 'Acesso negado');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário deletado com sucesso!');
    }

    /**
     * Busca AJAX para autocomplete (ex: vendas)
     */
    public function buscar(Request $request)
    {
        $termo = $request->get('q', '');
        // pode-se passar ?tipo=cliente para garantir o filtro
        $tipo = $request->get('tipo', 'cliente');

        $query = Usuario::where('tipo', $tipo);

        if (!empty($termo)) {
            // operador compatível com o driver (ILIKE em PG, LIKE em outros)
            $driver = DB::connection()->getDriverName();
            $operator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('nome', $operator, "%{$termo}%");
        }

        $usuarios = $query->limit(10)->get(['id', 'nome', 'telefone']);

        return response()->json($usuarios);
    }
}