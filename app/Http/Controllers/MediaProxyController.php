<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MediaProxyController extends Controller
{
    /**
     * Serve admin storage files over this site's HTTPS (avoids mobile SSL / mixed-content blocks).
     */
    public function show(Request $request, string $path): Response
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        $path = ltrim((string) preg_replace('#^storage/#', '', $path), '/');

        if ($path === '' || ! preg_match('#^[A-Za-z0-9_./\-]+$#', $path)) {
            abort(404);
        }

        $cacheDir = storage_path('app/media-cache');
        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $cacheKey = sha1($path);
        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey;
        $metaFile = $cacheFile . '.meta';

        if (is_readable($cacheFile) && is_readable($metaFile)) {
            $meta = json_decode((string) file_get_contents($metaFile), true) ?: [];
            $age = time() - (int) filemtime($cacheFile);
            if ($age < 604800) {
                return response(file_get_contents($cacheFile), 200, [
                    'Content-Type' => $meta['content_type'] ?? 'image/jpeg',
                    'Cache-Control' => 'public, max-age=604800',
                    'X-Media-Cache' => 'HIT',
                ]);
            }
        }

        $body = null;
        $contentType = 'image/jpeg';

        foreach ($this->candidateUrls($path) as $url) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'Accept' => 'image/*,*/*',
                        'User-Agent' => 'DallasBookingMediaProxy/1.0',
                    ])
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 100) {
                    $body = $response->body();
                    $contentType = $response->header('Content-Type') ?: $this->guessMime($path);
                    break;
                }
            } catch (\Throwable $e) {
                Log::warning('MediaProxy fetch failed', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($body === null) {
            abort(404);
        }

        @file_put_contents($cacheFile, $body);
        @file_put_contents($metaFile, json_encode(['content_type' => $contentType, 'path' => $path]));

        return response($body, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=604800',
            'X-Media-Cache' => 'MISS',
        ]);
    }

    /**
     * @return list<string>
     */
    private function candidateUrls(string $path): array
    {
        $admin = rtrim((string) config('services.admin_url'), '/');
        $hosts = array_values(array_unique(array_filter([
            $admin,
            'http://admin.dallasblacklimocars.com',
            'https://admin.dallasblacklimocars.com',
        ])));

        $urls = [];
        foreach ($hosts as $host) {
            $urls[] = $host . '/storage/' . $path;
        }

        return $urls;
    }

    private function guessMime(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }
}
