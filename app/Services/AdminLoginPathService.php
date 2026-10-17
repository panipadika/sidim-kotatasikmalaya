<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AdminLoginPathService
{
    public const DEFAULT_PATH = 'admin-dashboard';

    public const RESERVED_PATHS = [
        '',
        'login',
        'admin',
        'admin/login',
        'dashboard',
        'public',
        'users',
        'settings',
        'logout',
        'indikator',
        'capaian',
        'target',
        'analisis',
        'detail-indikator',
        'api',
        'storage',
        'home',
    ];

    protected static ?string $cachedPath = null;

    public static function getStorageFilePath(): string
    {
        return storage_path('app/admin_login_path.txt');
    }

    public static function getPath(): string
    {
        if (static::$cachedPath !== null) {
            return static::$cachedPath;
        }

        $filePath = static::getStorageFilePath();
        if (File::exists($filePath)) {
            $content = trim((string) File::get($filePath));
            if (!empty($content)) {
                return static::$cachedPath = $content;
            }
        }

        try {
            if (Schema::hasTable('system_setting')) {
                $setting = DB::table('system_setting')
                    ->where('key', 'admin_login_path')
                    ->value('value');

                if (!empty($setting)) {
                    $setting = trim((string) $setting);
                    static::writeToStorage($setting);
                    return static::$cachedPath = $setting;
                }
            }
        } catch (\Throwable $e) {
            // Ignore during early boot or when DB connection is unavailable
        }

        return static::$cachedPath = self::DEFAULT_PATH;
    }

    public static function setPath(string $newPath): string
    {
        $sanitized = strtolower(trim($newPath, " \t\n\r\0\x0B/"));
        if (empty($sanitized)) {
            $sanitized = self::DEFAULT_PATH;
        }

        static::writeToStorage($sanitized);

        try {
            if (Schema::hasTable('system_setting')) {
                DB::table('system_setting')->updateOrInsert(
                    ['key' => 'admin_login_path'],
                    ['value' => $sanitized, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        } catch (\Throwable $e) {
            // Ignore if DB is unavailable
        }

        static::$cachedPath = $sanitized;

        try {
            Artisan::call('route:clear');
        } catch (\Throwable $e) {
            // Ignore if in test runner without artisan
        }

        return $sanitized;
    }

    public static function isReserved(string $path): bool
    {
        $sanitized = strtolower(trim($path, " \t\n\r\0\x0B/"));

        return in_array($sanitized, self::RESERVED_PATHS, true);
    }

    public static function getFullUrl(?string $path = null): string
    {
        $target = $path !== null ? trim($path, '/') : static::getPath();

        return url('/' . $target);
    }

    protected static function writeToStorage(string $path): void
    {
        $filePath = static::getStorageFilePath();
        $directory = dirname($filePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }
        File::put($filePath, $path);
    }

    public static function resetToDefault(): void
    {
        static::$cachedPath = null;
        $filePath = static::getStorageFilePath();
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        try {
            if (Schema::hasTable('system_setting')) {
                DB::table('system_setting')->where('key', 'admin_login_path')->delete();
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }
}
