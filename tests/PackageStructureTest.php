<?php

declare(strict_types=1);

it('does not register the blade component twice in the service provider', function (): void {
    $providerFile = file_get_contents(__DIR__.'/../src/GoeyToastServiceProvider.php');

    expect($providerFile)->toBeString()
        ->not->toContain('Blade::component(');
});

it('includes accessibility attributes for toast announcements', function (): void {
    $stackView = file_get_contents(__DIR__.'/../resources/views/components/stack.blade.php');

    expect($stackView)->toBeString()
        ->toContain("x-bind:role=\"toast.type === 'danger' ? 'alert' : 'status'\"")
        ->toContain("x-bind:aria-live=\"toast.type === 'danger' ? 'assertive' : 'polite'\"")
        ->toContain('aria-atomic="true"');
});

it('includes reduced motion styles for users with motion preferences', function (): void {
    $stackView = file_get_contents(__DIR__.'/../resources/views/components/stack.blade.php');

    expect($stackView)->toBeString()
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('.goey-toast__timer');
});
