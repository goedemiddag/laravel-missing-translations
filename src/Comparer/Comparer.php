<?php

namespace Goedemiddag\LaravelMissingTranslations\Comparer;

use Generator;
use Goedemiddag\LaravelMissingTranslations\Language;
use Illuminate\Console\Command;

abstract class Comparer
{
    /**
     * @param array<string, Language> $languages
     * @param array<string, mixed> $options
     * @return void
     */
    abstract public function handle(array $languages, array $options): void;

    public function configureCommand(Command $command): void
    {
        //
    }

    /**
     * @phpstan-ignore-next-line
     * @param array $firstArray
     * @param array $secondArray
     * @param string[] $prefix
     * @return Generator<string>
     */
    protected function arrayDiffRecursive(array $firstArray, array $secondArray, array $prefix = []): Generator
    {
        foreach ($firstArray as $key => $value) {
            if (array_key_exists($key, $secondArray)) {
                if (is_array($value) && !array_is_list($value)) {
                    yield from $this->arrayDiffRecursive($value, $secondArray[$key], [...$prefix, $key]);
                }
            } else {
                yield implode(' > ', [...$prefix, $key]);
            }
        }
    }
}
