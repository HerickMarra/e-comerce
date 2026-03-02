<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'is_encrypted'];

    const CACHE_KEY = 'app_settings_all';
    const CACHE_TTL = 86400; // 24 hours (in seconds)

    /**
     * Load all settings from cache (or DB on miss), as key => decrypted_value map.
     */
    protected static function all_cached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $map = [];
            foreach (self::all() as $setting) {
                if ($setting->is_encrypted && $setting->value) {
                    try {
                        $map[$setting->key] = Crypt::decryptString($setting->value);
                    } catch (\Exception $e) {
                        $map[$setting->key] = null;
                    }
                } else {
                    $map[$setting->key] = $setting->value;
                }
            }
            return $map;
        });
    }

    /**
     * Get a setting by its key (from cache, zero DB queries after first call).
     */
    public static function get($key, $default = null): mixed
    {
        $all = self::all_cached();
        return array_key_exists($key, $all) ? ($all[$key] ?? $default) : $default;
    }

    /**
     * Set a setting and invalidate the cache.
     */
    public static function set($key, $value, $encrypt = false): self
    {
        $stored = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => ($encrypt && $value) ? Crypt::encryptString($value) : $value,
                'is_encrypted' => $encrypt,
            ]
        );

        // Invalidate immediately so next request gets fresh data
        Cache::forget(self::CACHE_KEY);

        return $stored;
    }

    /**
     * Flush all settings cache. Call after bulk updates.
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
