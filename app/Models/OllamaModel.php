<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OllamaModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'model_id',
        'description',
        'parameters',
        'is_active',
        'request_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'json',
        'is_active' => 'boolean',
        'request_count' => 'integer',
    ];

    /**
     * Get the API requests for the model.
     */
    public function apiRequests(): HasMany
    {
        return $this->hasMany(ApiRequest::class);
    }

    /**
     * Increment the request count for this model.
     */
    public function incrementRequestCount(): void
    {
        $this->increment('request_count');
    }
}
