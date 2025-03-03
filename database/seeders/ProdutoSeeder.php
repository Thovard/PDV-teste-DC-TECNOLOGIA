<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produtos;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        Produtos::factory(30)->create();
    }
}
