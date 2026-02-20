<?php

declare(strict_types=1);

namespace Ketchalegend\LaravelGoeyToast\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GoeyToastStack extends Component
{
    /**
     * @return array<int, array{id: string, type: string, message: string, duration: int, dismissible: bool, meta: array<string, mixed>}>
     */
    public function toasts(): array
    {
        $toasts = session((string) config('goey-toast.session_key', 'goey_toasts'), []);

        if (! is_array($toasts)) {
            $toasts = [];
        }

        $legacyToasts = $this->legacyFlashedToasts();

        return array_values(array_filter(
            [...$toasts, ...$legacyToasts],
            static fn (mixed $toast): bool => is_array($toast)
        ));
    }

    /**
     * @return array<int, array{id: string, type: string, message: string, duration: int, dismissible: bool, meta: array<string, mixed>}>
     */
    protected function legacyFlashedToasts(): array
    {
        $flashKeys = array_unique(array_filter([
            ...session()->get('_flash.new', []),
            ...session()->get('_flash.old', []),
        ], static fn (mixed $key): bool => is_string($key)));

        $messages = [];

        foreach ($flashKeys as $flashKey) {
            if ($flashKey === (string) config('goey-toast.session_key', 'goey_toasts')) {
                continue;
            }

            $value = session($flashKey);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $messages[] = [
                'id' => 'legacy-'.md5($flashKey.'-'.$value),
                'type' => $this->typeFromFlashKey($flashKey),
                'message' => $value,
                'duration' => (int) config('goey-toast.default_duration', 4500),
                'dismissible' => (bool) config('goey-toast.dismissible', true),
                'meta' => [
                    'source' => 'legacy-flash',
                    'key' => $flashKey,
                ],
            ];
        }

        return $messages;
    }

    protected function typeFromFlashKey(string $flashKey): string
    {
        $normalizedKey = strtolower($flashKey);

        if (str_contains($normalizedKey, 'error')) {
            return 'danger';
        }

        if (str_contains($normalizedKey, 'warning')) {
            return 'warning';
        }

        if (str_contains($normalizedKey, 'status')) {
            return 'info';
        }

        return 'success';
    }

    public function render(): View
    {
        return view('goey-toast::components.stack', [
            'toasts' => $this->toasts(),
            'position' => (string) config('goey-toast.position', 'top-right'),
            'maxVisible' => (int) config('goey-toast.max_visible', 4),
            'zIndex' => (int) config('goey-toast.z_index', 9999),
        ]);
    }
}
