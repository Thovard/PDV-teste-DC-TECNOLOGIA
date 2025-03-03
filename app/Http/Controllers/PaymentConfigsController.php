<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentConfig;

class PaymentConfigsController extends Controller
{
    public function index()
    {
        $paymentConfigs = PaymentConfig::all();
        foreach ($paymentConfigs as $config) {
            $config->cash_rate = number_format($config->cash_rate, 2, '.', ',');
            $config->installment_rate = number_format($config->installment_rate, 2, '.', ',');
        }
        return view('dashboard.payment-configs', compact('paymentConfigs'));
    }

    public function update(Request $request, PaymentConfig $paymentConfig)
    {

        $validated = $request->validate([
            'cash_rate'         => 'required',
            'approval_time'     => 'required',
            'installment_rate'  => 'nullable',
            'installment_limit' => 'nullable',
        ]);
        $cash_rate = preg_replace('/[^0-9,]/', '', $validated['cash_rate']);
        $cash_rate = str_replace(',', '.', $cash_rate);
        $validated['cash_rate'] = floatval($cash_rate);

        if (array_key_exists('installment_rate', $validated)) {
            $installment_rate = preg_replace('/[^0-9,]/', '', $validated['installment_rate']);
            $installment_rate = str_replace(',', '.', $installment_rate);
            $validated['installment_rate'] = floatval($installment_rate);

            if ($validated['installment_rate'] > 100) {
                return redirect()->back()
                    ->withErrors(['installment_rate' => 'O campo Taxa de Parcelamento não pode ser maior que 100.'])
                    ->withInput();
            }
        }
        // Verifica se os valores são maiores que 100
        if ($validated['cash_rate'] > 100) {
            return redirect()->back()
                ->withErrors(['cash_rate' => 'O campo Taxa à Vista não pode ser maior que 100.'])
                ->withInput();
        }

        if (isset($validated['installment_limit']) && $validated['installment_limit'] > 100) {
            return redirect()->back()
                ->withErrors(['installment_limit' => 'O campo Limite de Parcelas não pode ser maior que 100.'])
                ->withInput();
        }

        $paymentConfig->update($validated);

        return redirect()->route('payment-configs.index')->with('success', 'Configuração atualizada com sucesso!');
    }
}
