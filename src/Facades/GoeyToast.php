<?php

declare(strict_types=1);

namespace Ketchalegend\LaravelGoeyToast\Facades;

use Illuminate\Support\Facades\Facade;
use Ketchalegend\LaravelGoeyToast\GoeyToastManager;

/**
 * @method static \Ketchalegend\LaravelGoeyToast\Toast push(string $message, string $type = 'info', ?int $duration = null, ?bool $dismissible = null, array $meta = [])
 * @method static \Ketchalegend\LaravelGoeyToast\Toast success(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = [])
 * @method static \Ketchalegend\LaravelGoeyToast\Toast info(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = [])
 * @method static \Ketchalegend\LaravelGoeyToast\Toast warning(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = [])
 * @method static \Ketchalegend\LaravelGoeyToast\Toast error(string $message, ?int $duration = null, ?bool $dismissible = null, array $meta = [])
 */
class GoeyToast extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GoeyToastManager::class;
    }
}
