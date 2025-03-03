@props([
    'message' => 'Tem certeza que deseja excluir?',
    'title' => 'Confirmação',
    'confirmButtonText' => 'Sim, excluir!',
    'cancelButtonText' => 'Cancelar'
])
<form {{ $attributes->merge(['onsubmit' => 'event.preventDefault(); showConfirmation(this);']) }}>
    {{ $slot }}
</form>
<script>
    function showConfirmation(form) {
        Swal.fire({
            title: '{{ $title }}',
            text: '{{ $message }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ $confirmButtonText }}',
            cancelButtonText: '{{ $cancelButtonText }}'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
