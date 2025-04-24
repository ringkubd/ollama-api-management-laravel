@extends('layouts.app')

@section('title', 'Model Playground')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Model Playground</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
            <p class="text-gray-600 mb-4">
                Test your Ollama models directly in the browser. Switch between Chat and Text Generation modes to see how the models respond to different inputs.
            </p>

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px" id="tabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block py-2 px-4 text-blue-600 border-b-2 border-blue-600 active" id="chat-tab" data-tab-target="chat-panel" type="button" role="tab">
                            Chat Mode
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block py-2 px-4 text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300" id="generate-tab" data-tab-target="generate-panel" type="button" role="tab">
                            Text Generation
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Model Selection (Common to both tabs) -->
            <div class="mt-6 mb-4">
                <label for="model-select" class="block text-sm font-medium text-gray-700 mb-1">Select Model:</label>
                <select id="model-select" class="border border-gray-300 rounded-md w-full p-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @foreach($models as $model)
                        <option value="{{ $model->model_id }}">{{ $model->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Parameter Controls (Common to both tabs) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature: <span id="temperature-value">0.7</span></label>
                    <input type="range" id="temperature" min="0" max="2" step="0.1" value="0.7" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">Lower values produce more focused and deterministic outputs.</p>
                </div>
                <div>
                    <label for="max-tokens" class="block text-sm font-medium text-gray-700 mb-1">Max Tokens:</label>
                    <input type="number" id="max-tokens" value="1024" min="1" max="4096" class="border border-gray-300 rounded-md w-full p-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Maximum number of tokens to generate.</p>
                </div>
            </div>

            <!-- Response timing indicator -->
            <div id="response-timing" class="mb-4 hidden">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Response time: <span id="response-time">0</span>ms</span>
                </div>
            </div>

            <!-- Chat Interface -->
            <div id="chat-panel" class="tab-panel">
                <div class="border rounded-md mb-4">
                    <div id="chat-messages" class="p-4 max-h-96 overflow-y-auto space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-gray-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0012 0c0-1.146-.322-2.217-.878-3.125A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-3 max-w-3xl">
                                <p class="text-sm">Welcome to the chat! I'm here to help answer your questions.</p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t p-4">
                        <form id="chat-form" class="flex">
                            <input type="text" id="chat-input" class="flex-1 border border-gray-300 rounded-l-md p-2 focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="Type your message...">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Text Generation Interface -->
            <div id="generate-panel" class="tab-panel hidden">
                <div class="mb-4">
                    <label for="prompt" class="block text-sm font-medium text-gray-700 mb-1">Prompt:</label>
                    <textarea id="prompt" rows="4" class="border border-gray-300 rounded-md w-full p-2 focus:ring focus:ring-blue-200 focus:border-blue-500" placeholder="Enter your prompt here..."></textarea>
                </div>

                <div class="mb-4">
                    <button id="generate-btn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Generate Text
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Generated Output:</label>
                    <div id="generation-result" class="border rounded-md p-4 min-h-32 bg-gray-50">
                        <p class="text-gray-400 italic">Generated text will appear here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize UI elements
        const tabs = document.querySelectorAll('#tabs button');
        const temperatureSlider = document.getElementById('temperature');
        const temperatureValue = document.getElementById('temperature-value');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const modelSelect = document.getElementById('model-select');
        const loadingIndicator = document.getElementById('loading-indicator');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        const responseTiming = document.getElementById('response-timing');
        const responseTime = document.getElementById('response-time');
        const viewDetailsBtn = document.getElementById('view-details-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const closeModalBtn2 = document.getElementById('close-modal-btn2');
        const errorModal = document.getElementById('error-modal');
        const modalErrorText = document.getElementById('modal-error-text');
        const exceptionClass = document.getElementById('exception-class');
        const exceptionFileLine = document.getElementById('exception-file-line');
        const exceptionTrace = document.getElementById('exception-trace');
        const copyErrorBtn = document.getElementById('copy-error-btn');

        // Store the last error details
        window.lastErrorDetails = {};

        // Store conversation history
        let messageHistory = [{
            role: 'assistant',
            content: 'Welcome to the chat! I\'m here to help answer your questions.'
        }];

        // Tab switching
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab-target');

                // Hide all panels
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });

                // Show target panel
                document.getElementById(target).classList.remove('hidden');

                // Update active tab
                tabs.forEach(t => {
                    t.classList.remove('text-blue-600', 'border-blue-600');
                    t.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
                });

                tab.classList.add('text-blue-600', 'border-blue-600');
                tab.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
            });
        });

        // Temperature slider
        temperatureSlider.addEventListener('input', () => {
            temperatureValue.textContent = temperatureSlider.value;
        });

        // Chat form submission
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const message = chatInput.value.trim();
            if (!message) return;

            // Clear input
            chatInput.value = '';

            // Add user message to UI
            appendMessage('user', message);

            // Add to history
            messageHistory.push({
                role: 'user',
                content: message
            });

            // Show loading, hide previous errors and timing
            if (loadingIndicator) loadingIndicator.classList.remove('hidden');
            if (errorMessage) errorMessage.classList.add('hidden');
            if (responseTiming) responseTiming.classList.add('hidden');

            const startTime = performance.now();

            try {
                // Get current parameters
                const modelId = modelSelect.value;
                const temperature = parseFloat(temperatureSlider.value);
                const maxTokens = parseInt(document.getElementById('max-tokens').value);

                // Make API request
                const response = await fetch('{{ route('admin.playground.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        model_id: modelId,
                        messages: messageHistory,
                        temperature: temperature,
                        max_tokens: maxTokens
                    })
                });

                const data = await response.json();
                const endTime = performance.now();
                const duration = endTime - startTime;

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to get response');
                }

                // Extract the assistant's message
                const assistantMessage = data.choices[0].message.content;

                // Add to history
                messageHistory.push({
                    role: 'assistant',
                    content: assistantMessage
                });

                // Add message to UI
                appendMessage('assistant', assistantMessage);

                // Show response time
                responseTime.textContent = Math.round(duration);
                responseTiming.classList.remove('hidden');

            } catch (error) {
                console.error('Error:', error);
                window.lastErrorDetails = error.responseJSON || {};
                errorText.textContent = error.message || 'An error occurred while processing your request.';
                errorMessage.classList.remove('hidden');
            } finally {
                // Hide loading
                loadingIndicator.classList.add('hidden');

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });

        // Error modal handling
        if (viewDetailsBtn) {
            viewDetailsBtn.addEventListener('click', () => {
                modalErrorText.textContent = window.lastErrorDetails.error || 'Unknown error';
                exceptionClass.textContent = window.lastErrorDetails.exception_class || 'N/A';
                exceptionFileLine.textContent = window.lastErrorDetails.exception_file ?
                    `${window.lastErrorDetails.exception_file}:${window.lastErrorDetails.exception_line}` : 'N/A';
                exceptionTrace.textContent = window.lastErrorDetails.exception_trace || 'No stack trace available';

                errorModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden'); // Prevent scrolling behind modal
            });
        }

        // Close modal buttons
        if (closeModalBtn && closeModalBtn2) {
            [closeModalBtn, closeModalBtn2].forEach(btn => {
                btn.addEventListener('click', () => {
                    errorModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            });
        }

        // Copy error details to clipboard
        if (copyErrorBtn) {
            copyErrorBtn.addEventListener('click', () => {
                const errorDetails = `
Error: ${window.lastErrorDetails.error || 'Unknown error'}
Exception: ${window.lastErrorDetails.exception_class || 'N/A'}
Location: ${window.lastErrorDetails.exception_file ? `${window.lastErrorDetails.exception_file}:${window.lastErrorDetails.exception_line}` : 'N/A'}
Code: ${window.lastErrorDetails.exception_code || 'N/A'}

Stack Trace:
${window.lastErrorDetails.exception_trace || 'No stack trace available'}
                `.trim();

                navigator.clipboard.writeText(errorDetails).then(() => {
                    copyErrorBtn.textContent = 'Copied!';
                    setTimeout(() => {
                        copyErrorBtn.textContent = 'Copy to Clipboard';
                    }, 2000);
                });
            });
        }

        // Text generation functionality
        const generateBtn = document.getElementById('generate-btn');
        const promptInput = document.getElementById('prompt');
        const generationResult = document.getElementById('generation-result');

        if (generateBtn) {
            generateBtn.addEventListener('click', async () => {
                const prompt = promptInput.value.trim();
                if (!prompt) return;

                // Show loading, hide previous errors and timing
                loadingIndicator.classList.remove('hidden');
                errorMessage.classList.add('hidden');
                responseTiming.classList.add('hidden');
                generationResult.innerHTML = '<p class="text-gray-400 italic">Generating...</p>';

                const startTime = performance.now();

                try {
                    // Get current parameters
                    const modelId = modelSelect.value;
                    const temperature = parseFloat(temperatureSlider.value);
                    const maxTokens = parseInt(document.getElementById('max-tokens').value);

                    // Make API request
                    const response = await fetch('{{ route('admin.playground.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            model_id: modelId,
                            prompt: prompt,
                            temperature: temperature,
                            max_tokens: maxTokens
                        })
                    });

                    const data = await response.json();
                    const endTime = performance.now();
                    const duration = endTime - startTime;

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to get response');
                    }

                    // Display result
                    const generatedText = data.choices[0].text;
                    generationResult.innerHTML = `<pre class="whitespace-pre-wrap">${escapeHtml(generatedText)}</pre>`;

                    // Show response time
                    responseTime.textContent = Math.round(duration);
                    responseTiming.classList.remove('hidden');

                } catch (error) {
                    console.error('Error:', error);
                    window.lastErrorDetails = error.responseJSON || {};
                    errorText.textContent = error.message || 'An error occurred while processing your request.';
                    errorMessage.classList.remove('hidden');
                    generationResult.innerHTML = '<p class="text-red-500 italic">Error generating text.</p>';
                } finally {
                    // Hide loading
                    loadingIndicator.classList.add('hidden');
                }
            });
        }

        // Helper function to append messages to the chat
        function appendMessage(role, content) {
            const messageElement = document.createElement('div');
            messageElement.className = 'flex items-start mb-4';

            const iconClass = role === 'user' ? 'text-blue-500' : 'text-gray-500';
            const bgClass = role === 'user' ? 'bg-blue-50' : 'bg-gray-100';
            const iconSvg = role === 'user'
                ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>'
                : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0012 0c0-1.146-.322-2.217-.878-3.125A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>';

            messageElement.innerHTML = `
                <div class="flex-shrink-0 ${bgClass} rounded-full p-2 mr-3">
                    ${iconSvg}
                </div>
                <div class="${bgClass} rounded-lg p-3 max-w-3xl">
                    <p class="text-sm whitespace-pre-wrap">${escapeHtml(content)}</p>
                </div>
            `;

            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
</script>
@endsection
