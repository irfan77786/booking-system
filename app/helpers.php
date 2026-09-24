<?php

if (! function_exists('admin_storage_url')) {
    /**
     * Same-origin URL for admin storage files (vehicle images).
     * Proxied via /media/... so mobile browsers are not blocked by admin SSL / mixed content.
     */
    function admin_storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        // Absolute remote URL → extract storage-relative path when it is our admin host
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parts = parse_url($path);
            $urlPath = $parts['path'] ?? '';
            if (preg_match('#/storage/(.+)$#', $urlPath, $m)) {
                $path = $m[1];
            } else {
                return $path;
            }
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return url('/media/' . $path);
    }
}
