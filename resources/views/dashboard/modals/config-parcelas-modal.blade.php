<style>
    .card {

        border-width: 2px;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .variacao-icon::before {
        font-family: "bootstrap-icons";
        margin-right: 0.3rem;
        font-size: 0.9em;
    }

    .text-success .variacao-icon::before {
        content: "\F138";
        color: #198754;
    }

    .text-danger .variacao-icon::before {
        content: "\F13A";
        color: #dc3545;
    }
</style>
<x-modal id="editParcelasModal" title="Configuração de parcelas" size="large">
    <div class="modal-body">
        <div id="parcelasContainer" class="container mb-4"></div>

        <div class="row g-3 mt-4">
            <div class="col-md-4">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-currency-dollar fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="card-title text-muted mb-1 small">TOTAL DA VENDA</h6>
                            <span id="totalOriginal" class="h4 fw-bold text-primary">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-secondary shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-arrow-up-down fs-3 text-muted"></i>
                        </div>
                        <div>
                            <h6 class="card-title text-muted mb-1 small">VARIAÇÃO</h6>
                            <span id="variacaoTotal" class="h4 fw-bold">
                                <span class="variacao-icon"></span>
                                R$ 0,00
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-receipt fs-3 text-success"></i>
                        </div>
                        <div>
                            <h6 class="card-title text-muted mb-1 small">NOVO TOTAL</h6>
                            <span id="novoTotal" class="h4 fw-bold text-success">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="salvarParcelas">Salvar</button>
        <button type="button" class="btn btn-secondary" id="reiniciarParcelas">Reiniciar</button>
    </div>
</x-modal>

<template id="parcelaTemplate">
    <div class="row mb-3 parcela-item">
        <div class="col-md-5">
            <label class="form-label">Valor da Parcela</label>
            <div class="input-group">
                <span class="input-group-text">R$</span>
                <input type="text" class="form-control valor-parcela" data-mask="dinheiro">
            </div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Data de Vencimento</label>
            <input type="date" class="form-control data-vencimento">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <span class="badge bg-primary">Parcela #<span class="numero-parcela"></span></span>
        </div>
    </div>
</template>
