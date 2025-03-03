<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelasTable extends Migration
{
    public function up()
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');   
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->enum('status', [
                'pendente', 
                'paga',     
                'atrasada', 
                'cancelada' 
            ])->default('pendente');
            $table->timestamps();

            $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcelas');
    }
}
