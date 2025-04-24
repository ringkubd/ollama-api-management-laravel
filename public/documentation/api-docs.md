# Ollama API Management - API Documentation

Welcome to the Ollama API Management System API documentation. This guide provides all the information needed to interact with our API endpoints for text generation and chat completions using Ollama models.

## Base URL

```
https://your-domain.com/api
```

Replace `your-domain.com` with your actual domain where the Ollama API Management system is deployed.

## Authentication

All API requests require authentication using an API key. Include your API key in the request headers:

```
Authorization: Bearer your_api_key_here
```

API keys can be created and managed through the dashboard at `/admin/api-keys`.

## Available Endpoints

### List Available Models

Retrieve a list of all available models you can use for generation.

**Endpoint:** `GET /models`

**Response Example:**
```json
{
  "models": [
    {
      "id": "llama2:7b",
      "name": "Llama 2 7B",
      "description": "Meta's Llama 2 7B parameter model",
      "is_active": true,
      "parameters": {
        "temperature": 0.7,
        "top_p": 0.9,
        "top_k": 40
      }
    },
    {
      "id": "codellama:7b",
      "name": "Code Llama 7B",
      "description": "Meta's Code Llama 7B parameter model optimized for code generation",
      "is_active": true,
      "parameters": {
        "temperature": 0.5,
        "top_p": 0.95
      }
    }
  ]
}
```

### Chat Completions

Generate a conversational response based on a series of messages.

**Endpoint:** `POST /chat`

**Request Body:**
```json
{
  "model": "llama2:7b",
  "messages": [
    {
      "role": "system",
      "content": "You are a helpful assistant."
    },
    {
      "role": "user",
      "content": "Hello, can you explain how neural networks work?"
    }
  ],
  "temperature": 0.7,
  "max_tokens": 1000
}
```

**Parameters:**
- `model` (required): The ID of the model to use for generation
- `messages` (required): An array of messages in the conversation history
  - Each message has a `role` (system, user, or assistant) and `content`
- `temperature` (optional): Controls randomness. Higher values (e.g., 0.8) make output more random, lower values (e.g., 0.2) make it more deterministic. Default: 0.7
- `max_tokens` (optional): Maximum number of tokens to generate. Default: 1024

**Response:**
```json
{
  "id": "cmpl-123456",
  "object": "chat.completion",
  "created": 1677858242,
  "model": "llama2:7b",
  "choices": [
    {
      "message": {
        "role": "assistant",
        "content": "Neural networks are computational systems inspired by the human brain..."
      },
      "finish_reason": "stop",
      "index": 0
    }
  ],
  "usage": {
    "prompt_tokens": 38,
    "completion_tokens": 125,
    "total_tokens": 163
  }
}
```

### Text Generation

Generate text based on a prompt.

**Endpoint:** `POST /generate`

**Request Body:**
```json
{
  "model": "llama2:7b",
  "prompt": "Write a short poem about artificial intelligence.",
  "temperature": 0.7,
  "max_tokens": 1000
}
```

**Parameters:**
- `model` (required): The ID of the model to use for generation
- `prompt` (required): The text prompt to generate from
- `temperature` (optional): Controls randomness. Default: 0.7
- `max_tokens` (optional): Maximum number of tokens to generate. Default: 1024

**Response:**
```json
{
  "id": "cmpl-123456",
  "object": "text.completion",
  "created": 1677858242,
  "model": "llama2:7b",
  "choices": [
    {
      "text": "Silicon dreams in neural maze,\nWhispers of code, a digital phase.\nThoughts electric, bound by no chain,\nArtificial wisdom, both wild and tame.\n\nIn data oceans, patterns emerge,\nHuman and machine begin to merge.\nA dance of logic, beyond our sight,\nIntelligence born from binary light.",
      "finish_reason": "stop",
      "index": 0
    }
  ],
  "usage": {
    "prompt_tokens": 12,
    "completion_tokens": 85,
    "total_tokens": 97
  }
}
```

## Error Handling

When an API request fails, you will receive a JSON response with an error message and an appropriate HTTP status code.

**Example Error Response:**
```json
{
  "error": {
    "message": "Invalid API key provided",
    "type": "authentication_error",
    "code": "invalid_api_key"
  }
}
```

Common HTTP status codes:
- `400 Bad Request` - Your request was improperly formatted or had invalid parameters
- `401 Unauthorized` - Invalid or missing API key
- `404 Not Found` - The requested resource does not exist
- `429 Too Many Requests` - You have exceeded your rate limit
- `500 Internal Server Error` - Something went wrong on our end

## Rate Limits

Requests are subject to rate limiting based on your API key's permissions. The current rate limits are:

- Standard API keys: 60 requests per minute
- Premium API keys: 300 requests per minute

You can check your current rate limit status in the response headers:
- `X-RateLimit-Limit`: Maximum number of requests allowed per minute
- `X-RateLimit-Remaining`: Number of requests remaining in the current window
- `X-RateLimit-Reset`: Time at which the rate limit will reset (Unix timestamp)

## Using with cURL

Here's an example of making a chat completion request using cURL:

```bash
curl -X POST "https://your-domain.com/api/chat" \
  -H "Authorization: Bearer your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "llama2:7b",
    "messages": [
      {
        "role": "system",
        "content": "You are a helpful assistant."
      },
      {
        "role": "user",
        "content": "Write me a short story about a robot who discovers emotions."
      }
    ],
    "temperature": 0.7
  }'
```

## Playground

For testing API calls interactively, you can use the Model Playground available at `/admin/playground`. The playground provides a user-friendly interface to experiment with different models, parameters, and prompts without writing any code.

## Support

If you encounter any issues or have questions about the API, please contact our support team at support@example.com.
