@props([
    'id' => 'defaultModalId',
    'title' => 'Título da Modal',
    'size' => ''
])
@php
    $footer = $footer ?? null;
    switch ($size) {
        case'small':
            $modalSize ='modal-sm';
            break;
        case 'large':
            $modalSize ='modal-lg';
            break;
        case 'extra-large':
            $modalSize = 'modal-xl';
            break;
        default:
            $modalSize = '';
    }
@endphp
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label"
    aria-hidden="true">
    <div class="modal-dialog  {{ $modalSize }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if ($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
