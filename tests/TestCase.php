<?php

namespace Goedemiddag\LaravelMissingTranslations\Tests;

use Goedemiddag\LaravelMissingTranslations\MissingTranslation;
use Goedemiddag\LaravelMissingTranslations\MissingTranslationsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MissingTranslation::clear();
    }

    protected function getPackageProviders($app): array
    {
        return [MissingTranslationsServiceProvider::class];
    }
}
