@props([
    'href' => null,                // Link para redirecionamento (opcional)
    'type' => 'button',            // 'button', 'submit', etc.
    'variant' => 'primary',        // Bootstrap: primary, secondary, success, danger, warning, info, light, dark
    'modalTarget' => null,         // ID da modal a ser aberta, se for um botão para abrir modal
    'class' => '',
    'id' => null               // Classes CSS adicionais
])

<button
    @if($href) onclick="location.href='{{ $href }}'" @endif
    type="{{ $type }}"
    id="{{ $id }}"
    class="btn btn-{{ $variant }} {{ $class }}"
    @if($modalTarget) data-bs-toggle="modal" data-bs-target="#{{ $modalTarget }}" @endif
>
    {{ $slot }}
</button>
