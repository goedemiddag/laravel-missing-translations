<?php

namespace Goedemiddag\LaravelMissingTranslations;

use Illuminate\Support\Facades\File;
use SplFileInfo;

class Language
{
    public readonly string $identifier;

    public function __construct(
        public readonly string $directory
    ) {
        $this->identifier = basename($this->directory);
    }

    /**
     * @return string[]
     */
    public function fileNames(): array
    {
        return array_map(
            fn (SplFileInfo $fileInfo) => $fileInfo->getFilename(),
            File::files($this->directory)
        );
    }

    public function hasFile(string $file): bool
    {
        return File::exists("{$this->directory}/{$file}");
    }


    public function getFile(string $file): mixed
    {
        return File::getRequire("{$this->directory}/{$file}");
    }
}
