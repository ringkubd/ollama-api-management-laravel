@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">API Documentation</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.documentation.postman') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Download Postman Collection
            </a>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="markdown-content prose max-w-none">
            {!! \Illuminate\Support\Str::markdown($content) !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add syntax highlighting to code blocks
    document.querySelectorAll('pre code').forEach((block) => {
        block.classList.add('bg-gray-100', 'p-4', 'rounded', 'block', 'overflow-x-auto', 'mb-4');
    });
</script>
@endsection
