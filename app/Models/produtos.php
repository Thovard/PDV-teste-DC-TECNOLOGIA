<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produtos extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'preco', 'quantidade', 'imagem', 'user_id', 'tipo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
