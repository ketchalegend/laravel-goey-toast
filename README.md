# Laravel Goey Toast

Gooey animated toast notifications for Laravel + Livewire.

## Install

```bash
composer require ketchalegend/laravel-goey-toast
```

Publish config if needed:

```bash
php artisan vendor:publish --tag="goey-toast-config"
```

## Usage

Add the toast stack component to your layout before `</body>`:

```blade
<x-goey-toast-stack />
```

Flash toasts from PHP:

```php
use Ketchalegend\LaravelGoeyToast\Facades\GoeyToast;

GoeyToast::success('Profile updated');
GoeyToast::error('Something went wrong');

GoeyToast::success('Saved', meta: [
    'title' => 'Changes saved',
    'description' => 'Your changes were synced successfully.',
    'spring' => true,
    'action' => [
        'label' => 'Open',
        'href' => '/dashboard',
    ],
]);
```

Helper function:

```php
goey_toast('Saved');
goey_toast('Invalid token', 'danger');
```

Fire toast events from JavaScript:

```js
window.goeyToast('Build completed', { type: 'success', duration: 5000 });
window.goeyToast('Saved', {
  title: 'Changes saved',
  description: 'Everything is synced.',
  spring: true,
  action: { label: 'Undo', event: 'undo-last-change', dismissOnClick: true }
});
```

Promise lifecycle helper:

```js
await window.goeyToast.promise(
  () => fetch('/api/sync').then((r) => r.json()),
  {
    loading: { message: 'Syncing...', type: 'info' },
    success: { message: 'Sync complete', type: 'success' },
    error: (err) => ({ message: err?.message ?? 'Sync failed', type: 'danger' }),
  }
);
```

Dispatch from Livewire with browser events:

```php
$this->dispatch('goey-toast', [
    'message' => 'Data synced',
    'type' => 'success',
]);
```

For Livewire actions, browser dispatch is the recommended path for immediate in-place UI updates.

## Config

```php
return [
    'position' => 'top-right', // top-left, top-center, top-right, bottom-left, bottom-center, bottom-right
    'default_duration' => 4500,
    'max_visible' => 4,
    'dismissible' => true,
    'dedupe' => [
        'enabled' => true,
        'window_ms' => 3000,
    ],
    'animation' => [
        'spring_enabled' => true,
        'enter_duration' => 460,
        'leave_duration' => 230,
        'spring_curve' => 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'smooth_curve' => 'cubic-bezier(0.4, 0, 0.2, 1)',
        'start_offset' => 14,
        'start_scale' => 0.92,
    ],
];
```

Per-toast overrides:
- `spring` (`true|false`) to enable/disable spring effect on a toast.
- `title` and `description` for richer content.
- `action` with `label`, optional `href`, optional `event`, optional `payload`, optional `dismissOnClick`.
- Duplicate toasts inside the dedupe window are automatically grouped and display a `×N` badge.
