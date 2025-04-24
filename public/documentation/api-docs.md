# Ollama API Management - API Documentation

Welcome to the Ollama API Management System API documentation. This guide provides all the information needed to interact with our API endpoints for text generation and chat completions using Ollama models.

## Base URL

```
https://your-domain.com/api/v1
```

Replace `your-domain.com` with your actual domain where the Ollama API Management system is deployed.

## Authentication

All API requests require authentication using an API key. Include your API key in the request headers:

```
X-API-Key: your_api_key_here
```

API keys can be created and managed through the dashboard at `/admin/api-keys`.

## Response Codes

The API uses standard HTTP response codes:

- `200 OK`: Request successful
- `202 Accepted`: Request has been accepted for processing (queued)
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Missing or invalid API key
- `403 Forbidden`: Valid API key but insufficient permissions
- `404 Not Found`: The requested resource does not exist
- `429 Too Many Requests`: Rate limit exceeded, request has been queued
- `500 Internal Server Error`: Something went wrong on the server

## Endpoints

### List Available Models

Returns a list of all available models that can be used for generation.

**Endpoint:** `GET /models`

**Example Request:**

```bash
curl -X GET \
  https://your-domain.com/api/v1/models \
  -H 'X-API-Key: your_api_key_here'
```

**Example Response:**

```json
{
  "models": [
    {
      "name": "Llama 2 7B",
      "model_id": "llama2",
      "description": "Meta's Llama 2 7B parameter model"
    },
    {
      "name": "Mistral 7B",
      "model_id": "mistral",
      "description": "Mistral AI's 7B parameter model"
    }
  ]
}
```

### Text Generation

Generates text completion based on a provided prompt.

**Endpoint:** `POST /generate/{modelId}`

**URL Parameters:**
- `modelId` (required): The ID of the model to use for generation (e.g., "llama2")

**Request Body:**

```json
{
  "prompt": "Once upon a time",
  "max_tokens": 100,
  "temperature": 0.7,
  "top_p": 0.9,
  "frequency_penalty": 0,
  "presence_penalty": 0,
  "stop": ["\n\n", "END"]
}
```

**Request Parameters:**
- `prompt` (required): The text prompt to generate from
- `max_tokens` (optional): Maximum number of tokens to generate (default: varies by model)
- `temperature` (optional): Controls randomness (0.0-1.0, default: 0.8)
- `top_p` (optional): Nucleus sampling parameter (default: 0.9)
- `frequency_penalty` (optional): Reduces repetition of token sequences (default: 0)
- `presence_penalty` (optional): Reduces repetition of topics (default: 0)
- `stop` (optional): Array of sequences where the API will stop generating (default: none)

**Example Request:**

```bash
curl -X POST \
  https://your-domain.com/api/v1/generate/llama2 \
  -H 'X-API-Key: your_api_key_here' \
  -H 'Content-Type: application/json' \
  -d '{
    "prompt": "Write a short poem about programming",
    "max_tokens": 150,
    "temperature": 0.7
  }'
```

**Example Response:**

```json
{
  "id": "gen_12345",
  "object": "text_completion",
  "created": 1714064522,
  "model": "llama2",
  "choices": [
    {
      "text": "In lines of code, a world unfolds,\nLogic and art in patterns told.\nBugs and features, side by side,\nIn digital realms where dreams reside.\n\nCompilers hum, interpreters dance,\nGiving human thoughts their one chance\nTo transform into electric life,\nSolving problems, easing strife.",
      "index": 0,
      "finish_reason": "stop"
    }
  ],
  "usage": {
    "prompt_tokens": 6,
    "completion_tokens": 59,
    "total_tokens": 65
  }
}
```

**Queued Response:**

If the system is under heavy load, your request may be queued:

```json
{
  "message": "Your request has been queued and will be processed shortly",
  "queue_position": 3,
  "estimated_time": 180
}
```

### Chat Completion

Generates conversational responses based on a message history.

**Endpoint:** `POST /chat/{modelId}`

**URL Parameters:**
- `modelId` (required): The ID of the model to use for chat (e.g., "llama2")

**Request Body:**

```json
{
  "messages": [
    {
      "role": "system",
      "content": "You are a helpful assistant."
    },
    {
      "role": "user",
      "content": "Hello, how are you today?"
    }
  ],
  "temperature": 0.7,
  "max_tokens": 100
}
```

**Request Parameters:**
- `messages` (required): Array of messages, each with `role` and `content`
  - Roles can be: "system", "user", or "assistant"
- Other parameters match those of the `/generate` endpoint

**Example Request:**

```bash
curl -X POST \
  https://your-domain.com/api/v1/chat/llama2 \
  -H 'X-API-Key: your_api_key_here' \
  -H 'Content-Type: application/json' \
  -d '{
    "messages": [
      {
        "role": "system",
        "content": "You are a helpful assistant."
      },
      {
        "role": "user",
        "content": "Explain quantum computing in simple terms."
      }
    ],
    "temperature": 0.7,
    "max_tokens": 200
  }'
```

**Example Response:**

```json
{
  "id": "chat_12345",
  "object": "chat.completion",
  "created": 1714064665,
  "model": "llama2",
  "choices": [
    {
      "index": 0,
      "message": {
        "role": "assistant",
        "content": "Quantum computing is like having a super-powered calculator that can try many possible solutions at once instead of one at a time. Regular computers use bits (0s and 1s), but quantum computers use \"qubits\" that can be 0, 1, or both simultaneously—a bit like being in two places at once. This allows them to solve certain complex problems much faster than regular computers. Think of it as having parallel universes helping to solve your math homework!"
      },
      "finish_reason": "stop"
    }
  ],
  "usage": {
    "prompt_tokens": 29,
    "completion_tokens": 96,
    "total_tokens": 125
  }
}
```

## Error Responses

**Invalid API Key:**
```json
{
  "error": "Unauthorized",
  "message": "Invalid API key"
}
```

**Model Not Found:**
```json
{
  "error": "Model not found",
  "message": "The model 'nonexistent-model' is not available"
}
```

**Rate Limited:**
```json
{
  "error": "Too many requests",
  "message": "The server is currently handling too many requests. Please try again later."
}
```

**Generation Error:**
```json
{
  "error": "Generation failed",
  "message": "An error occurred during generation: [specific error details]"
}
```

## Rate Limits and Queuing

The API implements a queuing system for handling high traffic. When the queue size exceeds the configured maximum, requests will be rejected with a 429 status code. Otherwise, requests will be queued for processing and will receive a 202 status code with queue position information.

## Example Applications

### Node.js

```javascript
const axios = require('axios');

async function generateText(prompt) {
  try {
    const response = await axios.post('https://your-domain.com/api/v1/generate/llama2', {
      prompt: prompt,
      max_tokens: 100,
      temperature: 0.7
    }, {
      headers: {
        'Content-Type': 'application/json',
        'X-API-Key': 'your_api_key_here'
      }
    });
    
    return response.data;
  } catch (error) {
    console.error('Error:', error.response ? error.response.data : error.message);
    throw error;
  }
}

// Example usage
generateText('Write a haiku about programming')
  .then(result => console.log(result))
  .catch(err => console.error('Failed to generate text:', err));
```

### Python

```python
import requests

def generate_text(prompt):
    url = 'https://your-domain.com/api/v1/generate/llama2'
    headers = {
        'Content-Type': 'application/json',
        'X-API-Key': 'your_api_key_here'
    }
    payload = {
        'prompt': prompt,
        'max_tokens': 100,
        'temperature': 0.7
    }
    
    response = requests.post(url, headers=headers, json=payload)
    
    if response.status_code == 200:
        return response.json()
    elif response.status_code == 202:
        print(f"Request queued: {response.json()}")
        return None
    else:
        print(f"Error: {response.status_code}")
        print(response.json())
        return None

# Example usage
result = generate_text('Write a haiku about programming')
if result:
    print(result['choices'][0]['text'])
```

### PHP

```php
<?php

function generate_text($prompt) {
    $url = 'https://your-domain.com/api/v1/generate/llama2';
    $data = [
        'prompt' => $prompt,
        'max_tokens' => 100,
        'temperature' => 0.7
    ];
    
    $headers = [
        'Content-Type: application/json',
        'X-API-Key: your_api_key_here'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($status_code == 200) {
        return $result;
    } else {
        echo "Error: " . $status_code . "\n";
        print_r($result);
        return null;
    }
}

// Example usage
$result = generate_text('Write a haiku about programming');
if ($result) {
    echo $result['choices'][0]['text'] . "\n";
}
```

## Getting Help

If you encounter any issues with the API, please contact your system administrator or refer to the admin dashboard for more information.
