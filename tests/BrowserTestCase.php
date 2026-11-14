<?php

declare(strict_types=1);

namespace Tests;

/**
 * Base case for browser tests. Re-enables Vite in `setUp()` rather than a
 * path-scoped `beforeEach`, so `pest --agent` snippets get it too.
 */
abstract class BrowserTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The browser needs the built assets TestCase disables.
        $this->withVite();
    }
}
