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
        $createdAtMs = $this->nowMs();
        $title = is_string($meta['title'] ?? null) ? (string) $meta['title'] : null;
        $description = is_string($meta['description'] ?? null) ? (string) $meta['description'] : null;
        $action = is_array($meta['action'] ?? null) ? $meta['action'] : null;
        $spring = isset($meta['spring']) ? (bool) $meta['spring'] : null;
        $toastDuration = $duration ?? (int) config('goey-toast.default_duration', 4500);
        $toastDismissible = $dismissible ?? (bool) config('goey-toast.dismissible', true);

        unset($meta['title'], $meta['description'], $meta['action'], $meta['spring']);

        $toast = new Toast(
            id: (string) Str::uuid(),
            type: $type,
            message: $message,
            title: $title,
            description: $description,
            action: $action,
            spring: $spring,
            duration: $toastDuration,
            dismissible: $toastDismissible,
            count: 1,
            createdAtMs: $createdAtMs,
            meta: $meta,
        );

        $sessionKey = (string) config('goey-toast.session_key', 'goey_toasts');
        $toasts = $this->session->get($sessionKey, []);

        if (! is_array($toasts)) {
            $toasts = [];
        }

        if ($this->dedupeEnabled()) {
            $duplicateIndex = $this->findDuplicateIndex(
                $toasts,
                $type,
                $message,
                $title,
                $description,
                $action,
                $toastDuration,
                $toastDismissible,
                $createdAtMs
            );

            if ($duplicateIndex !== null) {
                $existing = is_array($toasts[$duplicateIndex]) ? $toasts[$duplicateIndex] : [];
                $existingCount = (int) ($existing['count'] ?? 1);
                $toasts[$duplicateIndex]['count'] = $existingCount + 1;
                $toasts[$duplicateIndex]['createdAtMs'] = $createdAtMs;

                $this->session->flash($sessionKey, $toasts);

                return new Toast(
                    id: (string) ($toasts[$duplicateIndex]['id'] ?? $toast->id),
                    type: (string) ($toasts[$duplicateIndex]['type'] ?? $type),
                    message: (string) ($toasts[$duplicateIndex]['message'] ?? $message),
                    title: is_string($toasts[$duplicateIndex]['title'] ?? null) ? $toasts[$duplicateIndex]['title'] : $title,
                    description: is_string($toasts[$duplicateIndex]['description'] ?? null) ? $toasts[$duplicateIndex]['description'] : $description,
                    action: is_array($toasts[$duplicateIndex]['action'] ?? null) ? $toasts[$duplicateIndex]['action'] : $action,
                    spring: isset($toasts[$duplicateIndex]['spring']) ? (bool) $toasts[$duplicateIndex]['spring'] : $spring,
                    duration: (int) ($toasts[$duplicateIndex]['duration'] ?? $toastDuration),
                    dismissible: (bool) ($toasts[$duplicateIndex]['dismissible'] ?? $toastDismissible),
                    count: (int) ($toasts[$duplicateIndex]['count'] ?? 1),
                    createdAtMs: (int) ($toasts[$duplicateIndex]['createdAtMs'] ?? $createdAtMs),
                    meta: is_array($toasts[$duplicateIndex]['meta'] ?? null) ? $toasts[$duplicateIndex]['meta'] : $meta,
                );
            }
        }

        $toasts[] = $toast->toArray();

        $this->session->flash($sessionKey, $toasts);

        return $toast;
    }

    protected function dedupeEnabled(): bool
    {
        return (bool) config('goey-toast.dedupe.enabled', true);
    }

    protected function dedupeWindowMs(): int
    {
        return max((int) config('goey-toast.dedupe.window_ms', 3000), 0);
    }

    protected function nowMs(): int
    {
        return (int) floor(microtime(true) * 1000);
    }

    /**
     * @param  array<int, mixed>  $toasts
     * @param  array<string, mixed>|null  $action
     */
    protected function findDuplicateIndex(
        array $toasts,
        string $type,
        string $message,
        ?string $title,
        ?string $description,
        ?array $action,
        int $duration,
        bool $dismissible,
        int $nowMs
    ): ?int {
        $windowMs = $this->dedupeWindowMs();

        for ($index = count($toasts) - 1; $index >= 0; $index--) {
            $toast = $toasts[$index];

            if (! is_array($toast)) {
                continue;
            }

            if (($toast['type'] ?? null) !== $type || ($toast['message'] ?? null) !== $message) {
                continue;
            }

            if (($toast['title'] ?? null) !== $title || ($toast['description'] ?? null) !== $description) {
                continue;
            }

            if (($toast['duration'] ?? null) !== $duration || (bool) ($toast['dismissible'] ?? true) !== $dismissible) {
                continue;
            }

            if (($toast['action'] ?? null) !== $action) {
                continue;
            }

            $createdAtMs = (int) ($toast['createdAtMs'] ?? 0);

            if ($windowMs > 0 && $createdAtMs > 0 && ($nowMs - $createdAtMs) > $windowMs) {
                continue;
            }

            return $index;
        }

        return null;
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
