<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama API Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-slate-800 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">Ollama API Management</a>
                <div class="space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-300">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">Ollama API Management</h1>
            <p class="text-xl lg:text-2xl mb-8">A robust API management system for Ollama language models</p>
            <a href="{{ route('admin.dashboard') }}" class="bg-white text-blue-600 hover:bg-blue-50 font-bold py-3 px-6 rounded-lg shadow-lg transition-all">Open Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-blue-600 text-4xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">Simplified Access</h2>
                <p class="text-gray-600">Access multiple Ollama models through a single, unified API interface with smart request queuing.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-blue-600 text-4xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">Comprehensive Monitoring</h2>
                <p class="text-gray-600">Track API usage, performance metrics, error rates, and model performance through an intuitive dashboard.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-blue-600 text-4xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">Flexible Configuration</h2>
                <p class="text-gray-600">Customize model parameters, configure queue settings, and manage multiple Ollama models easily.</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8 mb-16">
            <h2 class="text-3xl font-bold mb-6">API Documentation</h2>

            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-3">Authentication</h3>
                <p class="mb-4">All API requests require an API key sent in the X-API-Key header.</p>
                <pre><code class="language-bash">curl -X POST \
    -H "Content-Type: application/json" \
    -H "X-API-Key: your_api_key" \
    -d '{"prompt": "Hello, how are you?"}' \
    https://your-domain.com/api/v1/generate/llama2</code></pre>
            </div>

            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-3">Available Endpoints</h3>

                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">List Models</h4>
                    <p class="mb-2">Get a list of all available models:</p>
                    <pre><code class="language-bash">GET /api/v1/models</code></pre>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">Text Generation</h4>
                    <p class="mb-2">Generate text completions with a specific model:</p>
                    <pre><code class="language-bash">POST /api/v1/generate/{modelId}</code></pre>
                    <p class="mb-2">Request body example:</p>
                    <pre><code class="language-json">{
    "prompt": "Write a poem about artificial intelligence.",
    "temperature": 0.7,
    "max_tokens": 500
}</code></pre>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">Chat Completions</h4>
                    <p class="mb-2">Generate chat completions with a specific model:</p>
                    <pre><code class="language-bash">POST /api/v1/chat/{modelId}</code></pre>
                    <p class="mb-2">Request body example:</p>
                    <pre><code class="language-json">{
    "messages": [
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": "What is the capital of France?"}
    ],
    "temperature": 0.7,
    "max_tokens": 500
}</code></pre>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold mb-3">Response Format</h3>
                <p class="mb-2">Successful responses will match the format of the Ollama API responses.</p>
                <p class="mb-2">If the system is under heavy load, requests will be queued and you'll receive:</p>
                <pre><code class="language-json">{
    "message": "Your request has been queued and will be processed shortly",
    "queue_position": 3,
    "estimated_time": 180
}</code></pre>
            </div>
        </div>

        <div class="bg-slate-800 text-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-4">Ready to get started?</h2>
                    <p class="text-xl mb-6 md:mb-0">Access the dashboard to start managing your Ollama API.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all">Open Dashboard</a>
            </div>
        </div>
    </main>

    <footer class="bg-slate-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} Ollama API Management System</p>
                <p class="text-sm text-gray-400 mt-2">Powered by Laravel and Ollama</p>
            </div>
        </div>
    </footer>
</body>
</html>
