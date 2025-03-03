<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'produtos_ids',
        'forma_pagamento_id',
        'quantidade_parcelas',
        'valor_produto',
        'valor_taxa',
        'total',
        'status',
        'data_primeira_parcela',
        'data_demais_parcelas',
    ];

    protected $casts = [
        'produtos_ids' => 'array',
        'data_primeira_parcela' => 'date',
        'data_demais_parcelas' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'venda_id');
    }


    public function formaPagamento()
    {
        return $this->belongsTo(PaymentConfig::class, 'forma_pagamento_id');
    }
}
