<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ollama_model_id',
        'api_key',
        'endpoint',
        'request_payload',
        'response_payload',
        'ip_address',
        'user_agent',
        'status_code',
        'response_time',
        'error_message',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_payload' => 'json',
        'response_payload' => 'json',
        'response_time' => 'float',
    ];

    /**
     * Get the Ollama model that the request is using.
     */
    public function ollamaModel(): BelongsTo
    {
        return $this->belongsTo(OllamaModel::class);
    }
}
