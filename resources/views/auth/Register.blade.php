@extends('layouts.app')

@section('content')
<section class="vh-100 auth-backgroud">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-2-strong" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">

                        <h3 class="mb-5">Criar conta</h3>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="name">Nome</label>
                                <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                            </div>

                            <div class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg" required />
                            </div>

                            <div class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="password">Senha</label>
                                <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                            </div>

                            <div class="form-outline mb-4 d-flex flex-column">
                                <label class="form-label text-start" for="password_confirmation">Confirmar Senha</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" required />
                            </div>

                            <button class="btn btn-primary btn-lg btn-block w-100" type="submit">Cadastrar</button>
                        </form>

                        <div class="d-flex align-items-center my-4">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted">ou</span>
                            <hr class="flex-grow-1">
                        </div>

                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg w-100">
                            Já tem uma conta? Faça login
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
