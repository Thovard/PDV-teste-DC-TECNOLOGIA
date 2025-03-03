@extends('layouts.dashboard')

@section('dashboard-content')
<div class="m-4">
    <x-table
    title="Lista de Produtos"
    registerButtonText="Novo Produto"
    :headers="['ID', 'Nome', 'Foto', 'Quantidade', 'Tipo', 'Preço', 'Ações']"
    idRegistreModal='storModalProduto'
    enableSearch="true" enableExport="true" id="produtos">

    @foreach ($produtos as $produto)
    <tr>
        <td>{{ $produto->id }}</td>
        <td>{{ $produto->nome }}</td>
        <td>
            @if ($produto->foto)
                <img src="{{ $produto->foto }}" alt="{{ $produto->nome }}" style="max-height: 50px;">
            @else
                N/A
            @endif
        </td>
        <td>{{ $produto->quantidade }}</td>
        <td>{{ ucfirst($produto->tipo) }}</td>
        <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
        <td>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModalProduto{{ $produto->id }}">
                Editar
            </button>
            <form action="{{ route('produtos.destroy', $produto) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </td>
    </tr>
    @endforeach

</x-table>
</div>
@include('dashboard.modals.store-update-produtos')
@vite('resources/js/dashboard/produtos.js')
@endsection
