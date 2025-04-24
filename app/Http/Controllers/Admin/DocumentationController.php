<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    /**
     * Display the API documentation.
     */
    public function viewDocs()
    {
        $docsPath = public_path('documentation/api-docs.md');
        $content = '';

        if (File::exists($docsPath)) {
            $content = File::get($docsPath);

            // Convert Markdown to HTML if needed
            // For a simple solution, we'll just use Markdown content directly
            // In a production environment, you might want to use a Markdown parser
        }

        return view('admin.documentation.view', ['content' => $content]);
    }

    /**
     * Download the Postman collection JSON file.
     */
    public function downloadPostman()
    {
        $postmanPath = public_path('documentation/ollama-api-postman-collection.json');

        if (File::exists($postmanPath)) {
            return response()->download($postmanPath, 'ollama-api-postman-collection.json');
        }

        return back()->with('error', 'Postman collection file not found.');
    }
}
