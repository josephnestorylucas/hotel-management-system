<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    use HasSoftDelete;

    public $incrementing = false;
    public $timestamps = true;

    protected $primaryKey = 'key';
    protected $keyType = 'string';

    protected $fillable = ['id', 'key', 'value', 'description', 'updated_by', 'updated_at'];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static array $encryptedKeys = [
        'sms_api_key',
        'mail_password',
        'snipe_api_key',
        'snipe_api_secret',
        'snipe_webhook_secret',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');

        if ($value === null) {
            return $default;
        }

        if (static::shouldEncrypt($key) && $value !== '') {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable $exception) {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set a setting value by key (create if doesn't exist).
     */
    public static function setValue(string $key, mixed $value, ?string $description = null, ?string $updatedBy = null): void
    {
        $storedValue = static::normalizeValue($key, $value);
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update([
                'value' => $storedValue,
                'description' => $description ?? $setting->description,
                'updated_by' => $updatedBy,
                'updated_at' => now(),
            ]);
        } else {
            static::create([
                'id' => Str::uuid()->toString(),
                'key' => $key,
                'value' => $storedValue,
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

    protected static function shouldEncrypt(string $key): bool
    {
        return in_array($key, static::$encryptedKeys, true);
    }

    protected static function normalizeValue(string $key, mixed $value): string
    {
        $stringValue = $value === null ? '' : (string) $value;

        if (!static::shouldEncrypt($key) || $stringValue === '') {
            return $stringValue;
        }

        return Crypt::encryptString($stringValue);
    }
}
