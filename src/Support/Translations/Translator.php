<?php

namespace Boil\Support\Translations;

use Illuminate\Support\Collection;

class Translator
{
    /**
     * @var array<string, array<string, string>>
     */
    protected array $translations = [];

    protected string $currentLocale = 'en';

    /**
     * @var array<string, array<string>>
     */
    protected array $translationFiles = [];

    /**
     * @var array<string, array<string>>
     */
    protected array $loadedFiles = [];

    public function queueFile(string $file): void
    {
        if (str_ends_with($file, '.php') || str_ends_with($file, '.json')) {
            $this->translationFiles[$this->normalizeLocale(pathinfo($file, PATHINFO_FILENAME))][] = $file;
        }
    }

    public function loadFile(string $file, string $locale): void
    {
        if (! file_exists($file)) {
            return;
        }

        if (str_ends_with($file, '.json')) {
            $this->translations[$locale] = array_merge(
                $this->translations[$locale] ?? [],
                json_decode(file_get_contents($file) ?: '', true)
            );

            return;
        }

        if (str_ends_with($file, '.php')) {
            $this->translations[$locale] = array_merge($this->translations[$locale] ?? [], include $file);
        }
    }

    public function loadFromPath(string $path): void
    {
        (new Collection(scandir($path) ?: []))
            ->filter(fn ($file) => str_ends_with($file, '.php') || str_ends_with($file, '.json'))
            ->each(fn ($file) => $this->queueFile(implode(DIRECTORY_SEPARATOR, [$path, $file])));
    }

    public function setTranslation(string $locale, string $original, string $translation): void
    {
        $this->translations[$this->normalizeLocale($locale)][$original] = $translation;
    }

    public function setLocale(string $locale): void
    {
        $this->currentLocale = $this->normalizeLocale($locale);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function get(string $string, array $attributes = []): string
    {
        return $this->translate($string, $attributes);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function translate(string $string, array $attributes = []): string
    {
        $this->load();

        $translated = $this->translations[$this->normalizeLocale($this->currentLocale)][$string] ?? null;

        if ($translated) {
            $shouldReplace = [];

            foreach ($attributes as $key => $value) {
                $shouldReplace[":{$key}"] = $this->translate($value);
            }

            return strtr($translated, $shouldReplace);
        }

        return $string;
    }

    protected function normalizeLocale(string $locale): string
    {
        [$locale] = preg_split('/-|_/', $locale) ?: [null];

        if (empty($locale)) {
            throw new \InvalidArgumentException('Could not determine locale');
        }

        return $locale;
    }

    protected function load(): void
    {
        if (count($this->loadedFiles[$this->currentLocale] ?? []) === count($this->translationFiles[$this->currentLocale] ?? [])) {
            return;
        }

        foreach ($this->translationFiles[$this->currentLocale] ?? [] as $file) {
            $this->loadFile($file, $this->currentLocale);
            $this->loadedFiles[$this->currentLocale][] = $file;
        }
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function __invoke(string $string, array $attributes = []): string
    {
        return $this->translate($string, $attributes);
    }
}
