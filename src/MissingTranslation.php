<?php

namespace Goedemiddag\LaravelMissingTranslations;

class MissingTranslation
{
    /** @var self[] */
    private static array $missingTranslations = [];

    public static function file(string $file, string $language): self
    {
        $instance = self::$missingTranslations[$file] ?? new self($file);
        $instance->languages[] = $language;

        return self::$missingTranslations[$file] = $instance;
    }

    public static function key(string $file, string $key, string $language): self
    {
        $id = "{$file}({$key})";

        $instance = self::$missingTranslations[$id] ?? new self($file, $key);
        $instance->languages[] = $language;

        return self::$missingTranslations[$id] = $instance;
    }

    public static function isEmpty(): bool
    {
        return empty(self::$missingTranslations);
    }

    public static function isNotEmpty(): bool
    {
        return !self::isEmpty();
    }

    /**
     * @return self[]
     */
    public static function all(): array
    {
        return self::$missingTranslations;
    }

    /**
     * @return self[]
     */
    public static function findAllForLanguage(string $language): array
    {
        return array_filter(
            self::$missingTranslations,
            fn (self $translation) => $translation->isMissingInLanguage($language)
        );
    }

    /**
     * @return string[]
     */
    public static function languages(): array
    {
        return collect(self::$missingTranslations)->flatMap(fn (self $translation) => $translation->languages)->unique(
        )->values()->all();
    }

    /** @var string[] */
    private array $languages = [];

    private function __construct(
        public readonly string $file,
        public readonly ?string $key = null,
    ) {
    }

    public function isMissingInLanguage(string $language): bool
    {
        return in_array($language, $this->languages, true);
    }

    /**
     * @return string[]
     */
    public function missingInLanguages(): array
    {
        return $this->languages;
    }

    public function isEntireFile(): bool
    {
        return !isset($this->key);
    }
}
