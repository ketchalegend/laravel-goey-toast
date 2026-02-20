<?php

declare(strict_types=1);

namespace Ketchalegend\LaravelGoeyToast;

use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;

class GoeyToastManager
{
    public function __construct(protected Session $session) {}

    /**
     * @param  array<string, mixed>  $meta
     */
    public function push(
        string $message,
        string $type = 'info',
        ?int $duration = null,
        ?bool $dismissible = null,
        array $meta = [],
    ): Toast {
        $title = is_string($meta['title'] ?? null) ? (string) $meta['title'] : null;
        $description = is_string($meta['description'] ?? null) ? (string) $meta['description'] : null;
        $action = is_array($meta['action'] ?? null) ? $meta['action'] : null;
        $spring = isset($meta['spring']) ? (bool) $meta['spring'] : null;

        unset($meta['title'], $meta['description'], $meta['action'], $meta['spring']);

        $toast = new Toast(
            id: (string) Str::uuid(),
            type: $type,
            message: $message,
            title: $title,
            description: $description,
            action: $action,
            spring: $spring,
            duration: $duration ?? (int) config('goey-toast.default_duration', 4500),
            dismissible: $dismissible ?? (bool) config('goey-toast.dismissible', true),
            meta: $meta,
        );

        $sessionKey = (string) config('goey-toast.session_key', 'goey_toasts');
        $toasts = $this->session->get($sessionKey, []);
        $toasts[] = $toast->toArray();

        $this->session->flash($sessionKey, $toasts);

        return $toast;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function success(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = []): Toast
    {
        return $this->push($message, 'success', $duration, $dismissible, $meta);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function info(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = []): Toast
    {
        return $this->push($message, 'info', $duration, $dismissible, $meta);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function warning(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = []): Toast
    {
        return $this->push($message, 'warning', $duration, $dismissible, $meta);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function error(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = []): Toast
    {
        return $this->push($message, 'danger', $duration, $dismissible, $meta);
    }
}
