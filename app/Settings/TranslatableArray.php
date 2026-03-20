<?php

namespace App\Settings;

use Livewire\Wireable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use Countable;
use ArrayAccess;
use Stringable;
use JsonSerializable;
use ArrayIterator;
use Traversable;

/**
 * Value object that satisfies both Filament (as an ArrayAccess object) and Blade (as a string).
 * Refactored to avoid ArrayObject inheritance to prevent Livewire reflection issues.
 */
class TranslatableArray implements ArrayAccess, IteratorAggregate, Countable, Stringable, JsonSerializable, Wireable, Arrayable, Jsonable
{
    protected array $data = [];

    public function __construct(array|string|null $data = [])
    {
        if (is_string($data)) {
            $this->data = json_decode($data, true) ?? [];
        } elseif ($data instanceof self) {
            $this->data = $data->toArray();
        } else {
            $this->data = (array) $data;
        }
    }

    public function toLivewire()
    {
        return $this->data;
    }

    public static function fromLivewire($value)
    {
        return new static($value);
    }

    public function get(?string $locale = null, bool $fallback = true): string
    {
        $locale = $locale ?: app()->getLocale();

        if (isset($this->data[$locale]) && $this->data[$locale] !== '') {
            $value = $this->data[$locale];
            return is_array($value) ? json_encode($value) : (string) $value;
        }

        if ($fallback) {
            $fallbackLocale = config('app.fallback_locale', 'en');
            $value = $this->data[$fallbackLocale] ?? (reset($this->data) ?: '');
            return is_array($value) ? json_encode($value) : (string) $value;
        }

        return '';
    }

    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Used by Blade when iterating over the object (e.g. for Repeaters).
     */
    public function getIterator(): Traversable
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        $current = $this->data[$locale] ?? $this->data[$fallback] ?? [];
        return new ArrayIterator(is_array($current) ? $current : []);
    }

    /**
     * Used by Blade when calling count() on the object.
     */
    public function count(): int
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        $current = $this->data[$locale] ?? $this->data[$fallback] ?? [];
        return is_array($current) ? count($current) : 0;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    // ArrayAccess Implementation
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    // Magic methods for property access (site_name->ar)
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}
