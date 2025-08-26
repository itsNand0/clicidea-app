<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Estadísticas - ClicIdea</title>
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <x-Barmenu />
    
    <main class="max-w-7xl mx-auto mt-6 p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2 ml-6">
                <i class="fa-solid fa-chart-pie text-blue-600"></i> Estadísticas de Incidencias
            </h1>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clipboard-list text-3xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Incidencias</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalIncidencias }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Incidencias Abiertas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $incidenciasAbiertas }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-month text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Este Mes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $incidenciasMesActual }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Gráfico de torta - Estados -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-600"></i>
                    Incidencias por Estado
                </h2>
                <div class="relative h-80">
                    <canvas id="estadosChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de barras - Últimos 6 meses -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-green-600"></i>
                    Incidencias por Mes
                </h2>
                <div class="relative h-80">
                    <canvas id="mesesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de usuarios más activos -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-purple-600"></i>
                Top 5 Usuarios con más Incidencias
            </h2>
            <div class="relative h-80">
                <canvas id="usuariosChart"></canvas>
            </div>
        </div>
    </main>

    @livewireScripts

    <script>
        // Colores para los gráficos
        const colors = [
            '#EF4444', // Rojo - Abierta
            '#F59E0B', // Amarillo - En Proceso  
            '#10B981', // Verde - Cerrada
            '#8B5CF6', // Púrpura - Pendiente
            '#06B6D4', // Cyan - Otros
            '#F97316', // Naranja
            '#EC4899'  // Rosa
        ];

        // Gráfico de Estados (Torta)
        const estadosCtx = document.getElementById('estadosChart').getContext('2d');
        const estadosData = @json($estadisticasPorEstado);
        
        new Chart(estadosCtx, {
            type: 'doughnut',
            data: {
                labels: estadosData.map(item => item.estado),
                datasets: [{
                    data: estadosData.map(item => item.total),
                    backgroundColor: colors.slice(0, estadosData.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Meses (Barras)
        const mesesCtx = document.getElementById('mesesChart').getContext('2d');
        const mesesData = @json($estadisticasPorMes);
        
        new Chart(mesesCtx, {
            type: 'bar',
            data: {
                labels: mesesData.map(item => {
                    const fecha = new Date(item.mes);
                    return fecha.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Incidencias',
                    data: mesesData.map(item => item.total),
                    backgroundColor: '#3B82F6',
                    borderColor: '#1D4ED8',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Gráfico de Usuarios (Barras horizontales)
        const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
        const usuariosData = @json($estadisticasPorUsuario);
        
        new Chart(usuariosCtx, {
            type: 'bar',
            data: {
                labels: usuariosData.map(item => item.usuario),
                datasets: [{
                    label: 'Incidencias',
                    data: usuariosData.map(item => item.total),
                    backgroundColor: '#8B5CF6',
                    borderColor: '#7C3AED',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
