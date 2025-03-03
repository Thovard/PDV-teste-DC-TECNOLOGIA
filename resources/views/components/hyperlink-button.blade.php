@props([
    'href' => null,
    'modalTarget' => null,
    'class' => '',
    'id' => '',
    'onclick' => '',
    'dataId' => '',
])
<a
    @if($href && !$modalTarget)
        href="{{ $href }}"
    @else
        href="javascript:void(0);"
    @endif
    class="btn {{ $class }}"
    @if($modalTarget) data-bs-toggle="modal" data-bs-target="#{{ $modalTarget }}" @endif
    id="{{ $id }}"
    onclick="{{ $onclick }}"
    @if($dataId) dataId="{{ $dataId }}" @endif>
    {{ $slot }}
</a>
