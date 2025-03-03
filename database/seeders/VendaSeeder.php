<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venda;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Produtos;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendaSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $clientes = Cliente::all();
        $produtos = Produtos::all();

        foreach ($clientes as $cliente) {
            $user = $users->random();
            $formaPagamentoId = rand(1, 3); 
            $quantidadeParcelas = $formaPagamentoId == 2 ? rand(1, 6) : 1; 
            $statusOptions = ['pendente', 'aprovado', 'em_dia', 'recusado', 'cancelada', 'atrasado'];
            $status = $statusOptions[array_rand($statusOptions)];

            
            $produtosVenda = [];
            $totalVenda = 0;
            $valorProduto = 0;
            $valorTaxa = 0;

            for ($i = 0; $i < rand(1, 3); $i++) {
                $produto = $produtos->random();
                $quantidade = rand(1, 2);
                $subtotal = $produto->preco * $quantidade;

                $produtosVenda[] = [
                    'produto' => $produto->id,
                    'quantidade' => $quantidade
                ];

                $totalVenda += $subtotal;
                $valorProduto = $subtotal;
                $valorTaxa = $subtotal * 0.05; 
            }

            Venda::create([
                'user_id' => $user->id,
                'cliente_id' => $cliente->id,
                'produtos' => json_encode($produtosVenda),
                'forma_pagamento_id' => $formaPagamentoId,
                'quantidade_parcelas' => $quantidadeParcelas,
                'valor_produto' => $valorProduto,
                'valor_taxa' => $valorTaxa,
                'total' => $totalVenda + $valorTaxa,
                'status' => $status,
                'data_primeira_parcela' => Carbon::now()->addDays(rand(1, 30)),
                'data_demais_parcelas' => $formaPagamentoId == 2 ? Carbon::now()->addDays(rand(30, 180)) : null, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
