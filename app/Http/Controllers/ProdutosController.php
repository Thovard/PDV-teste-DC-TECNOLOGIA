<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produtos;

class ProdutosController extends Controller
{
    public function index()
    {
        $produtos = Produtos::where('user_id', auth()->id())->get();
        return view('dashboard.produtos', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'       => 'required|string|max:255',
            'foto'       => 'nullable|string',
            'quantidade' => 'required|integer|min:0',
            'tipo'       => 'required|string|max:255',
            'preco'      => 'required|string',
        ], [
            'nome.required'       => 'O campo nome é obrigatório.',
            'quantidade.required' => 'O campo quantidade é obrigatório.',
            'tipo.required'       => 'O campo tipo é obrigatório.',
            'preco.required'      => 'O campo preço é obrigatório.',
        ]);

        // Converte o valor do preço
        $price = $validated['preco'];
        $price = preg_replace('/[^0-9,]/', '', $price);
        $price = str_replace(',', '.', $price);
        $validated['preco'] = floatval($price);

        $validated['user_id'] = auth()->id();
        Produtos::create($validated);

        return redirect()->back()->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Produtos $produto)
    {
        if ($produto->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para editar esse produto.');
        }
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, $id)
    {
        $produto = Produtos::findOrFail($id);
        if ($produto->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para atualizar esse produto.');
        }

        $validated = $request->validate([
            'nome'       => 'required|string|max:255',
            'foto'       => 'nullable|string',
            'quantidade' => 'required|integer|min:0',
            'tipo'       => 'required|string|max:255',
            'preco'      => 'required|string',
        ], [
            'nome.required'       => 'O campo nome é obrigatório.',
            'quantidade.required' => 'O campo quantidade é obrigatório.',
            'tipo.required'       => 'O campo tipo é obrigatório.',
            'preco.required'      => 'O campo preço é obrigatório.',
        ]);

        $price = $validated['preco'];
        $price = preg_replace('/[^0-9,]/', '', $price);
        $price = str_replace(',', '.', $price);
        $validated['preco'] = floatval($price);

        $produto->update($validated);

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produtos $produto)
    {
        if ($produto->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para excluir esse produto.');
        }
        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso!');
    }
}
