@props([
    'title' => '',
    'headers' => [],
    'registerButtonText' => '',
    'idRegistreModal' => '',
    'enableSearch' => false,
    'enableExport' => false,
    'id' => '',
])

<style>
    .table-responsive {
        overflow-x: auto;
    }

    .card-header {
        background: #343a40;
        color: white;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }

    #searchInput {
        width: 250px;
    }

    .table thead th {
        text-align: center;
        white-space: nowrap;
    }
</style>

<div class="card shadow border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="m-0">{{ $title }}</h4>
        <div class="d-flex gap-2">
            @if ($enableSearch)
                <div class="input-group input-group-sm">
                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
            @endif
            @if ($enableExport)
                <button id="exportPdf-{{ $id }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Exportar
                </button>
            @endif
            @if ($registerButtonText)
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#{{ $idRegistreModal }}">
                    <i class="bi bi-plus-lg"></i> {{ $registerButtonText }}
                </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="{{ $id }}">
                @if (count($headers) > 0)
                    <thead class="table-dark">
                        <tr>
                            @foreach ($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                @endif
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
            <div class="pagination-container"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Define o ID da tabela dinamicamente
        const tableId = @json($id); // Pega o ID da tabela passado via props
        let rows = $(`#${tableId} tbody tr`), // Usa o ID dinâmico
            perPage = 10;

        function paginateTable(filteredRows = rows) {
            $(".pagination-container").empty();
            filteredRows.hide().slice(0, perPage).show();
            let totalPages = Math.ceil(filteredRows.length / perPage);
            if (totalPages > 1) {
                let pagination = $("<ul class='pagination pagination-sm'></ul>");
                for (let i = 1; i <= totalPages; i++) {
                    $("<li class='page-item'><a href='#' class='page-link'>" + i + "</a></li>").appendTo(
                        pagination);
                }
                pagination.find("li:first").addClass("active");
                pagination.appendTo(".pagination-container");

                $(".pagination-container").on("click", ".page-link", function(e) {
                    e.preventDefault();
                    let page = parseInt($(this).text());
                    filteredRows.hide().slice((page - 1) * perPage, page * perPage).show();
                    $(".page-item").removeClass("active");
                    $(this).parent().addClass("active");
                });
            }
        }

        if ($("#searchInput").length) {
            $("#searchInput").on("input", function() {
                let searchText = $(this).val().toLowerCase();
                let filteredRows = rows.filter(function() {
                    return $(this).text().toLowerCase().includes(searchText);
                });
                rows.hide();
                paginateTable(filteredRows);
            });
        }

        // Verifica se o botão de exportação existe
        if ($(`#exportPdf-${tableId}`).length) {
            $(`#exportPdf-${tableId}`).off("click").on("click", function() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });

                // Usa o título do card como nome do arquivo
                const docTitle = @json($title).trim().replace(/ /g, '_').toLowerCase();
                const fileName = docTitle ? `${docTitle}.pdf` : 'relatorio.pdf';

                // Configuração do título
                doc.setFontSize(16);
                doc.text(@json($title) || 'Relatório de Dados', 14, 15);

                // Captura headers e índices
                const originalHeaders = @json($headers);
                const filteredHeaders = originalHeaders.filter(header => header !== 'Ações');
                const validIndexes = originalHeaders.reduce((acc, header, index) => {
                    if (header !== 'Ações') acc.push(index);
                    return acc;
                }, []);

                // Coleta dados de TODAS as linhas da tabela (ignorando paginação e filtros)
                const data = [];
                $(`#${tableId} tbody tr`).each(function() {
                    const rowData = [];
                    $(this).find("td").each((index, td) => {
                        if (validIndexes.includes(index)) {
                            rowData.push($(td).text().trim());
                        }
                    });
                    data.push(rowData);
                });

                // Gera tabela no PDF
                doc.autoTable({
                    head: [filteredHeaders],
                    body: data,
                    styles: {
                        fontSize: 10,
                        cellPadding: 3,
                        halign: 'center'
                    },
                    margin: {
                        top: 25
                    },
                    tableWidth: 'auto',
                    theme: 'grid',
                    headerStyles: {
                        fillColor: [52, 58, 64], // Cor do cabeçalho (#343a40)
                        fontSize: 12,
                        textColor: 255
                    }
                });

                // Salva o PDF
                doc.save(fileName);
            });
        }
    });
</script>
