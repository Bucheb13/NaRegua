@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Clientes</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('clientes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Novo Cliente</a>

    @if($clientes->count())
        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr class="bg-[#CFAE70] text-[#5A3825]">
                    <th class="border px-4 py-2">Nome</th>
                    <th class="border px-4 py-2">Telefone</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Barbearia</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    <tr class="border">
                        <td class="px-4 py-2">{{ $cliente->nome }}</td>
                        <td class="px-4 py-2">{{ $cliente->telefone ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $cliente->email ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $cliente->barbearia->nome ?? '-' }}</td>
                        <td class="px-4 py-2 capitalize">{{ $cliente->status }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="text-blue-600">Editar</a> |
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mt-4 text-gray-600">Nenhum cliente cadastrado.</p>
    @endif
</div>
@endsection
