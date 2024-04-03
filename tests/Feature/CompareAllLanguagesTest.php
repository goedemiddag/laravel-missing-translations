<?php

namespace Goedemiddag\LaravelMissingTranslations\Tests\Feature;

use Goedemiddag\LaravelMissingTranslations\Tests\TestCase;

class CompareAllLanguagesTest extends TestCase
{
    public function test_missing()
    {
        $command = $this->artisan('lang:missing', [
            '--dir' => __DIR__ . '/../lang/missing-nl',
        ]);

        $command->expectsTable([
            'File', 'Key', 'en', 'nl',
        ], [
            ['general.php', 'bye', '<bg=green>   </>', '<error> X </error>'],
        ]);

        $command->assertFailed();
    }

    public function test_complete()
    {
        $command = $this->artisan('lang:missing', [
            '--dir' => __DIR__ . '/../lang/complete-nl',
        ]);

        $command->expectsOutputToContain('No missing translations!');

        $command->assertSuccessful();
    }
}
