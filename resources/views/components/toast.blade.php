@props([
    'message',
    'title' => 'Sucesso',
    'delay' => 2500,
    'type' => 'success'
])
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: '{{ $title }}',
        text: '{{ $message }}',
        icon: '{{ $type }}',
        timer: {{ $delay }},
        showConfirmButton: true,
        toast: true,
    });
});
</script>
