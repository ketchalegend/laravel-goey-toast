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
```

Helper function:

```php
goey_toast('Saved');
goey_toast('Invalid token', 'danger');
```

Fire toast events from JavaScript:

```js
window.goeyToast('Build completed', { type: 'success', duration: 5000 });
```

Dispatch from Livewire with browser events:

```php
$this->dispatch('goey-toast', [
    'message' => 'Data synced',
    'type' => 'success',
]);
```

For Livewire actions, browser dispatch is the recommended path for immediate in-place UI updates.
