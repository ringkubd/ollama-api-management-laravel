<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        'is_active',
        'last_used_at',
        'request_count',
        'allowed_models',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'request_count' => 'integer',
        'allowed_models' => 'array',
    ];

    /**
     * Get the API requests associated with this API key.
     */
    public function apiRequests(): HasMany
    {
        return $this->hasMany(ApiRequest::class, 'api_key', 'key');
    }

    /**
     * Generate a new API key.
     */
    public static function generateKey(): string
    {
        return Str::random(32);
    }

    /**
     * Increment the request count for this API key.
     */
    public function incrementRequestCount(): void
    {
        $this->increment('request_count');
        $this->update(['last_used_at' => now()]);
    }
}
