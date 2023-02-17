<?php

namespace Goedemiddag\LaravelMissingTranslations\Comparer;

use Goedemiddag\LaravelMissingTranslations\Language;
use Goedemiddag\LaravelMissingTranslations\MissingTranslation;

class CompareAllLanguages extends Comparer
{
    /**
     * @phpstan-param array<string, Language> $languages
     */
    public function handle(array $languages, array $options): void
    {
        foreach ($languages as $baseLocale) {
            $baseLanguageFiles = $baseLocale->fileNames();

            foreach ($baseLanguageFiles as $languageFile) {
                $baseLanguageFile = $baseLocale->getFile($languageFile);

                foreach ($languages as $language) {
                    /*
                     * Check whether the second language is not actually the base language
                     */
                    if ($baseLocale->identifier === $language->identifier) {
                        continue;
                    }

                    /*
                     * Check if the file exists in the second language
                     */
                    if (!$language->hasFile($languageFile)) {
                        MissingTranslation::file($languageFile, $language->identifier);
                        continue;
                    }

                    $secondLanguageFile = $language->getFile($languageFile);

                    /** @phpstan-ignore-next-line */
                    foreach ($this->arrayDiffRecursive($baseLanguageFile, $secondLanguageFile) as $missingKey) {
                        MissingTranslation::key($languageFile, $missingKey, $language->identifier);
                    }
                }
            }
        }
    }
}
