@extends('layouts.app')

@section('content')
<div class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="position: fixed; width: 300px; height: 100vh;">
        <div class="text-center mb-5">
            <img src="{{ asset('images/user.png') }}" alt="User Image" class="rounded-circle" width="80">
            <h5 class="mt-2">{{ Auth::user()->name }}</h5>
            <a href="{{ route('perfil') }}" class="text-white text-decoration-none">Perfil</a>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('vendas.index') }}" class="nav-link text-white {{ request()->routeIs('vendas.index') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-cart me-2"></i> Vendas
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('produtos.index') }}" class="nav-link text-white {{ request()->routeIs('produtos.index') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Produtos
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientes.index') }}" class="nav-link text-white {{ request()->routeIs('clientes.index') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-people me-2"></i> Clientes
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('payment-configs.index') }}" class="nav-link text-white {{ request()->routeIs('payment-configs.index') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-credit-card me-2"></i> Configuração de Pagamento
                </a>
            </li>
        </ul>
        <div class="mt-auto pt-3">
            <button type="button" class="btn btn-danger w-100"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Sair
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
    <div class="p-4 dash-backgroud" style="margin-left: 300px; width: calc(100% - 300px);">
        @yield('dashboard-content')
    </div>
</div>
@endsection
