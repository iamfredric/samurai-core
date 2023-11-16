<?php

namespace Samurai\Routing;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomRoute
{
    /**
     * @var string|string[]|Closure|null
     */
    protected string|array|Closure|null $callback;

    /**
     * @param  string|string[]|callable|null  $callback
     */
    public function __construct(
        protected string $endpoint,
        string|array|callable|null $callback,
    ) {
        $this->callback = is_callable($callback) ? $callback(...) : $callback;
    }

    public function id(): string
    {
        return Str::slug($this->endpoint);
    }

    public function getQuery(): string
    {
        $arguments = [
            'pagename='.$this->id(),
        ];

        $number = 1;

        $query = (new Collection(explode('/', $this->endpoint)))
            ->skip(1);

        foreach ($query as $key) {
            if (str_starts_with($key, '{')) {
                $key = str_replace(['{', '}'], '', $key);
                $arguments[] = $key.'='.'$matches['.$number.']';
                $number++;
            } else {
                $arguments[] = $key;
            }
        }

        return 'index.php?'.implode('&', $arguments);
    }

    public function getQueryVars(): mixed
    {
        return (new Collection(explode('/', $this->endpoint)))
            ->filter(fn ($part) => str_starts_with($part, '{'))
            ->map(fn ($part) => str_replace(['{', '}'], '', $part));
    }

    public function getRegex(): string
    {
        $endpoint = (new Collection(explode('/', $this->endpoint)))
            ->map(fn ($part) => str_starts_with($part, '{') ? '([a-z0-9\-]+)' : $part)
            ->implode('/');

        return "^{$endpoint}";
    }

    /** @return string|string[]|Closure */
    public function getCallable(): array|string|Closure
    {
        if ($this->callback instanceof Closure) {
            return $this->callback;
        }

        $callback = is_array($this->callback) ? $this->callback : explode('@', $this->callback ?: '');

        return [$callback[0], $callback[1] ?? '__invoke'];
    }
}
