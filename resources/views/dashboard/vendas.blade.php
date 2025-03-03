@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            Registrar Venda
        </div>
        <div class="card-body">
            <form id="formVenda">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-7">
                        <label for="cliente" class="form-label">Cliente</label>
                        <div class="input-group">
                            <select id="cliente" name="cliente" class="form-select">
                                <option value="">Venda sem cliente</option>
                            </select>
                            <x-button type="button" variant="outline-secondary" modalTarget="storModalCliente">
                                <i class="bi bi-plus-lg"></i>
                            </x-button>
                        </div>
                    </div>
                </div>
                <div id="produtosContainer" class="row mb-2 d-flex produto-item">
                    <div class="row mb-2 d-flex produto-item">
                        <div class="col-md-6">
                            <label for="produto" class="form-label">Produto</label>
                            <div class="input-group">
                                <select id="produto" name="produto" class="form-select">
                                    <option value="" hidden>Selecione o produto</option>
                                </select>
                                <x-button type="button" variant="outline-secondary" modalTarget="storModalProduto"
                                    class="py-2">
                                    <i class="bi bi-plus-lg"></i>
                                </x-button>
                            </div>
                            <small id="estoqueHelper" class="form-text text-muted"></small>
                        </div>
                        <div class="col-md-4">
                            <label for="quantidadeProduto" class="form-label">Quantidade</label>
                            <div class="d-flex flex flex-column">
                                <input type="number" id="quantidadeProduto" name="quantidadeProduto" class="form-control"
                                    placeholder="Qtd" min="1">
                                <span class="bg-transparent border-0">
                                    <span id="estoqueFeedback" class="badge"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start">
                    <x-button type="button" variant="outline-primary" id="btnAdicionarProduto" class="mb-3">
                        Adicionar Produto
                    </x-button>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pagamento" class="form-label">Forma de Pagamento</label>
                        <select id="pagamento" name="pagamento" class="form-select">
                            <option value="" hidden>Selecione a forma</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="parcelas" class="form-label">Parcelas</label>
                        <select id="parcelas" name="parcelas" class="form-select" disabled>
                            <option value="" hidden>Selecione as parcelas</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Valor do Produto</span>
                            <input type="text" class="form-control" id="valorProduto" value="" readonly
                                style="text-align: right;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Taxa</span>
                            <input type="text" class="form-control" id="taxa" value="" readonly
                                style="text-align: right;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Valor Total</span>
                            <input type="text" class="form-control" value="" id="valorTotal" readonly
                                style="text-align: right;">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center pt-4">
                    <x-button type="submit" variant="primary" class="w-25">
                        Registrar Venda
                    </x-button>
                </div>
            </form>
        </div>
    </div>
    <x-table title="Histórico de Vendas" id="vendas" :headers="['ID', 'Cliente', 'Pagamento', 'Parcelas', 'Valor', 'Ações']" enableSearch="true" enableExport="true">
        @foreach ($vendas as $venda)
            <tr>
                <td>{{ $venda->id }}</td>
                <td>{{ $venda->cliente ? $venda->cliente->nome : 'Venda sem cliente' }}</td>
                <td>{{ $venda->formaPagamento ? $venda->formaPagamento->name : '' }}</td>
                <td>{{ $venda->quantidade_parcelas > 1 ? $venda->quantidade_parcelas . 'x' : '-' }}</td>
                <td>{{ $venda->total_formatado ?? number_format($venda->total, 2, ',', '.') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-warning dropdown-toggle" type="button"
                            id="dropdownMenuButton{{ $venda->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            Ações
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $venda->id }}">
                            <li>
                                <x-hyperlink-button modalTarget="editModal{{ $venda->id }}"
                                    href="{{ url('/dashboard/vendas/get-venda/' . $venda->id) }}"
                                    class="dropdown-item edit-btn" id="edit" dataId="{{ $venda->id }}">
                                    Editar
                                </x-hyperlink-button>
                            </li>
                            @if ($venda->parcelas->count() > 0)
                                <li>
                                    <x-hyperlink-button modalTarget="parcelasModal{{ $venda->id }}"
                                        class="dropdown-item">
                                        Parcelas
                                    </x-hyperlink-button>
                                </li>
                            @endif
                            <li>
                                <x-confirmation-form action="{{ route('vendas.destroy', $venda->id) }}" method="POST"
                                    message="Tem certeza que deseja excluir esta venda?" title="Excluir Venda">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item">Excluir</button>
                                </x-confirmation-form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>
    @vite('resources/js/dashboard/vendas.js')
    @include('dashboard.modals.parcelas')
    @include('dashboard.modals.edit-venda-modal')
    @include('dashboard.modals.store-update-cliente')
    @include('dashboard.modals.store-update-produtos')
@endsection
