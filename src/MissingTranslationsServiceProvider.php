<?php

namespace Goedemiddag\LaravelMissingTranslations;

use Goedemiddag\LaravelMissingTranslations\Comparer\Comparer;
use Illuminate\Support\ServiceProvider;

class MissingTranslationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lang.php', 'lang');

        $this->app->bind(Comparer::class, function (): Comparer {
            /** @var class-string<Comparer> $instance */
            $instance = config('lang.missing.comparer');

            return new $instance;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/lang.php' => config_path('lang.php'),
        ]);

        $this->commands([
            FindMissingTranslationsCommand::class,
        ]);
    }
}
