<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. SUMMARY CARDS WITH NEW SUCCESS RATE --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="text-sm font-bold text-gray-500 uppercase">Total Found Items</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalFound }}</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500 hover:shadow-md transition">
                    <div class="text-sm font-bold text-gray-500 uppercase">Total Lost Reports</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalLost }}</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="text-sm font-bold text-gray-500 uppercase">Registered Staff</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalStaff }}</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="text-sm font-bold text-gray-500 uppercase">Recovery Success Rate</div>
                    <div class="text-3xl font-bold text-green-600">{{ $successRate }}%</div>
                </div>
            </div>

            {{-- 2. CHARTS SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                {{-- Items by Category --}}
                <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-700 mb-2">📦 Items by Category</h3>
                    <p class="text-xs text-gray-500 mb-6">Distribution of recovered items.</p>
                    <div style="height: 300px; display: flex; justify-content: center;">
                        <canvas id="itemsPieChart"></canvas>
                    </div>
                </div>

                {{-- Hotspots --}}
                <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-700 mb-2">📍 Top 5 Lost & Found Hotspots</h3>
                    <p class="text-xs text-gray-500 mb-6">Locations where items are most frequently found.</p>
                    <div style="height: 300px;">
                        <canvas id="hotspotsBarChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3. EXPORT SECTION --}}
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-700">Need to generate a report?</h3>
                    <p class="text-sm text-gray-500">Download the full Found Items database as a CSV file for Excel.</p>
                </div>
                <a href="{{ route('admin.export.found_items') }}" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 transition font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export to Excel
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // --- 1. PIE CHART (Categories) ---
            const pieCtx = document.getElementById('itemsPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryData) !!},
                        backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });

            // --- 2. HORIZONTAL BAR CHART (Hotspots) ---
            const barCtx = document.getElementById('hotspotsBarChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($hotspotLabels) !!},
                    datasets: [{
                        label: 'Total Items Found',
                        data: {!! json_encode($hotspotData) !!},
                        backgroundColor: '#3B82F6',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

        });
    </script>
</x-app-layout>