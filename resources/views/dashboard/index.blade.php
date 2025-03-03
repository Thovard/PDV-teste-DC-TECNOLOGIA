@extends('layouts.dashboard')

@section('dashboard-content')
    <h1>Dashboard</h1>
    <p>Bem-vindo ao seu painel de controle.</p>

    <script>
    window.dadosDashboard = {
        usuarios: {{ $clientesCount }},
        produtos: {{ $produtosCount }},
        vendas: {{ $vendasCount }},
        clientes: {!! json_encode($clientesMaisCompraram ?? []) !!},
        produtosVendidos: {!! json_encode($produtosMaisVendidos ?? []) !!},
        vendasPagas: {{ $statusVendas['pago'] ?? 0 }},
        vendasPendentes: {{ $statusVendas['pendente'] ?? 0 }}
    };
    </script>

    <div class="row">
        <div class="col-md-6">
            <x-card title="Resumo do Sistema">
                <canvas id="chartResumo" height="200"></canvas>
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card title="Clientes que mais compraram">
                <canvas id="chartClientes" height="200"></canvas>
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card title="Produtos mais vendidos">
                <canvas id="chartProdutos" height="200"></canvas>
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card title="Status das Vendas">
                <canvas id="chartStatusVendas" height="200"></canvas>
            </x-card>
        </div>
    </div>

    <x-table title="Clientes que mais compraram" :headers="['Nome', 'Quantidade de Compras']">
        @foreach ($clientesMaisCompraram as $cliente)
            <tr>
                <td>{{ $cliente->nome }}</td>
                <td>{{ $cliente->total_compras }}</td>
            </tr>
        @endforeach
    </x-table>

    <x-table title="Produtos mais vendidos" :headers="['Nome', 'Quantidade Vendida']">
        @foreach ($produtosMaisVendidos as $produto)
            <tr>
                <td>{{ $produto->nome }}</td>
                <td>{{ $produto->quantidade }}</td>
            </tr>
        @endforeach
    </x-table>

    @vite('resources/js/dashboard/dashboard.js')
@endsection
