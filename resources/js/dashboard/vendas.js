import applyMask from "../masks.js";
import $ from "jquery";
import "bootstrap";

$(document).ready(function () {
    let paymentConfigs = {};
    let produtosData = [];
    let vendaEditId;
    let originalTotal = 0;
    let parcelasData = {};

    // MAIN FUNCTIONS
    function aplicarMascaras() {
        $("input[name='cpf']").each(function () {
            applyMask(this, "cpf");
        });
        $("input[name='telefone']").each(function () {
            applyMask(this, "telefone");
        });
        $("input[name='preco']").each(function () {
            applyMask(this, "dinheiro");
        });
        $("input[name='quantidade']").each(function () {
            applyMask(this, "numero");
        });
    }

    function fetchData() {
        return $.getJSON("/dashboard/vendas/get-default-data").fail(
            handleDataError
        );
    }

    function popularClientes(clientes) {
        const $clienteSelect = $("#cliente")
            .empty()
            .append('<option value="">Venda sem cliente</option>');
        clientes.forEach((cliente) => {
            $clienteSelect.append(new Option(cliente.nome, cliente.id));
        });
    }

    function popularProdutosNoCampo(prods) {
        const produtoSelect = $("#produto")
            .empty()
            .append('<option value="" hidden>Selecione o produto</option>');
        prods.forEach((prod) => {
            produtoSelect.append(
                `<option value="${prod.id}" data-preco="${prod.preco}" data-quantidade="${prod.quantidade}">${prod.nome}</option>`
            );
        });
    }

    function popularPagamentos(configs) {
        paymentConfigs = configs;
        const $pagamentoSelect = $("#pagamento")
            .empty()
            .append('<option value="" hidden>Selecione a forma</option>');
        Object.values(configs).forEach((config) => {
            $pagamentoSelect.append(new Option(config.name, config.slug));
        });
    }

    function popularParcelas() {
        const $parcelasSelect = $("#parcelas").empty();
        const creditConfig = paymentConfigs["credit-card"];
        if (creditConfig) {
            $parcelasSelect.append(
                '<option value="" hidden>Selecione as parcelas</option>'
            );
            for (let i = 1; i <= creditConfig.installment_limit; i++) {
                $parcelasSelect.append(new Option(`${i}x`, i));
            }
        }
    }

    function validarEstoque() {
        let valido = true;
        $(".produto-item").each(function () {
            const $produto = $(this).find(".produto-select option:selected");
            const quantidadeStr = $(this).find(".quantidade-produto").val();
            if (
                !$produto.val() &&
                (!quantidadeStr || parseInt(quantidadeStr) === 0)
            ) {
                return true;
            }
            const quantidade = parseInt(quantidadeStr) || 0;
            const estoque = parseInt($produto.data("quantidade")) || 0;
            const $feedback = $(this).find(".estoque-feedback");

            if (!$produto.val()) {
                $feedback
                    .html("Selecione um produto")
                    .removeClass("text-success text-danger");
                valido = false;
                return false;
            }
            if (quantidade < 1 || isNaN(quantidade)) {
                $feedback
                    .html("Quantidade inválida")
                    .addClass("text-danger")
                    .removeClass("text-success");
                valido = false;
                return false;
            }
            if (quantidade > estoque) {
                $feedback
                    .html(
                        `Estoque insuficiente! <strong>Disponível: ${estoque}</strong>`
                    )
                    .addClass("text-danger")
                    .removeClass("text-success");
                valido = false;
                return false;
            }
            $feedback
                .html(
                    `Estoque suficiente! <strong>Restante: ${
                        estoque - quantidade
                    }</strong>`
                )
                .addClass("text-success")
                .removeClass("text-danger");
        });
        return valido;
    }

    function calcularValores() {
        if (!validarEstoque()) {
            $("#valorProduto, #taxa, #valorTotal").val("R$ 0,00");
            return;
        }

        let valorTotalProdutos = 0;
        $("#produtosContainer .produto-item").each(function () {
            const $produto = $(this).find(".produto-select option:selected");
            const preco = parseFloat($produto.data("preco")) || 0;
            const quantidade =
                parseInt($(this).find(".quantidade-produto").val()) || 0;
            valorTotalProdutos += preco * quantidade;
        });

        const metodo = $("#pagamento").val();
        let parcelas = parseInt($("#parcelas").val()) || 1;
        const config = paymentConfigs[metodo] || {};
        let taxaTotal = 0;

        if (metodo === "credit-card" && config.installment_limit) {
            parcelas = Math.min(parcelas, config.installment_limit);
        }

        if (metodo === "credit-card") {
            if (parcelas === 1 && config.cash_rate) {
                taxaTotal = valorTotalProdutos * (config.cash_rate / 100);
            } else if (parcelas > 1 && config.installment_rate) {
                taxaTotal =
                    valorTotalProdutos * (config.installment_rate / 100);
            }
        } else if (config.cash_rate) {
            taxaTotal = valorTotalProdutos * (config.cash_rate / 100);
        }

        let totalGeral = valorTotalProdutos + taxaTotal;
        if (
            parcelasData &&
            Object.keys(parcelasData).length > 0 &&
            parcelasData.newTotal
        ) {
            console.log(parcelasData)
            totalGeral = parcelasData.newTotal;
        }

        const formatter = new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
            minimumFractionDigits: 2,
        });

        $("#valorProduto").val(formatter.format(valorTotalProdutos));
        $("#taxa").val(formatter.format(taxaTotal));
        $("#valorTotal").val(formatter.format(totalGeral));
    }

    function updateProdutoSelectOptions() {
        let selectedIds = [];
        $(".produto-select").each(function () {
            const val = $(this).val();
            if (val) selectedIds.push(val.toString());
        });
        $(".produto-select").each(function () {
            const currentVal = $(this).val();
            const $select = $(this);
            $select
                .empty()
                .append('<option value="" hidden>Selecione o produto</option>');
            produtosData.forEach(function (produto) {
                if (
                    selectedIds.indexOf(produto.id.toString()) === -1 ||
                    produto.id.toString() === currentVal
                ) {
                    $select.append(
                        `<option value="${produto.id}" data-preco="${produto.preco}" data-quantidade="${produto.quantidade}">${produto.nome}</option>`
                    );
                }
            });
            $select.val(currentVal);
        });
    }

    function adicionarNovoProduto() {
        const index = $(".produto-item").length + 1;
        const novoProduto = $(`
            <div class="row mb-3 d-flex produto-item">
                <div class="col-md-6">
                    <label for="produto_${index}" class="form-label">Produto</label>
                    <div class="input-group">
                        <select id="produto_${index}" name="produto[]" class="form-select produto-select">
                            <option value="" hidden>Selecione o produto</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary py-2" data-bs-toggle="modal" data-bs-target="#storModalProduto">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted estoque-helper"></small>
                </div>
                <div class="col-md-4">
                    <label for="quantidadeProduto_${index}" class="form-label">Quantidade</label>
                    <div class="d-flex flex-column">
                        <input type="number" id="quantidadeProduto_${index}" name="quantidade[]" class="form-control quantidade-produto" placeholder="Qtd" min="1">
                        <span class="bg-transparent border-0">
                            <span class="badge estoque-feedback"></span>
                        </span>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-danger remover-produto">X</button>
                </div>
            </div>
        `);
        $("#produtosContainer").append(novoProduto);
        updateProdutoSelectOptions();
        $(`#produto_${index}, #quantidadeProduto_${index}`).on(
            "change input",
            function () {
                updateProdutoSelectOptions();
                calcularValores();
            }
        );
        novoProduto.find(".remover-produto").on("click", function () {
            $(this).closest(".produto-item").remove();
            updateProdutoSelectOptions();
            calcularValores();
        });
    }

    function enviarVenda() {
        $("#formVenda").on("submit", function (e) {
            e.preventDefault();
            let produtos = [];
            $("select[name='produto[]']").each(function (index) {
                const prod = $(this).val();
                const qtd = $("input[name='quantidade[]']").eq(index).val();
                if (prod) {
                    produtos.push({ produto: prod, quantidade: qtd });
                }
            });
            const formData = {
                _token: $('input[name="_token"]').val(),
                cliente: $("#cliente").val(),
                produtos: produtos,
                pagamento: $("#pagamento").val(),
                parcelas: $("#parcelas").val(),
                valorProduto: $("#valorProduto").val().replace(/\D/g, ""),
                taxa: $("#taxa").val().replace(/\D/g, ""),
                valorTotal: $("#valorTotal").val().replace(/\D/g, ""),
                parcelasData: parcelasData,
            };
            $.ajax({
                url: "/dashboard/vendas/store-venda",
                method: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Sucesso!",
                            text: "Venda registrada com sucesso!",
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text:
                                response.message ||
                                "Ocorreu um erro ao processar a venda",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = "Erro ao enviar formulário: ";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage += Object.values(
                            xhr.responseJSON.errors
                        ).join("\n");
                    } else {
                        errorMessage += xhr.statusText;
                    }
                    Swal.fire({
                        title: "Erro!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        });
    }

    // EDIT MODE FUNCTIONS
    function popularClientesEdit(clientes) {
        const $clienteSelect = $("#clienteEdit")
            .empty()
            .append('<option value="">Venda sem cliente</option>');
        clientes.forEach((cliente) => {
            $clienteSelect.append(new Option(cliente.nome, cliente.id));
        });
    }

    function popularProdutosEditNoCampo(prods) {
        const produtoSelect = $("#produtoEdit")
            .empty()
            .append('<option value="" hidden>Selecione o produto</option>');
        prods.forEach((prod) => {
            produtoSelect.append(
                `<option value="${prod.id}" data-preco="${prod.preco}" data-quantidade="${prod.quantidade}">${prod.nome}</option>`
            );
        });
    }

    function popularPagamentosEdit(configs) {
        paymentConfigs = configs;
        const $pagamentoSelect = $("#pagamentoEdit")
            .empty()
            .append('<option value="" hidden>Selecione a forma</option>');
        Object.values(configs).forEach((config) => {
            $pagamentoSelect.append(new Option(config.name, config.slug));
        });
    }
    function popularParcelasEdit() {
        const $parcelasSelect = $("#parcelasEdit").empty();
        const creditConfig = paymentConfigs["credit-card"];
        if (creditConfig) {
            $parcelasSelect.append(
                '<option value="" hidden>Selecione as parcelas</option>'
            );
            for (let i = 1; i <= creditConfig.installment_limit; i++) {
                $parcelasSelect.append(new Option(`${i}x`, i));
            }
        }
    }

    function validarEstoqueEdit() {
        let valido = true;
        $("#produtosContainerEdit .produto-item").each(function () {
            const $produto = $(this).find(".produto-select option:selected");
            const quantidadeStr = $(this).find(".quantidade-produto").val();
            if (
                !$produto.val() &&
                (!quantidadeStr || parseInt(quantidadeStr) === 0)
            ) {
                return true;
            }
            const quantidade = parseInt(quantidadeStr) || 0;
            const estoque = parseInt($produto.data("quantidade")) || 0;

            const $feedback = $(this).find(".estoque-feedback");
            if (!$produto.val()) {
                $feedback
                    .html("Selecione um produto")
                    .removeClass("text-success text-danger");
                valido = false;
                return false;
            }
            if (quantidade < 1 || isNaN(quantidade)) {
                $feedback
                    .html("Quantidade inválida")
                    .addClass("text-danger")
                    .removeClass("text-success");
                valido = false;
                return false;
            }
            if (quantidade > estoque) {
                $feedback
                    .html(
                        `Estoque insuficiente! <strong>Disponível: ${estoque}</strong>`
                    )
                    .addClass("text-danger")
                    .removeClass("text-success");
                valido = false;
                return false;
            }
            $feedback
                .html(
                    `Estoque suficiente! <strong>Restante: ${
                        estoque - quantidade
                    }</strong>`
                )
                .addClass("text-success")
                .removeClass("text-danger");
        });
        return valido;
    }

    function calcularValoresEdit() {
        if (!validarEstoqueEdit()) {
            $("#valorProdutoEdit, #taxaEdit, #valorTotalEdit").val("R$ 0,00");
            return;
        }
        let valorTotalProdutos = 0;
        $("#produtosContainerEdit .produto-item").each(function () {
            const $produto = $(this).find(".produto-select option:selected");
            const preco = parseFloat($produto.data("preco")) || 0;
            const quantidade =
                parseInt($(this).find(".quantidade-produto").val()) || 0;
            valorTotalProdutos += preco * quantidade;
        });

        const metodo = $("#pagamentoEdit").val();
        let parcelas = parseInt($("#parcelasEdit").val()) || 1;
        const config = paymentConfigs[metodo] || {};
        let taxaTotal = 0;
        if (metodo === "credit-card" && config.installment_limit) {
            parcelas = Math.min(parcelas, config.installment_limit);
        }
        if (metodo === "credit-card") {
            if (parcelas === 1 && config.cash_rate) {
                taxaTotal = valorTotalProdutos * (config.cash_rate / 100);
            } else if (parcelas > 1 && config.installment_rate) {
                taxaTotal =
                    valorTotalProdutos * (config.installment_rate / 100);
            }
        } else if (config.cash_rate) {
            taxaTotal = valorTotalProdutos * (config.cash_rate / 100);
        }
        const totalGeral = valorTotalProdutos + taxaTotal;
        const formatter = new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
            minimumFractionDigits: 2,
        });
        $("#valorProdutoEdit").val(formatter.format(valorTotalProdutos));
        $("#taxaEdit").val(formatter.format(taxaTotal));
        $("#valorTotalEdit").val(formatter.format(totalGeral));
    }

    function updateProdutoSelectOptionsEdit() {
        let selectedIds = [];
        $("#produtosContainerEdit .produto-select").each(function () {
            const val = $(this).val();
            if (val) selectedIds.push(val.toString());
        });
        $("#produtosContainerEdit .produto-select").each(function () {
            const currentVal = $(this).val();
            const $select = $(this);
            $select
                .empty()
                .append('<option value="" hidden>Selecione o produto</option>');
            produtosData.forEach(function (produto) {
                if (
                    selectedIds.indexOf(produto.id.toString()) === -1 ||
                    produto.id.toString() === currentVal
                ) {
                    $select.append(
                        `<option value="${produto.id}" data-preco="${produto.preco}" data-quantidade="${produto.quantidade}">${produto.nome}</option>`
                    );
                }
            });
            $select.val(currentVal);
        });
    }

    function adicionarNovoProdutoEdit() {
        const index = $("#produtosContainerEdit .produto-item").length + 1;
        const novoProduto = $(`
            <div class="row mb-3 d-flex produto-item">
                <div class="col-md-6">
                    <label for="produtoEdit_${index}" class="form-label">Produto</label>
                    <div class="input-group">
                        <select id="produtoEdit_${index}" name="produtoEdit[]" class="form-select produto-select">
                            <option value="" hidden>Selecione o produto</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary py-2" data-bs-toggle="modal" data-bs-target="#storModalProduto">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted estoque-helper"></small>
                </div>
                <div class="col-md-4">
                    <label for="quantidadeProdutoEdit_${index}" class="form-label">Quantidade</label>
                    <div class="d-flex flex-column">
                        <input type="number" id="quantidadeProdutoEdit_${index}" name="quantidadeEdit[]" class="form-control quantidade-produto" placeholder="Qtd" min="1">
                        <span class="bg-transparent border-0">
                            <span class="badge estoque-feedback"></span>
                        </span>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-danger remover-produto">X</button>
                </div>
            </div>
        `);
        $("#produtosContainerEdit").append(novoProduto);
        updateProdutoSelectOptionsEdit();
        $(`#produtoEdit_${index}, #quantidadeProdutoEdit_${index}`).on(
            "change input",
            function () {
                updateProdutoSelectOptionsEdit();
                calcularValoresEdit();
            }
        );
        novoProduto.find(".remover-produto").on("click", function () {
            $(this).closest(".produto-item").remove();
            updateProdutoSelectOptionsEdit();
            calcularValoresEdit();
        });
    }

    function enviarVendaEdit() {
        $("#formEditVenda").on("submit", function (e) {
            e.preventDefault();
            let produtos = [];
            $("select[name='produtoEdit[]']").each(function (index) {
                const prod = $(this).val();
                const qtd = $("input[name='quantidadeEdit[]']").eq(index).val();
                if (prod) {
                    produtos.push({ produto: prod, quantidade: qtd });
                }
            });
            const formData = {
                _token: $('input[name="_token"]').val(),
                _method: "PUT",
                cliente: $("#clienteEdit").val(),
                produtos: produtos,
                pagamento: $("#pagamentoEdit").val(),
                parcelas: $("#parcelasEdit").val(),
                valorProduto: $("#valorProdutoEdit").val().replace(/\D/g, ""),
                taxa: $("#taxaEdit").val().replace(/\D/g, ""),
                valorTotal: $("#valorTotalEdit").val().replace(/\D/g, ""),
            };
            $.ajax({
                url: "/dashboard/vendas/update/" + vendaEditId,
                method: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Sucesso!",
                            text: "Venda atualizada com sucesso!",
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text:
                                response.message || "Erro ao atualizar a venda",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = "Erro ao enviar formulário: ";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage += Object.values(
                            xhr.responseJSON.errors
                        ).join("\n");
                    } else {
                        errorMessage += xhr.statusText;
                    }
                    Swal.fire({
                        title: "Erro!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        });
    }

    function loadVendaForEdit(venda, defaultData) {
        popularClientesEdit(defaultData.clientes);
        if (venda.venda.cliente) {
            $("#clienteEdit").val(venda.venda.cliente.id);
        }

        $("#pagamentoEdit").val(venda.venda.forma_pagamento.slug);
        if (venda.venda.forma_pagamento.slug === "credit-card") {
            $("#parcelasEdit")
                .prop("disabled", false)
                .val(venda.venda.quantidade_parcelas);
        } else {
            $("#parcelasEdit").prop("disabled", true).val("");
        }
        $("#produtosContainerEdit").empty();
        venda.produtos.forEach(function (item, index) {
            const idx = index + 1;
            const isFirst = index === 0;
            const removeButton = isFirst
                ? ""
                : `<button type="button" class="btn btn-danger remover-produto">X</button>`;

            const novoProduto = $(`
                <div class="row mb-3 d-flex produto-item">
                    <div class="col-md-6">
                        <label for="produtoEdit_${idx}" class="form-label">Produto</label>
                        <div class="input-group">
                            <select id="produtoEdit_${idx}" name="produtoEdit[]" class="form-select produto-select">
                                <option value="" hidden>Selecione o produto</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary py-2" data-bs-toggle="modal" data-bs-target="#storModalProduto">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted estoque-helper"></small>
                    </div>
                    <div class="col-md-4">
                        <label for="quantidadeProdutoEdit_${idx}" class="form-label">Quantidade</label>
                        <div class="d-flex flex-column">
                            <input type="number" id="quantidadeProdutoEdit_${idx}" name="quantidadeEdit[]" class="form-control quantidade-produto" placeholder="Qtd" min="1">
                            <span class="bg-transparent border-0">
                                <span class="badge estoque-feedback"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        ${removeButton}
                    </div>
                </div>
            `);
            $("#produtosContainerEdit").append(novoProduto);

            const $select = novoProduto.find("select.produto-select");
            $select.append(
                `<option value="${item.produto.id}" data-preco="${item.produto.preco}" data-quantidade="${item.produto.quantidade}" selected>${item.produto.nome}</option>`
            );
            defaultData.produtos.forEach(function (prod) {
                if (prod.id != item.produto.id) {
                    $select.append(
                        `<option value="${prod.id}" data-preco="${prod.preco}" data-quantidade="${prod.quantidade}">${prod.nome}</option>`
                    );
                }
            });

            novoProduto.find("input.quantidade-produto").val(item.quantidade);

            $select.on("change", function () {
                updateProdutoSelectOptionsEdit();
                calcularValoresEdit();
            });
            novoProduto
                .find("input.quantidade-produto")
                .on("input", function () {
                    updateProdutoSelectOptionsEdit();
                    calcularValoresEdit();
                });
            novoProduto.find(".remover-produto").on("click", function () {
                $(this).closest(".produto-item").remove();
                updateProdutoSelectOptionsEdit();
                calcularValoresEdit();
            });
        });

        calcularValoresEdit();
    }
    //CONFIG DE PARCELAS.
    const calcularTotais = (somaParcelas) => {
        const variacao = somaParcelas - originalTotal;
        const variacaoArredondada = Number(variacao.toFixed(2));

        $("#variacaoTotal")
            .html(
                `<span class="variacao-icon"></span>${formatter.format(
                    variacaoArredondada
                )}`
            )
            .removeClass("text-success text-danger")
            .addClass(variacaoArredondada > 0 ? "text-success" : "text-danger");

        $("#novoTotal").text(
            formatter.format(originalTotal + variacaoArredondada)
        );
    };

    const formatarValorInput = (valor) => {
        return (
            parseFloat(
                valor.replace("R$", "").replace(/\./g, "").replace(",", ".")
            ) || 0
        );
    };

    const gerarParcelasPadrao = (numParcelas, valorTotal) => {
        console.log(numParcelas, valorTotal);
        const $container = $("#parcelasContainer").empty();
        const template = document.getElementById("parcelaTemplate");
        const valorBase = valorTotal / numParcelas;
        let valorAjustado = 0;

        for (let i = 0; i < numParcelas; i++) {
            const clone = document.importNode(template.content, true);
            const $clone = $(clone);
            const valor =
                i === numParcelas - 1
                    ? valorTotal - valorAjustado
                    : Math.floor(valorBase * 100) / 100;

            valorAjustado += valor;

            $clone.find(".numero-parcela").text(i + 1);
            $clone
                .find(".valor-parcela")
                .val(valor.toFixed(2).replace(".", ","));

            const dataVencimento = new Date();
            dataVencimento.setMonth(dataVencimento.getMonth() + (i + 1));
            $clone
                .find(".data-vencimento")
                .val(dataVencimento.toISOString().split("T")[0]);

            applyMask($clone.find(".valor-parcela")[0], "dinheiro");
            $container.append($clone);
        }
        calcularTotais(valorTotal);
    };

    const carregarParcelasSalvas = () => {
        if (!parcelasData.installments.length) return;

        const $container = $("#parcelasContainer").empty();
        const template = document.getElementById("parcelaTemplate");

        parcelasData.installments.forEach((parcela) => {
            const clone = document.importNode(template.content, true);
            const $clone = $(clone);

            $clone.find(".numero-parcela").text(parcela.numero);
            $clone
                .find(".valor-parcela")
                .val(parcela.valor.toFixed(2).replace(".", ","));
            $clone.find(".data-vencimento").val(parcela.vencimento);

            applyMask($clone.find(".valor-parcela")[0], "dinheiro");
            $container.append($clone);
        });

        originalTotal = parcelasData.totalOriginal;
        $("#totalOriginal").text(formatter.format(originalTotal));
        calcularTotais(parcelasData.newTotal);
    };

    // EVENT HANDLERS
    $("#ConfigParcelas").on("click", () => {
        const numParcelas = parseInt($("#parcelas").val()) || 1;
        const valorTotal =
            parseFloat($("#valorTotal").val().replace(/\D/g, "")) / 100;

        originalTotal = valorTotal;
        $("#totalOriginal").text(formatter.format(originalTotal));

        if (
            parcelasData &&
            Object.keys(parcelasData).length > 0 &&
            parcelasData.installments?.length > 0 &&
            parcelasData.parcelas === numParcelas
        ) {
            console.log("aqui1");
            carregarParcelasSalvas();
        } else {
            console.log("aqui2");
            gerarParcelasPadrao(numParcelas, valorTotal);
        }

        // Garante que a modal seja recriada corretamente
        var modalElement = document.getElementById("editParcelasModal");

        // Remove qualquer instância anterior para evitar conflitos
        var oldModal = bootstrap.Modal.getInstance(modalElement);
        if (oldModal) {
            oldModal.dispose();
        }

        var modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    $("#parcelasContainer").on("input", ".valor-parcela", function () {
        applyMask(this, "dinheiro");

        let somaParcelas = 0;
        $(".valor-parcela").each(function () {
            somaParcelas += formatarValorInput($(this).val());
        });

        calcularTotais(somaParcelas);
    });

    $("#salvarParcelas").on("click", async function () {
        const parcelasConfig = [];
        let error = false;

        $(".parcela-item").each(function (index) {
            const $this = $(this);
            const valor = formatarValorInput(
                $this.find(".valor-parcela").val()
            );
            const data = $this.find(".data-vencimento").val();

            $this.find(".is-invalid").removeClass("is-invalid");

            if (isNaN(valor) || valor <= 0) {
                $this.find(".valor-parcela").addClass("is-invalid");
                error = true;
            }
            if (!data) {
                $this.find(".data-vencimento").addClass("is-invalid");
                error = true;
            }

            parcelasConfig.push({
                numero: index + 1,
                valor: valor,
                vencimento: data,
            });
        });

        if (error)
            return Swal.fire("Erro", "Verifique os campos destacados", "error");

        const numParcelas = parseInt($("#parcelas").val()) || 1;
        const totalParcelas = parcelasConfig.reduce(
            (acc, parcela) => acc + parcela.valor,
            0
        );

        parcelasData = {
            parcelas: numParcelas,
            installments: parcelasConfig,
            totalOriginal: originalTotal,
            variation: totalParcelas - originalTotal,
            newTotal: totalParcelas,
        };
      await calcularValores();
        Swal.fire({
            title: "Sucesso!",
            text: "Parcelas atualizadas com sucesso!",
            icon: "success",
            confirmButtonText: "OK",
        }).then((result) => {
            var modalElement = document.getElementById("editParcelasModal");
            var modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.hide();
                modal.dispose(); // Remove completamente a instância do modal
            }

            // Aguarda um curto tempo para remover o backdrop e evitar bugs visuais
            setTimeout(() => {
                document
                    .querySelectorAll(".modal-backdrop")
                    .forEach((el) => el.remove());
                document.body.classList.remove("modal-open"); // Remove a classe que bloqueia o scroll
            }, 300);
        });
    });

    $("#reiniciarParcelas").click(function () {
        parcelasData = {
            installments: [],
            totalOriginal: 0,
            variation: 0,
            newTotal: 0,
        };

        const numParcelas = parseInt($("#parcelas").val()) || 1;
        const valorTotal =
            parseFloat($("#valorTotal").val().replace(/\D/g, "")) / 100;

        originalTotal = valorTotal;
        gerarParcelasPadrao(numParcelas, valorTotal);
        $("#totalOriginal").text(formatter.format(originalTotal));
    });

    $("#parcelas").on("change", function () {
        const numParcelas = parseInt($(this).val()) || 1;
        const parcelas = parseInt($(this).val()) || 0;
        $("#ConfigParcelas").prop("disabled", parcelas <= 1);
        if (parcelasData && Object.keys(parcelasData).length > 0) {
            parcelasData.parcelas = numParcelas;
            parcelasData.total = calcularNovoTotal(parcelasData);
        }
        calcularValores();
    });
    $("#edit").on("click", async function () {
        const id = $(this).attr("dataid");
        try {
            const venda = await $.getJSON("/dashboard/vendas/get-venda/" + id);
            vendaEditId = venda.venda.id;

            const defaultData = await fetchData();
            produtosData = defaultData.produtos;
            popularPagamentosEdit(defaultData.paymentConfigs);
            popularParcelasEdit();

            loadVendaForEdit(venda, defaultData);

            enviarVendaEdit();
        } catch (error) {
            handleDataError(error);
        }
    });
    $("#pagamentoEdit").on("change", function () {
        let pagamentoSelecionado = $(this)
            .find("option:selected")
            .text()
            .trim()
            .toLowerCase();
        let parcelasField = $("#parcelasEdit");

        if (pagamentoSelecionado === "cartão de crédito") {
            parcelasField.prop("disabled", false);
        } else {
            parcelasField.prop("disabled", true).val("");
        }
    });
    $(".modal").on("show.bs.modal", function () {
        let modal = $(this);
        let pagamentoSelecionado = modal
            .find("#pagamentoEdit option:selected")
            .text()
            .trim()
            .toLowerCase();
        let parcelasField = modal.find("#parcelasEdit");
        let quantidadeParcelas = modal
            .find("#parcelasEdit")
            .data("quantidade-parcelas");

        if (pagamentoSelecionado === "cartão de crédito") {
            parcelasField.prop("disabled", false);
            if (quantidadeParcelas) {
                parcelasField.val(quantidadeParcelas);
            }
        } else {
            parcelasField.prop("disabled", true).val("");
        }
    });

    // COMMON FUNCTIONS
    function handleDataError(jqXHR, textStatus, errorThrown) {
        console.error("Erro ao carregar dados:", textStatus, errorThrown);
        alert("Erro ao carregar dados para o formulário");
    }
    const formatter = new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    });

    // INITIALIZATION
    async function init() {
        aplicarMascaras();

        $("#produtosContainer").addClass("container");
        $(".row.mb-3.d-flex").has("#produto").addClass("produto-item");
        $("#produto").attr("name", "produto[]").addClass("produto-select");
        $("#quantidadeProduto")
            .attr("name", "quantidade[]")
            .addClass("quantidade-produto");
        $("#estoqueFeedback").removeAttr("id").addClass("estoque-feedback");
        $("#estoqueHelper").removeAttr("id").addClass("estoque-helper");

        try {
            const data = await fetchData();
            calcularValores();
            calcularValoresEdit();
            popularProdutosNoCampo(data.produtos);
            popularClientes(data.clientes);
            produtosData = data.produtos;
            popularPagamentos(data.paymentConfigs);
            popularParcelas();
            enviarVenda();

            $("#btnAdicionarProduto").on("click", function () {
                adicionarNovoProduto();
            });
            $("#btnAdicionarProdutoEdit").on("click", function () {
                adicionarNovoProdutoEdit();
            });
            $("#pagamento").on("change", function () {
                const isCredit = $(this).val() === "credit-card";
                $("#parcelas").prop("disabled", !isCredit);
                $("#ConfigParcelas").prop("disabled", true);
                if (!isCredit) {
                    $("#parcelas").val("");
                } else {
                    $("#parcelas").val(1);
                }
                calcularValores();
            });
            $("#parcelas").on("change", function () {
                calcularValores();
            });
            $(
                ".produto-item .produto-select, .produto-item .quantidade-produto"
            ).on("change input", function () {
                updateProdutoSelectOptions();
                calcularValores();
            });
        } catch (error) {
            handleDataError(error);
        }
    }
    init();
});
