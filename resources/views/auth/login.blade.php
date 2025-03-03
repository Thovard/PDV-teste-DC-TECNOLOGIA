@extends('layouts.app')

@section('content')
<section class="vh-100 auth-backgroud">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-2-strong" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <h3 class="mb-5">Entrar agora</h3>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div data-mdb-input-init class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg" required autofocus />
                            </div>
                            <div data-mdb-input-init class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="password">Senha</label>
                                <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                            </div>
                            <div class="form-check d-flex justify-content-start align-items-center mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" />
                                <label class="form-check-label ms-2" for="remember">Lembrar da senha</label>
                            </div>
                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg btn-block w-100" type="submit">
                                Entrar
                            </button>
                        </form>

                        <div class="d-flex align-items-center my-4">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted">ou</span>
                            <hr class="flex-grow-1">
                        </div>
                        <x-hyperlink-button href="{{ route('register') }}" class="btn-outline-primary btn-lg w-100">
                            Cadastrar
                        </x-hyperlink-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($errors->any())
    <div aria-live="polite" aria-atomic="true" class="position-relative" style="z-index: 9999">
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Erro</strong>
                    <small>Agora</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
                <div class="toast-body">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('extra-js')
<script>
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, { delay: 5000 }).show();
    });
</script>
@endsection
