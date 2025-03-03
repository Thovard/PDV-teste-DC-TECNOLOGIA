<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venda;
use App\Models\Parcela;
use Carbon\Carbon;

class ParcelaSeeder extends Seeder
{
    public function run()
    {
        $vendas = Venda::all();

        foreach ($vendas as $venda) {
            $valorParcela = $venda->total / $venda->quantidade_parcelas;
            $dataVencimento = Carbon::parse($venda->data_primeira_parcela);

            for ($i = 0; $i < $venda->quantidade_parcelas; $i++) {
                $status = 'pendente';
                if ($dataVencimento->isPast()) {
                    $status = rand(0, 1) ? 'atrasada' : 'paga';
                }
                Parcela::create([
                    'venda_id' => $venda->id,
                    'valor' => number_format($valorParcela, 2, '.', ''),
                    'data_vencimento' => $dataVencimento->toDateString(),
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $dataVencimento->addMonth();
            }
        }
    }
}
