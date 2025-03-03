import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function () {
    const ctxResumo = document.getElementById('chartResumo');
    if (ctxResumo) {
        new Chart(ctxResumo, {
            type: 'bar',
            data: {
                labels: ['Usuários', 'Produtos', 'Vendas'],
                datasets: [{
                    label: 'Quantidade',
                    data: [window.dadosDashboard.usuarios, window.dadosDashboard.produtos, window.dadosDashboard.vendas],
                    backgroundColor: ['#007bff', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const ctxClientes = document.getElementById('chartClientes');
    if (ctxClientes) {
        new Chart(ctxClientes, {
            type: 'pie',
            data: {
                labels: window.dadosDashboard.clientes.map(c => c.nome),
                datasets: [{
                    data: window.dadosDashboard.clientes.map(c => c.total_compras),
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const ctxProdutos = document.getElementById('chartProdutos');
    if (ctxProdutos) {
        new Chart(ctxProdutos, {
            type: 'doughnut',
            data: {
                labels: window.dadosDashboard.produtosVendidos.map(p => p.nome),
                datasets: [{
                    data: window.dadosDashboard.produtosVendidos.map(p => p.quantidade),
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const ctxStatusVendas = document.getElementById('chartStatusVendas');
    if (ctxStatusVendas) {
        new Chart(ctxStatusVendas, {
            type: 'bar', 
            data: {
                labels: ['Pagas', 'Pendentes'],
                datasets: [{
                    label: 'Status das Vendas',
                    data: [window.dadosDashboard.vendasPagas, window.dadosDashboard.vendasPendentes],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
