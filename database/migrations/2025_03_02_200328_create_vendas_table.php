<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasTable extends Migration
{
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->json('produtos_ids')->nullable();
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->integer('quantidade_parcelas')->default(1);
            $table->decimal('valor_produto', 10, 2);
            $table->decimal('valor_taxa', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', [
                'pendente',
                'aprovado',
                'em_dia',
                'recusado',
                'cancelada',
                'atrasado'
            ])->default('pendente');
            $table->date('data_primeira_parcela')->nullable();
            $table->date('data_demais_parcelas')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
