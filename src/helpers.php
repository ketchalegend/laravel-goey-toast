<?php

declare(strict_types=1);

use Ketchalegend\LaravelGoeyToast\GoeyToastManager;

if (! function_exists('goey_toast')) {
    /**
     * @param  array<string, mixed>  $meta
     */
    function goey_toast(string $message, string $type = 'info', ?int $duration = null, ?bool $dismissible = null, array $meta = []): void
    {
        app(GoeyToastManager::class)->push($message, $type, $duration, $dismissible, $meta);
    }
}
