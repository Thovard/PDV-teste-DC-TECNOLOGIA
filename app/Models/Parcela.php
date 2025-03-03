<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $fillable = [
        'venda_id',
        'valor',
        'data_vencimento',
        'status',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }
}
