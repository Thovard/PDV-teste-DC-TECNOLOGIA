@foreach ($vendas as $venda)
    <x-modal id="editModal{{ $venda->id }}" title="Editar Venda" size="extra-large">
        <form id="formEditVenda" method="POST">
            @csrf
            @method('PUT')
            <!-- Cliente -->
            <div class="row mb-3">
                <div class="col-md-7">
                    <label for="clienteEdit" class="form-label">Cliente</label>
                    <div class="input-group">
                        <select id="clienteEdit" name="clienteEdit" class="form-select">
                            <option value="">Venda sem cliente</option>
                        </select>
                        <x-button type="button" variant="outline-secondary" modalTarget="storModalCliente">
                            <i class="bi bi-plus-lg"></i>
                        </x-button>
                    </div>
                </div>
            </div>

            <!-- Produtos -->
            <div id="produtosContainerEdit" class="row mb-2">
                <div class="row mb-2 d-flex produto-item">
                    <div class="col-md-6">
                        <label for="produtoEdit_1" class="form-label">Produto</label>
                        <div class="input-group">
                            <select id="produtoEdit_1" name="produtoEdit[]" class="form-select produto-select">
                                <option value="" hidden>Selecione o produto</option>
                            </select>
                            <x-button type="button" variant="outline-secondary" modalTarget="storModalProduto" class="py-2">
                                <i class="bi bi-plus-lg"></i>
                            </x-button>
                        </div>
                        <small class="form-text text-muted estoque-helper-edit"></small>
                    </div>
                    <div class="col-md-4">
                        <label for="quantidadeProdutoEdit_1" class="form-label">Quantidade</label>
                        <div class="d-flex flex-column">
                            <input type="number" id="quantidadeProdutoEdit_1" name="quantidadeEdit[]" class="form-control" placeholder="Qtd" min="1">
                            <span class="bg-transparent border-0">
                                <span class="badge estoque-feedback-edit"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão para adicionar novos produtos -->
            <div class="d-flex justify-content-start">
                <x-button type="button" variant="outline-primary" id="btnAdicionarProdutoEdit" class="mb-3">
                    Adicionar Produto
                </x-button>
            </div>

            <!-- Pagamento e Parcelas -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="pagamentoEdit" class="form-label">Forma de Pagamento</label>
                    <select id="pagamentoEdit" name="pagamentoEdit" class="form-select">
                        <option value="" hidden>Selecione a forma</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="parcelasEdit" class="form-label">Parcelas</label>
                    <select id="parcelasEdit" name="parcelasEdit" class="form-select" disabled>
                        <option value="" hidden>Selecione as parcelas</option>
                    </select>
                </div>
            </div>

            <!-- Valores -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Valor do Produto</span>
                        <input type="text" class="form-control" id="valorProdutoEdit" readonly style="text-align: right;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Taxa</span>
                        <input type="text" class="form-control" id="taxaEdit" readonly style="text-align: right;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Valor Total</span>
                        <input type="text" class="form-control" id="valorTotalEdit" readonly style="text-align: right;">
                    </div>
                </div>
            </div>

            <!-- Botão de Envio -->
            <div class="d-flex justify-content-center pt-4">
                <x-button type="submit" variant="primary" class="w-25">
                    Atualizar Venda
                </x-button>
            </div>
        </form>
    </x-modal>
@endforeach
