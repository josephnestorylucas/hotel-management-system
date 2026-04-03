<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    public $incrementing = false;
    public $timestamps = true;

    protected $primaryKey = 'key';
    protected $keyType = 'string';

    protected $fillable = ['id', 'key', 'value', 'description', 'updated_by', 'updated_at'];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set a setting value by key (create if doesn't exist).
     */
    public static function setValue(string $key, mixed $value, ?string $description = null, ?string $updatedBy = null): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update([
                'value' => $value,
                'description' => $description ?? $setting->description,
                'updated_by' => $updatedBy,
                'updated_at' => now(),
            ]);
        } else {
            static::create([
                'id' => Str::uuid()->toString(),
                'key' => $key,
                'value' => $value,
                'description' => $description,
                'updated_by' => $updatedBy,
            ]);
        }

        // Clear related caches
        Cache::forget($key);
        Cache::forget('system_currency');
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function getAllSettings(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
