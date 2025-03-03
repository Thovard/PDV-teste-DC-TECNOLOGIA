<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    use HasFactory;

    protected $table = 'payment_configs';

    protected $fillable = [
        'slug',
        'name',
        'cash_rate',
        'installment_rate',
        'approval_time',
        'installment_limit',
    ];

    protected $casts = [
        'cash_rate' => 'float',
        'installment_rate' => 'float',
        'approval_time' => 'integer',
        'installment_limit' => 'integer',
    ];
}
