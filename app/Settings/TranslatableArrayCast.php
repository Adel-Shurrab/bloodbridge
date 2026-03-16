<?php

namespace App\Settings;

use Spatie\LaravelSettings\SettingsCasts\SettingsCast;

/**
 * Cast for Spatie Laravel Settings to handle TranslatableArray.
 */
class TranslatableArrayCast implements SettingsCast
{
    public function get($payload): mixed
    {
        return new TranslatableArray($payload);
    }
    public function set($payload): mixed
    {
        return $payload instanceof TranslatableArray ? $payload->toArray() : $payload;
    }
}
