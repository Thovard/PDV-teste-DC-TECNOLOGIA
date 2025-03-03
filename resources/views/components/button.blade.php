@props([
    'href' => null,                
    'type' => 'button',            
    'variant' => 'primary',        
    'modalTarget' => null,         
    'class' => '',
    'id' => null               
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
