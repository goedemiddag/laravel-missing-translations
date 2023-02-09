<?php

namespace Goedemiddag\LaravelMissingTranslations;

use Goedemiddag\LaravelMissingTranslations\Comparer\Comparer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class FindMissingTranslationsCommand extends Command
{
    protected $signature = 'lang:missing {--dir= : Relative path of lang directory, e.g. "/lang", a directory that contains all supported locales}';

    protected $description = 'Find missing translations';

    public function __construct(
        private readonly Comparer $comparer
    ) {
        parent::__construct();
        $this->comparer->configureCommand($this);
    }

    public function handle(): int
    {
        $languagePath = $this->getPath();

        $localeDirectories = File::directories($languagePath);

        $languages = collect($localeDirectories)
            ->mapInto(Language::class)
            ->keyBy('identifier')
            ->all();

        $this->comparer->handle($languages, $this->options());

        if (MissingTranslation::isNotEmpty()) {
            $languages = MissingTranslation::languages();

            $this->table(
                ['File', 'Key', ...$languages],
                array_map(function (MissingTranslation $translation) use ($languages) {
                    return [
                        $translation->file,
                        $translation->key,
                        ...array_map(function (string $language) use ($translation) {
                            return $translation->isMissingInLanguage($language) ? '<error> X </error>' : '';
                        }, $languages),
                    ];
                }, MissingTranslation::all())
            );

            return self::FAILURE;
        }

        $this->line('[ <fg=green>OK</> ] No missing translations!');

        return self::SUCCESS;
    }

    private function getPath(): string
    {
        if ($this->option('dir') === null) {
            return lang_path();
        }

        assert(is_string($this->option('dir')), 'Directory is not a string');

        if (File::isDirectory($this->option('dir'))) {
            return $this->option('dir');
        }

        if (File::isDirectory(base_path($this->option('dir')))) {
            return base_path($this->option('dir'));
        }

        throw new InvalidArgumentException("Specified language directory {$this->option('dir')} does not exist.");
    }
}
