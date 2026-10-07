<?php

declare(strict_types=1);

test('password visibility can be toggled on the login screen', function (): void {
    visit(route('login', absolute: false))
        ->assertAttribute('#password', 'type', 'password')
        ->click('[aria-label="Show password"]')
        ->assertAttribute('#password', 'type', 'text')
        ->click('[aria-label="Hide password"]')
        ->assertAttribute('#password', 'type', 'password')
        ->assertNoJavaScriptErrors();
});
