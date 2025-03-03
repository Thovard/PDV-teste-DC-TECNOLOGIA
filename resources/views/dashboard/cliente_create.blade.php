@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="m-4">
        <x-table
        title="Lista de Clientes"
        registerButtonText="Novo Cliente"
        :headers="['ID', 'Nome', 'Email', 'CPF', 'Telefone', 'Ações']"
        idRegistreModal='storModalCliente'>

            @foreach ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id }}</td>
                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td data-mask="cpf">{{ $cliente->cpf }}</td>
                    <td data-mask="telefone">{{ $cliente->telefone }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning" name="editar" data-bs-toggle="modal"
                            data-bs-target="#editModalCliente{{ $cliente->id }}">
                            Editar
                        </button>
                        <form action="{{ route('cliente.destroy', $cliente) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach

        </x-table>
    </div>

    @include('dashboard.modals.store-update-cliente')

    @vite('resources/js/dashboard/cliente.js')
@endsection
