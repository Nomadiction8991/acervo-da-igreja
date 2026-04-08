<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow">Inteligência</p>
            <h1 class="section-title mt-1">Relatórios e Análises</h1>
        </div>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2 mb-8">
        {{-- Igrejas por Cidade --}}
        <div class="surface panel-padding">
            <h2 class="text-lg font-semibold mb-4">Igrejas por Cidade</h2>
            <canvas id="chartCidades" height="300"></canvas>
        </div>

        {{-- Documentos por Tipo --}}
        <div class="surface panel-padding">
            <h2 class="text-lg font-semibold mb-4">Documentos por Tipo</h2>
            <canvas id="chartTipos" height="300"></canvas>
        </div>

        {{-- Tarefas por Status --}}
        <div class="surface panel-padding">
            <h2 class="text-lg font-semibold mb-4">Tarefas por Status</h2>
            <canvas id="chartStatus" height="300"></canvas>
        </div>

        {{-- Tarefas por Prioridade --}}
        <div class="surface panel-padding">
            <h2 class="text-lg font-semibold mb-4">Tarefas por Prioridade</h2>
            <canvas id="chartPrioridade" height="300"></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
        <script>
            const chartCidades = new Chart(document.getElementById('chartCidades'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($igrejasPorCidade->pluck('cidade')->all()) !!},
                    datasets: [{
                        label: 'Quantidade',
                        data: {!! json_encode($igrejasPorCidade->pluck('total')->all()) !!},
                        backgroundColor: '#3B82F6',
                        borderColor: '#1E40AF',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            const chartTipos = new Chart(document.getElementById('chartTipos'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($documentosPorTipo->pluck('tipo')->all()) !!},
                    datasets: [{
                        data: {!! json_encode($documentosPorTipo->pluck('total')->all()) !!},
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            const statusColors = {
                'concluida': '#10B981',
                'em_andamento': '#3B82F6',
                'pendente': '#FBBF24',
                'cancelada': '#EF4444'
            };

            const chartStatus = new Chart(document.getElementById('chartStatus'), {
                type: 'pie',
                data: {
                    labels: {!! json_encode($tarefasPorStatus->map(fn($t) => $t->status)->all()) !!},
                    datasets: [{
                        data: {!! json_encode($tarefasPorStatus->pluck('total')->all()) !!},
                        backgroundColor: {!! json_encode($tarefasPorStatus->map(fn($t) => $statusColors[$t->status] ?? '#6B7280')->all()) !!}
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            const prioridadeColors = {
                'baixa': '#10B981',
                'media': '#FBBF24',
                'alta': '#EF4444'
            };

            const chartPrioridade = new Chart(document.getElementById('chartPrioridade'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($tarefasPorPrioridade->map(fn($t) => $t->prioridade)->all()) !!},
                    datasets: [{
                        label: 'Quantidade',
                        data: {!! json_encode($tarefasPorPrioridade->pluck('total')->all()) !!},
                        backgroundColor: {!! json_encode($tarefasPorPrioridade->map(fn($t) => $prioridadeColors[$t->prioridade] ?? '#6B7280')->all()) !!}
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        </script>
    @endpush
</x-app-layout>
