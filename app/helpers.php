<?php

if (! function_exists('admin_storage_url')) {
    

    function admin_storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

         
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
