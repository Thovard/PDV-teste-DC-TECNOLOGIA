@foreach ($vendas as $venda)
    <x-modal id="parcelasModal{{ $venda->id }}" title="Parcelas">
        <x-table title="Parcelas" :headers="['Parcela', 'Valor', 'Vencimento', 'Status']">
            @foreach ($venda->parcelas as $parcela)
                <tr>
                    <td>{{ $loop->iteration }} / {{ $loop->count }}</td>
                    <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</td>
                    <td>{{ $parcela->status }}</td>
                </tr>
            @endforeach
        </x-table>
        <script>
            $('#parcelasModal{{ $venda->id }}').on('shown.bs.modal', function() {
                var $modal = $(this);
                var $table = $modal.find('table');
                var $rows = $table.find('tbody tr');
                var perPage = 10;
                if ($rows.length > perPage) {
                    $rows.hide();
                    $rows.slice(0, perPage).show();
                    var totalPages = Math.ceil($rows.length / perPage);
                    var $pagination = $('<ul class="pagination pagination-sm mt-2 pagination-container"></ul>');
                    for (var i = 1; i <= totalPages; i++) {
                        $pagination.append('<li class="page-item ' + (i === 1 ? 'active' : '') +
                            '"><a class="page-link" href="#">' + i + '</a></li>');
                    }
                    $modal.find('.pagination-container').remove();
                    $pagination.insertAfter($table);
                    $pagination.find('a').on('click', function(e) {
                        e.preventDefault();
                        var page = parseInt($(this).text());
                        var start = (page - 1) * perPage;
                        var end = start + perPage;
                        $rows.hide().slice(start, end).show();
                        $pagination.find('li').removeClass('active');
                        $(this).parent().addClass('active');
                    });
                }
            });
        </script>
    </x-modal>
@endforeach
