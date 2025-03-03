<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('cash_rate', 8, 2);
            $table->decimal('installment_rate', 8, 2)->default(0);
            $table->integer('approval_time')->default(1);
            $table->integer('installment_limit')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_configs');
    }
}
