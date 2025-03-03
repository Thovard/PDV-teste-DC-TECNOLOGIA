<x-modal id="storModalCliente" title="Cadastrar Cliente">
    <form method="POST" action="{{ route('cliente.store') }}">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" id="cpf" name="cpf" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" id="telefone" name="telefone" class="form-control" required>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>
</x-modal>

@php
    $clientes = $clientes ?? null;
@endphp
@if ($clientes)
    @foreach ($clientes as $cliente)
        <x-modal id="editModalCliente{{ $cliente->id }}" title="Editar Cliente">
            <form method="POST" action="{{ route('cliente.update', $cliente) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" id="nome" name="nome" class="form-control"
                        value="{{ $cliente->nome }}" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ $cliente->email }}" required>
                </div>
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" value="{{ $cliente->cpf }}"
                        required>
                </div>
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" id="telefone" name="telefone" class="form-control"
                        value="{{ $cliente->telefone }}" required>
                </div>
                <div class="d-flex justify-content-end">
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
