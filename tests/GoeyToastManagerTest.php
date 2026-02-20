<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Ketchalegend\LaravelGoeyToast\GoeyToastManager;

require_once __DIR__.'/../src/Toast.php';
require_once __DIR__.'/../src/GoeyToastManager.php';

function setGoeyToastConfig(): void
{
    $container = new Container;

    $container->instance('config', new Repository([
        'goey-toast' => [
            'session_key' => 'goey_toasts',
            'default_duration' => 4500,
            'dismissible' => true,
            'dedupe' => [
                'enabled' => true,
                'window_ms' => 3000,
            ],
        ],
    ]));

    Container::setInstance($container);
}

it('pushes a structured toast into flashed session state', function (): void {
    setGoeyToastConfig();

    $session = new Store('testing', new ArraySessionHandler(120));
    $session->start();

    $manager = new GoeyToastManager($session);

    $toast = $manager->push('Saved successfully', 'success', null, null, [
        'title' => 'Saved',
        'description' => 'Your changes are now synced.',
        'action' => [
            'label' => 'Open',
            'href' => '/dashboard',
        ],
        'spring' => false,
        'source' => 'feature-test',
    ]);

    expect($toast->type)->toBe('success')
        ->and($toast->title)->toBe('Saved')
        ->and($toast->description)->toBe('Your changes are now synced.')
        ->and($toast->spring)->toBeFalse()
        ->and($toast->duration)->toBe(4500)
        ->and($toast->dismissible)->toBeTrue()
        ->and($toast->meta)->toBe(['source' => 'feature-test']);

    $flashedToasts = $session->get('goey_toasts');

    expect($flashedToasts)->toBeArray()->toHaveCount(1)
        ->and($flashedToasts[0]['type'])->toBe('success')
        ->and($flashedToasts[0]['message'])->toBe('Saved successfully')
        ->and($flashedToasts[0]['meta'])->toBe(['source' => 'feature-test']);
});

it('maps error toasts to the danger type', function (): void {
    setGoeyToastConfig();

    $session = new Store('testing', new ArraySessionHandler(120));
    $session->start();

    $manager = new GoeyToastManager($session);
    $toast = $manager->error('Something failed');

    expect($toast->type)->toBe('danger');
});

it('groups duplicate toasts when dedupe is enabled', function (): void {
    setGoeyToastConfig();

    $session = new Store('testing', new ArraySessionHandler(120));
    $session->start();

    $manager = new GoeyToastManager($session);

    $manager->success('Saved');
    $secondToast = $manager->success('Saved');

    $flashedToasts = $session->get('goey_toasts');

    expect($flashedToasts)->toBeArray()->toHaveCount(1)
        ->and($flashedToasts[0]['count'])->toBe(2)
        ->and($secondToast->count)->toBe(2);
});

it('does not group duplicate toasts when dedupe is disabled', function (): void {
    $container = new Container;

    $container->instance('config', new Repository([
        'goey-toast' => [
            'session_key' => 'goey_toasts',
            'default_duration' => 4500,
            'dismissible' => true,
            'dedupe' => [
                'enabled' => false,
                'window_ms' => 3000,
            ],
        ],
    ]));

    Container::setInstance($container);

    $session = new Store('testing', new ArraySessionHandler(120));
    $session->start();

    $manager = new GoeyToastManager($session);

    $manager->success('Saved');
    $manager->success('Saved');

    $flashedToasts = $session->get('goey_toasts');

    expect($flashedToasts)->toBeArray()->toHaveCount(2);
});
