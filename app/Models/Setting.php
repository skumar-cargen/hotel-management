<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    protected const CACHE_KEY = 'settings.all';

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::all()->mapWithKeys(fn ($s) => [$s->key => self::castValue($s->value, $s->type)])->all();
        });

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $serialized = match ($type) {
            'json', 'array' => json_encode($value),
            'boolean', 'bool' => $value ? '1' : '0',
            default => $value === null ? null : (string) $value,
        };

        self::updateOrCreate(['key' => $key], ['value' => $serialized, 'type' => $type]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer', 'int' => (int) $value,
            'boolean', 'bool' => in_array($value, ['1', 'true', 'yes', 'on'], true),
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }
}
