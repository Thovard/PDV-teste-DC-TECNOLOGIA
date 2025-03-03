<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index(Cliente $cliente)
    {
        $clientes = Cliente::where('user_id', auth()->id())->paginate(10);
        return view('dashboard.cliente_create', compact('clientes'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:clientes',
            'cpf'      => 'required|string|max:14|unique:clientes',
            'telefone' => 'required|string|max:20',
        ], [
            'nome.required'     => 'O campo nome é obrigatório.',
            'email.required'    => 'O campo email é obrigatório.',
            'email.email'       => 'O campo email deve ser um endereço de email válido.',
            'email.unique'      => 'Este email já está em uso.',
            'cpf.required'      => 'O campo CPF é obrigatório.',
            'cpf.unique'        => 'Este CPF já está em uso.',
            'telefone.required' => 'O campo telefone é obrigatório.',
        ]);

        $validated = array_map('trim', $validated);

        $validated['cpf'] = preg_replace('/\D/', '', $validated['cpf']);
        $validated['telefone'] = preg_replace('/\D/', '', $validated['telefone']);

        $validated['user_id'] = auth()->id();

        $cliente = Cliente::create($validated);

        return redirect()->back()->with('success', 'Cliente criado com sucesso!');

    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.index', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if ($cliente->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para atualizar esse cliente.');
        }

        $validated = $request->validate([
            'nome'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:clientes,email,' . $cliente->id,
            'cpf'      => 'required|string|max:14|unique:clientes,cpf,' . $cliente->id,
            'telefone' => 'required|string|max:20',
        ], [
            'nome.required'     => 'O campo nome é obrigatório.',
            'email.required'    => 'O campo email é obrigatório.',
            'email.email'       => 'O campo email deve ser um endereço de email válido.',
            'email.unique'      => 'Este email já está em uso.',
            'cpf.required'      => 'O campo CPF é obrigatório.',
            'cpf.unique'        => 'Este CPF já está em uso.',
            'telefone.required' => 'O campo telefone é obrigatório.',
        ]);

        $validated = array_map('trim', $validated);
        $validated['cpf'] = preg_replace('/\D/', '', $validated['cpf']);
        $validated['telefone'] = preg_replace('/\D/', '', $validated['telefone']);

        $cliente->update($validated);

        return redirect()->route('clientes.index', $cliente)->with('success', 'Cliente atualizado com sucesso!');
    }
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para deletar esse cliente.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente deletado com sucesso!');
    }
}
