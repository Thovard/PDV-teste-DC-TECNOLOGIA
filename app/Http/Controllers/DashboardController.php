<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produtos;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();


        $clientesCount = Cliente::where('user_id', $userId)->count();
        $produtosCount = Produtos::where('user_id', $userId)->count();
        $vendasCount = Venda::where('user_id', $userId)->count();


        $statusVendas = Venda::where('user_id', $userId)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();


        $statusVendas = [
            'pago' => $statusVendas['pago'] ?? 0,
            'pendente' => $statusVendas['pendente'] ?? 0,
        ];


        $clientesMaisCompraram = Venda::where('user_id', $userId)
            ->select('cliente_id', DB::raw('COUNT(*) as total_compras'))
            ->groupBy('cliente_id')
            ->orderByDesc('total_compras')
            ->with('cliente:id,nome')
            ->get()
            ->map(fn($cliente) => (object) [
                'nome' => $cliente->cliente->nome ?? 'Desconhecido',
                'total_compras' => $cliente->total_compras,
            ]);


        $produtosMaisVendidos = [];
        $vendas = Venda::where('user_id', $userId)->get();

        foreach ($vendas as $venda) {
            $produtos = json_decode($venda->produtos, true);

            if (is_array($produtos)) {
                foreach ($produtos as $produto) {
                    $produtoModel = Produtos::find($produto['produto']);

                    if ($produtoModel) {
                        $nomeProduto = $produtoModel->nome;
                        $produtosMaisVendidos[$nomeProduto] = ($produtosMaisVendidos[$nomeProduto] ?? 0) + $produto['quantidade'];
                    }
                }
            }
        }


        $produtosMaisVendidos = collect($produtosMaisVendidos)->map(fn($quantidade, $nome) => (object) [
            'nome' => $nome,
            'quantidade' => $quantidade,
        ])->values();

        return view('dashboard.index', compact(
            'clientesCount',
            'produtosCount',
            'vendasCount',
            'clientesMaisCompraram',
            'produtosMaisVendidos',
            'statusVendas'
        ));
    }

    public function perfil()
    {
        $user = Auth::user();
        return view('dashboard.perfil', compact('user'));
    }
    public function update(Request $request){
        $user = Auth::user();

        // Validação dos campos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Atualizar dados do usuário
        $user->name = $request->name;
        $user->email = $request->email;

        // Atualizar a senha apenas se for preenchida
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('perfil')->with('success', 'Perfil atualizado com sucesso!');
    }
}
