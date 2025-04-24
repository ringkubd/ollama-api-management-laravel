@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">API Request Metrics</h2>
                <div>
                    <form action="{{ route('admin.dashboard') }}" method="get">
                        <select name="time_range" onchange="this.form.submit()" class="border rounded p-2">
                            <option value="day" {{ $timeRange === 'day' ? 'selected' : '' }}>Last 24 Hours</option>
                            <option value="week" {{ $timeRange === 'week' ? 'selected' : '' }}>Last Week</option>
                            <option value="month" {{ $timeRange === 'month' ? 'selected' : '' }}>Last Month</option>
                            <option value="year" {{ $timeRange === 'year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Requests Card -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <h3 class="text-lg font-medium text-blue-800">Total Requests</h3>
                    <p class="text-3xl font-bold">{{ number_format($totalRequests) }}</p>
                </div>

                <!-- Requests in Period Card -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <h3 class="text-lg font-medium text-green-800">Requests (This Period)</h3>
                    <p class="text-3xl font-bold">{{ number_format($periodRequests) }}</p>
                </div>

                <!-- Error Rate Card -->
                <div class="bg-{{ $errorRate > 5 ? 'red' : 'yellow' }}-50 rounded-lg p-4 border border-{{ $errorRate > 5 ? 'red' : 'yellow' }}-200">
                    <h3 class="text-lg font-medium text-{{ $errorRate > 5 ? 'red' : 'yellow' }}-800">Error Rate</h3>
                    <p class="text-3xl font-bold">{{ number_format($errorRate, 2) }}%</p>
                </div>

                <!-- Average Response Time Card -->
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <h3 class="text-lg font-medium text-purple-800">Avg Response Time</h3>
                    <p class="text-3xl font-bold">{{ number_format($averageResponseTime * 1000, 2) }} ms</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Requests Over Time Chart -->
                <div class="bg-white rounded-lg p-4 border">
                    <h3 class="text-lg font-medium mb-2">Requests Over Time</h3>
                    <canvas id="requestsChart" height="200"></canvas>
                </div>

                <!-- Request Status Distribution Chart -->
                <div class="bg-white rounded-lg p-4 border">
                    <h3 class="text-lg font-medium mb-2">Request Status Distribution</h3>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Models -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-xl font-semibold mb-4">Top Models</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 text-left">Model</th>
                                <th class="py-2 px-4 text-left">Requests</th>
                                <th class="py-2 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topModels as $model)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $model->name }}</td>
                                    <td class="py-2 px-4">{{ number_format($model->request_count) }}</td>
                                    <td class="py-2 px-4">
                                        @if($model->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-gray-500">No models found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Errors -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-xl font-semibold mb-4">Recent Errors</h2>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="py-2 px-4 text-left">Time</th>
                                <th class="py-2 px-4 text-left">Model</th>
                                <th class="py-2 px-4 text-left">Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentErrors as $error)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $error->created_at->diffForHumans() }}</td>
                                    <td class="py-2 px-4">{{ $error->ollamaModel->name }}</td>
                                    <td class="py-2 px-4 truncate max-w-xs" title="{{ $error->error_message }}">
                                        {{ \Illuminate\Support\Str::limit($error->error_message, 50) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-gray-500">No errors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- API Key Management Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">API Key Management</h2>
                <a href="{{ route('admin.api-keys.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm">Create New API Key</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Total API Keys Card -->
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <h3 class="text-lg font-medium text-indigo-800">Total API Keys</h3>
                    <p class="text-3xl font-bold">{{ number_format($totalApiKeys ?? 0) }}</p>
                    <p class="text-sm text-indigo-600 mt-1">{{ number_format($activeApiKeys ?? 0) }} active keys</p>
                </div>

                <!-- API Key Usage Card -->
                <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                    <h3 class="text-lg font-medium text-teal-800">Top API Key Usage</h3>
                    @if(isset($topApiKeys) && $topApiKeys->isNotEmpty())
                        <p class="text-3xl font-bold">{{ number_format($topApiKeys->first()->request_count) }}</p>
                        <p class="text-sm text-teal-600 mt-1">{{ $topApiKeys->first()->name }}</p>
                    @else
                        <p class="text-3xl font-bold">0</p>
                        <p class="text-sm text-teal-600 mt-1">No API keys used yet</p>
                    @endif
                </div>
            </div>

            <!-- Top API Keys Table -->
            <h3 class="font-medium mb-3">Top API Keys</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left">Application</th>
                            <th class="py-2 px-4 text-left">Requests</th>
                            <th class="py-2 px-4 text-left">Last Used</th>
                            <th class="py-2 px-4 text-left">Status</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topApiKeys ?? [] as $apiKey)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4">{{ $apiKey->name }}</td>
                                <td class="py-2 px-4">{{ number_format($apiKey->request_count) }}</td>
                                <td class="py-2 px-4">
                                    @if($apiKey->last_used_at)
                                        {{ $apiKey->last_used_at->diffForHumans() }}
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    @if($apiKey->is_active)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.api-keys.show', $apiKey->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">No API keys found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-4">
                <a href="{{ route('admin.api-keys.index') }}" class="text-blue-600 hover:text-blue-800">View all API keys →</a>
            </div>
        </div>

        <!-- Endpoint Distribution Chart -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-xl font-semibold mb-4">Endpoint Distribution</h2>
            <canvas id="endpointChart" height="100"></canvas>
        </div>

        <!-- API Documentation Section -->
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">API Documentation</h2>
                <div class="space-x-2">
                    <a href="{{ route('admin.documentation') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">View Full Documentation</a>
                    <a href="{{ route('admin.documentation.postman') }}" class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded text-sm">Download Postman Collection</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2 text-blue-700">List Models</h3>
                    <p class="text-sm text-gray-600 mb-2">Get available models for generation</p>
                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">GET /api/v1/models</pre>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2 text-blue-700">Text Generation</h3>
                    <p class="text-sm text-gray-600 mb-2">Generate text completions</p>
                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">POST /api/v1/generate/{modelId}</pre>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2 text-blue-700">Chat Completion</h3>
                    <p class="text-sm text-gray-600 mb-2">Generate conversational responses</p>
                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">POST /api/v1/chat/{modelId}</pre>
                </div>
            </div>

            <div class="mt-4 text-sm text-gray-600">
                <p>📘 Complete documentation with examples and response details available in the full documentation.</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Requests Over Time Chart
    const requestsCtx = document.getElementById('requestsChart').getContext('2d');
    new Chart(requestsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($requestsOverTime)) !!},
            datasets: [{
                label: 'Requests',
                data: {!! json_encode(array_values($requestsOverTime)) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                tension: 0.1,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Completed', 'Failed'],
            datasets: [{
                data: [
                    {{ $requestsByStatus['pending'] ?? 0 }},
                    {{ $requestsByStatus['processing'] ?? 0 }},
                    {{ $requestsByStatus['completed'] ?? 0 }},
                    {{ $requestsByStatus['failed'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(239, 68, 68, 0.7)'
                ],
                borderWidth: 1
            }]
        }
    });

    // Endpoint Distribution Chart
    const endpointCtx = document.getElementById('endpointChart').getContext('2d');
    new Chart(endpointCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($requestsByEndpoint)) !!},
            datasets: [{
                label: 'Requests by Endpoint',
                data: {!! json_encode(array_values($requestsByEndpoint)) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
