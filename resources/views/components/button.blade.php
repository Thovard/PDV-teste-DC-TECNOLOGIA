@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'modalTarget' => null,
    'class' => '',
    'id' => null,
    'disabled' => false,
])

<button
    @if($href) onclick="location.href='{{ $href }}'" @endif
    type="{{ $type }}"
    id="{{ $id }}"
    class="btn btn-{{ $variant }} {{ $class }}"
    @if ($disabled) disabled @endif
    @if($modalTarget) data-bs-toggle="modal" data-bs-target="#{{ $modalTarget }}" @endif
>
    {{ $slot }}
</button>
