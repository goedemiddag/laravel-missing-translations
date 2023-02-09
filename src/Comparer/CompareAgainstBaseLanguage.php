<?php

namespace Goedemiddag\LaravelMissingTranslations\Comparer;

use Goedemiddag\LaravelMissingTranslations\Language;
use Goedemiddag\LaravelMissingTranslations\MissingTranslation;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CompareAgainstBaseLanguage extends Comparer
{
    public function configureCommand(Command $command): void
    {
        $command->addOption(
            name: '--base',
            mode: InputOption::VALUE_OPTIONAL,
            description: 'Base locale, e.g. "en". All other locales are compared to this locale.'
        );
    }

    /**
     * @phpstan-param array<string, Language> $languages
     */
    public function handle(array $languages, array $options): void
    {
        $baseLocale = $languages[$options['base'] ?? config('app.locale')];

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
