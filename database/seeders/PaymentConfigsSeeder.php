<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentConfigsSeeder extends Seeder
{
    public function run()
    {
        DB::table('payment_configs')->insert([
            [
                'slug' => 'pix',
                'name' => 'PIX',
                'cash_rate' => 0.00,
                'installment_rate' => 0.00,
                'approval_time' => 1,
                'installment_limit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'credit-card',
                'name' => 'Cartão de Crédito',
                'cash_rate' => 3.50,
                'installment_rate' => 3.50,
                'approval_time' => 1,
                'installment_limit' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'debit-card',
                'name' => 'Cartão de Débito',
                'cash_rate' => 2.50,
                'installment_rate' => 0.00,
                'approval_time' => 1,
                'installment_limit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'cash',
                'name' => 'Pagamento em Espécie',
                'cash_rate' => 0.00,
                'installment_rate' => 0.00,
                'approval_time' => 1,
                'installment_limit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
