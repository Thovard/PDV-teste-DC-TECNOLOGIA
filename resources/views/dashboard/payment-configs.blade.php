@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container-fluid">
        @foreach ($paymentConfigs as $config)
            <div class="row mb-4">
                <div class="col-12">
                    <x-card title="{{ $config->name }}" headerClass="bg-dark text-white" bodyClass="p-3">
                        <form method="POST" action="{{ route('payment-configs.update', $config) }}">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="approval_time_{{ $config->id }}" class="form-label">Tempo para Aprovação
                                        (dias)</label>
                                    <input type="number" id="approval_time_{{ $config->id }}" name="approval_time"
                                        class="form-control" value="{{ $config->approval_time }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="cash_rate_{{ $config->id }}" class="form-label">Taxa à Vista</label>
                                    <input type="text" id="cash_rate_{{ $config->id }}" name="cash_rate"
                                        class="form-control" value="{{ $config->cash_rate }}">
                                </div>
                                @if ($config->installment_rate != 0)
                                    <div class="col-md-6">
                                        <label for="installment_rate_{{ $config->id }}" class="form-label">Taxa de
                                            Parcelamento</label>
                                        <input type="text" id="installment_rate_{{ $config->id }}"
                                            name="installment_rate" class="form-control"
                                            value="{{ $config->installment_rate }}" >
                                    </div>
                                @endif
                                @if ($config->installment_limit != null)
                                    <div class="col-md-6">
                                        <label for="installment_limit_{{ $config->id }}" class="form-label">Limite de
                                            Parcelas</label>
                                        <input type="number" id="installment_limit_{{ $config->id }}"
                                            name="installment_limit" class="form-control"
                                            value="{{ $config->installment_limit }}" >
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-end">
                                <x-button type="submit" variant="primary" class="w-100" >
                                    Salvar
                                </x-button>
                            </div>
                        </form>
                    </x-card>
                </div>
            </div>
        @endforeach
    </div>
    @if (session('success'))
<x-toast message="{{ session('success') }}" type="success" title="Sucesso" />
@endif

@if (session('error'))
<x-toast message="{{ session('error') }}" type="error" title="Erro" />
@endif
@vite('resources/js/dashboard/payment-configs.js')
@endsection
