<?php

if (! function_exists('admin_storage_url')) {
    /**
     * Absolute URL for a file stored on the admin panel (e.g. vehicle images).
     */
    function admin_storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $base = '';
        try {
            $base = rtrim((string) config('services.admin_url'), '/');
        } catch (\Throwable $e) {
            $base = '';
        }

        if ($base === '') {
            $base = 'https://admin.dallasblacklimocars.com';
        }

        $path = ltrim($path, '/');
        if (! str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }

        return $base . '/' . $path;
    }
}
