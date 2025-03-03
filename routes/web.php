<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\PaymentConfigsController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\VendasController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', action: [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [DashboardController::class, 'update'])->name('perfil.update');

    Route::prefix('/clientes')->group(function () {
        Route::get('/', [ClientesController::class, 'index'])->name('clientes.index');
        Route::post('/store', [ClientesController::class, 'store'])->name('cliente.store');
        Route::get('/{id}/edit', [ClientesController::class, 'edit'])->name('cliente.edit');
        Route::put('/{id}', [ClientesController::class, 'update'])->name('cliente.update');
        Route::delete('/{id}', [ClientesController::class, 'destroy'])->name('cliente.destroy');
    });

    Route::prefix('produtos')->group(function () {
        Route::get('/', [ProdutosController::class, 'index'])->name('produtos.index');
        Route::get('/create', [ProdutosController::class, 'create'])->name('produtos.create');
        Route::post('/', [ProdutosController::class, 'store'])->name('produtos.store');
        Route::get('/{produto}/edit', [ProdutosController::class, 'edit'])->name('produtos.edit');
        Route::put('/{produto}', [ProdutosController::class, 'update'])->name('produtos.update');
        Route::delete('/{produto}', [ProdutosController::class, 'destroy'])->name('produtos.destroy');
    });

    Route::prefix('payment-configs')->group(function () {
        Route::get('/', [PaymentConfigsController::class, 'index'])->name('payment-configs.index');
        Route::put('/{paymentConfig}', [PaymentConfigsController::class, 'update'])->name('payment-configs.update');
    });

    Route::prefix('vendas')->group(function () {
        Route::get('/', [VendasController::class, 'index'])->name('vendas.index');
        Route::get('/get-default-data', [VendasController::class, 'getDefaultData'])->name('vendas.get-default-data');
        Route::get('/get-venda/{venda}', [VendasController::class, 'getVenda'])->name('vendas.get');
        Route::post('/store-venda', [VendasController::class, 'store'])->name('vendas.store');
        Route::get('/edit/{venda}', [VendasController::class, 'edit'])->name('vendas.edit');
        Route::put('/update/{venda}', [VendasController::class, 'update'])->name('vendas.update');
        Route::delete('/destroy/{venda}', [VendasController::class, 'destroy'])->name('vendas.destroy');
        Route::get('/parcelas/{venda}', [VendasController::class, 'parcelas'])->name('vendas.parcelas');
    });
});
