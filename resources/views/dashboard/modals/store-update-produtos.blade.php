<x-modal id="storModalProduto" title="Cadastrar Produto">
    <form method="POST" action="{{ route('produtos.store') }}">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto (URL)</label>
            <input type="text" id="foto" name="foto" class="form-control">
        </div>
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" id="quantidade" name="quantidade" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="" hidden>Selecione</option>
                <option value="digital">Digital</option>
                <option value="fisico">Físico</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="text" id="preco" name="preco" class="form-control" style="text-align: left;" required>
        </div>
        <div class="d-flex justify-content-center pt-2">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>
</x-modal>

@php
    $produtos = $produtos ?? null;
@endphp
@if ($produtos)
    @foreach ($produtos as $produto)
        <x-modal id="editModalProduto{{ $produto->id }}" title="Editar Produto">
            <form method="POST" action="{{ route('produtos.update', $produto) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nome{{ $produto->id }}" class="form-label">Nome</label>
                    <input type="text" id="nome{{ $produto->id }}" name="nome" class="form-control"
                        value="{{ $produto->nome }}" required>
                </div>
                <div class="mb-3">
                    <label for="foto{{ $produto->id }}" class="form-label">Foto (URL)</label>
                    <input type="text" id="foto{{ $produto->id }}" name="foto" class="form-control"
                        value="{{ $produto->foto }}">
                </div>
                <div class="mb-3">
                    <label for="quantidade{{ $produto->id }}" class="form-label">Quantidade</label>
                    <input type="number" id="quantidade{{ $produto->id }}" name="quantidade" class="form-control"
                        value="{{ $produto->quantidade }}" required>
                </div>
                <div class="mb-3">
                    <label for="tipo{{ $produto->id }}" class="form-label">Tipo</label>
                    <select id="tipo{{ $produto->id }}" name="tipo" class="form-control" required>
                        <option value="" hidden>Selecione</option>
                        <option value="digital" {{ $produto->tipo === 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="fisico" {{ $produto->tipo === 'fisico' ? 'selected' : '' }}>Físico</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="preco{{ $produto->id }}" class="form-label">Preço</label>
                    <input type="text" id="preco{{ $produto->id }}" name="preco" class="form-control"
                        value="{{ $produto->preco }}" style="text-align: left;" required>
                </div>
                <div class="d-flex justify-content-center pt-2">
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </x-modal>
    @endforeach
@endif

@if (session('success'))
<x-toast message="{{ session('success') }}" type="success" title="Sucesso" />
@endif

@if (session('error'))
<x-toast message="{{ session('error') }}" type="error" title="Erro" />
@endif
