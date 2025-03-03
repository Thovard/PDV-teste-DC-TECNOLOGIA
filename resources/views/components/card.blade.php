@props([
    'title' => '',
    'headerClass' => 'bg-primary text-white',
    'bodyClass' => '',
    'footer' => null,
    'footerClass' => 'bg-light'
])

@php
    $title = $title ?? '';
    $footer = $footer ?? null;
@endphp

<div class="card shadow mb-4">
    @if($title)
        <div class="card-header {{ $headerClass }}">
            <h4 class="mb-0">{{ $title }}</h4>
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    @if($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
