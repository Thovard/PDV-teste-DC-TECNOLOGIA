<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venda;
use App\Models\Parcela;
use App\Models\PaymentConfig;
use App\Models\produtos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VendasController extends Controller
{
    public function index()
    {
        $vendas = Venda::where('user_id', auth()->id())
            ->with(['cliente', 'formaPagamento', 'parcelas'])
            ->orderByDesc('created_at')
            ->get();
        $produtos = produtos::where('user_id', auth()->id())->get();
        $vendas->transform(function ($venda) {
            $venda->valor_produto_formatado = 'R$ ' . number_format($venda->valor_produto, 2, ',', '.');
            $venda->valor_taxa_formatado    = 'R$ ' . number_format($venda->valor_taxa, 2, ',', '.');
            $venda->total_formatado         = 'R$ ' . number_format($venda->total, 2, ',', '.');
            $venda->forma_pagamento_name    = $venda->formaPagamento ? $venda->formaPagamento->name : '';
            $venda->produtos_ids = json_decode($venda->produtos_ids, true);
            return $venda;
        });

        return view('dashboard.vendas', compact('vendas', 'produtos'));
    }


    public function getDefaultData()
    {
        return response()->json([
            'produtos' => produtos::where('user_id', auth()->id())
                ->select(['id', 'nome', 'quantidade', 'preco'])->get(),
            'clientes' => Cliente::where('user_id', auth()->id())
                ->select(['id', 'nome'])->get(),
            'paymentConfigs' => PaymentConfig::all()->keyBy('slug')
        ]);
    }

    public function store(Request $request)
    {
        $mensagensPersonalizadas = [
            'required' => 'O campo :attribute é obrigatório.',
            'exists' => 'O :attribute selecionado é inválido.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'min' => 'O campo :attribute deve ser pelo menos :min.',
            'in' => 'A forma de pagamento selecionada é inválida.',
            'required_if' => 'O campo :attribute é obrigatório para pagamento com cartão de crédito.',
            'numeric' => 'O campo :attribute deve ser um valor numérico válido.',
            'produtos.*.produto.exists' => 'Produto selecionado não existe',
            'produtos.*.quantidade.min' => 'A quantidade mínima deve ser 1 unidade'
        ];

        $regrasValidacao = [
            'cliente' => 'nullable|exists:clientes,id',
            'produtos' => 'required|array|min:1',
            'produtos.*.produto' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'pagamento' => 'required|in:pix,credit-card,debit-card,cash',
            'parcelas' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->pagamento === 'credit-card';
                }),
                'integer',
                'min:1',
            ],
            'valorProduto' => 'required|numeric|min:0.01',
            'taxa' => 'required|numeric|min:0',
            'valorTotal' => 'required|numeric|min:0.01'
        ];

        $validated = $request->validate($regrasValidacao, [
            'cliente.exists' => 'Cliente selecionado não existe',
            'produtos.required' => 'É necessário selecionar ao menos um produto',
            'produtos.*.produto.exists' => 'Produto selecionado não existe',
            'produtos.*.quantidade.min' => 'A quantidade mínima deve ser 1 unidade',
            'pagamento.required' => 'Forma de pagamento é obrigatória',
            'parcelas.required_if' => 'Número de parcelas é obrigatório para cartão de crédito',
            'parcelas.min' => 'O número mínimo de parcelas é 1',
            'parcelas.max' => 'O número máximo de parcelas é 12',
            'valorProduto.min' => 'O valor do produto deve ser maior que zero',
            'taxa.min' => 'O valor da taxa não pode ser negativo',
            'valorTotal.min' => 'O valor total deve ser maior que zero'
        ]);

        try {
            DB::beginTransaction();

            if ($validated['pagamento'] !== 'credit-card') {
                $validated['parcelas'] = null;
            }

            $paymentConfig = PaymentConfig::where('slug', $validated['pagamento'])->firstOrFail();

            foreach ($validated['produtos'] as $item) {
                $produto = produtos::find($item['produto']);
                if ($produto->quantidade < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto: " . $produto->nome);
                }
                $produto->quantidade -= $item['quantidade'];
                $produto->save();
            }

            // Converter os valores recebidos em centavos para decimal, considerando as 2 últimas casas
            $valorProdutoDecimal = number_format(floatval($validated['valorProduto']) / 100, 2, '.', '');
            $taxaDecimal = number_format(floatval($validated['taxa']) / 100, 2, '.', '');
            $valorTotalDecimal = number_format(floatval($validated['valorTotal']) / 100, 2, '.', '');

            $dadosVenda = [
                'user_id' => Auth::id(),
                'cliente_id' => $validated['cliente'],
                'produtos_ids' => json_encode($validated['produtos']),
                'forma_pagamento_id' => $paymentConfig->id,
                'quantidade_parcelas' => $validated['parcelas'] ?? 1,
                'valor_produto' => $valorProdutoDecimal,
                'valor_taxa' => $taxaDecimal,
                'total' => $valorTotalDecimal,
                'status' => 'pendente',
                'data_primeira_parcela' => now()->addDays(30),
                'data_demais_parcelas' => now()->addDays(60)
            ];

            $venda = Venda::create($dadosVenda);

            if ($validated['pagamento'] === 'credit-card' && $validated['parcelas'] > 1) {
                $this->criarParcelas($venda, $validated, $valorTotalDecimal);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'venda_id' => $venda->id,
                'parcelas' => $venda->parcelas->count()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Configuração de pagamento não encontrada'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda: ' . $this->traduzirErro($e->getMessage()),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function edit(Venda $venda)
    {
        // Função para exibir o formulário de edição da venda
    }

    public function update(Request $request, Venda $venda)
    {
        $regrasValidacao = [
            'cliente' => 'nullable|exists:clientes,id',
            'produtos' => 'required|array|min:1',
            'produtos.*.produto' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'pagamento' => 'required|in:pix,credit-card,debit-card,cash',
            'parcelas' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return $request->pagamento === 'credit-card';
                }),
                'integer',
                'min:1',
            ],
            'valorProduto' => 'required|numeric|min:0.01',
            'taxa' => 'required|numeric|min:0',
            'valorTotal' => 'required|numeric|min:0.01'
        ];

        $validated = $request->validate($regrasValidacao);

        try {
            DB::beginTransaction();

            // Restaurar os produtos ao estoque antes de atualizar a venda
            $produtosAntigos = json_decode($venda->produtos_ids, true);
            if ($produtosAntigos) {
                foreach ($produtosAntigos as $item) {
                    $produto = produtos::find($item['produto']);
                    if ($produto) {
                        $produto->quantidade += $item['quantidade'];
                        $produto->save();
                    }
                }
            }

            // Excluir parcelas antigas
            $venda->parcelas()->delete();

            // Atualizar estoque com os novos produtos
            foreach ($validated['produtos'] as $item) {
                $produto = produtos::find($item['produto']);
                if ($produto->quantidade < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto: " . $produto->nome);
                }
                $produto->quantidade -= $item['quantidade'];
                $produto->save();
            }

            // Converter valores para decimal
            $valorProdutoDecimal = number_format(floatval($validated['valorProduto']) / 100, 2, '.', '');
            $taxaDecimal = number_format(floatval($validated['taxa']) / 100, 2, '.', '');
            $valorTotalDecimal = number_format(floatval($validated['valorTotal']) / 100, 2, '.', '');

            // Atualizar os dados da venda
            $venda->update([
                'cliente_id' => $validated['cliente'],
                'produtos_ids' => json_encode($validated['produtos']),
                'forma_pagamento_id' => PaymentConfig::where('slug', $validated['pagamento'])->firstOrFail()->id,
                'quantidade_parcelas' => $validated['parcelas'] ?? 1,
                'valor_produto' => $valorProdutoDecimal,
                'valor_taxa' => $taxaDecimal,
                'total' => $valorTotalDecimal,
                'status' => 'pendente',
            ]);

            // Criar novas parcelas se for cartão de crédito parcelado
            if ($validated['pagamento'] === 'credit-card' && $validated['parcelas'] > 1) {
                $this->criarParcelas($venda, $validated, $valorTotalDecimal);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda atualizada com sucesso!',
                'venda_id' => $venda->id,
                'parcelas' => $venda->parcelas->count()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar a venda: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Venda $venda)
    {
        try {
            DB::beginTransaction();

            $produtos = json_decode($venda->produtos_ids, true);
            if ($produtos) {
                foreach ($produtos as $item) {
                    $produto = produtos::find($item['produto']);
                    if ($produto) {
                        $produto->quantidade += $item['quantidade'];
                        $produto->save();
                    }
                }
            }

            $venda->parcelas()->delete();
            $venda->delete();

            DB::commit();

            return redirect()->route('vendas.index')->with('success', 'Venda excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao excluir a venda: ' . $this->traduzirErro($e->getMessage()));
        }
    }
    public function getVenda(Venda $venda)
    {
        $produtosVinculados = json_decode($venda->produtos_ids, true);
        $produtoIds = array_column($produtosVinculados, 'produto');
        $produtos = Produtos::where('user_id', $venda->user_id)
            ->whereIn('id', $produtoIds)
            ->get()
            ->keyBy('id');
        $produtosDaVenda = [];
        foreach ($produtosVinculados as $item) {
            $produtoId = $item['produto'];
            if (isset($produtos[$produtoId])) {
                $produtosDaVenda[] = [
                    'produto' => $produtos[$produtoId],
                    'quantidade' => $item['quantidade']
                ];
            }
        }
        $venda = Venda::where('user_id', $venda->user_id)->where('id', $venda->id)->with('cliente', 'user', 'formaPagamento')->first();
        return [
            'venda' => $venda,
            'produtos' => $produtosDaVenda
        ];
    }
    private function criarParcelas(Venda $venda, array $dados, $valorTotalDecimal)
    {
        $valorParcela = $valorTotalDecimal / $dados['parcelas'];
        $dataVencimento = now()->addDays(30);

        for ($i = 1; $i <= $dados['parcelas']; $i++) {
            Parcela::create([
                'venda_id' => $venda->id,
                'valor' => round($valorParcela, 2),
                'data_vencimento' => $dataVencimento,
                'status' => 'pendente'
            ]);

            $dataVencimento = $dataVencimento->copy()->addDays(30);
        }
    }

    private function traduzirErro(string $mensagem): string
    {
        $traducoes = [
            "attempt to read property" => "Erro de referência inválida",
            "SQLSTATE" => "Erro no banco de dados",
            "Connection refused" => "Falha na conexão com o servidor"
        ];

        foreach ($traducoes as $chave => $traducao) {
            if (str_contains($mensagem, $chave)) {
                return $traducao;
            }
        }

        return $mensagem;
    }
}
