<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama API Management - @yield('title', 'Dashboard')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-slate-800 text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Ollama API Management</a>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-300">Dashboard</a>
                        <a href="{{ route('admin.models.index') }}" class="hover:text-blue-300">Models</a>
                        <a href="{{ route('admin.api-keys.index') }}" class="hover:text-blue-300">API Keys</a>
                        <a href="{{ route('admin.playground') }}" class="hover:text-blue-300">Playground</a>
                        <a href="{{ route('admin.documentation') }}" class="hover:text-blue-300">API Docs</a>

                        @if(auth()->user() && auth()->user()->hasRole('admin'))
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" class="hover:text-blue-300 focus:outline-none">
                                Admin ▾
                            </button>
                            <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95">
                                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Users</a>
                                <a href="{{ route('admin.roles.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage Roles</a>
                            </div>
                        </div>
                        @endif

                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" class="hover:text-blue-300 focus:outline-none">
                                    {{ auth()->user()->name }} ▾
                                </button>
                                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow container mx-auto px-4 py-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-slate-800 text-white py-4">
            <div class="container mx-auto px-4 text-center">
                &copy; {{ date('Y') }} Ollama API Management System
            </div>
        </footer>
    </div>

    <!-- Additional Scripts -->
    @yield('scripts')
</body>
</html>
