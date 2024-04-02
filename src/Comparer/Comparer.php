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
     */
    abstract public function handle(array $languages, array $options): void;

    public function configureCommand(Command $command): void
    {
        //
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param string[] $prefix
     * @return Generator<string>
     */
    protected function arrayDiffRecursive(array $firstArray, array $secondArray, array $prefix = []): Generator
    {
        foreach ($firstArray as $key => $value) {
            if (array_key_exists($key, $secondArray)) {
                if (is_array($value) && !array_is_list($value)) {
                    $secondValue = is_array($secondArray[$key]) ? $secondArray[$key] : [];
                    yield from $this->arrayDiffRecursive($value, $secondValue, [...$prefix, $key]);
                }
            } else {
                yield implode(' > ', [...$prefix, $key]);
            }
        }
    }
}
