<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Governorate extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public array $translatable = ['name'];

    public function getLocalizedNameAttribute(): ?string
    {
        return $this->getTranslation('name', app()->getLocale(), false)
            ?: $this->getTranslation('name', 'ar', false)
            ?: $this->getTranslation('name', 'en', false);
    }

    public static function localizedOptions(): array
    {
        return static::query()
            ->get()
            ->mapWithKeys(fn (self $governorate) => [$governorate->id => $governorate->localized_name])
            ->toArray();
    }

    public function donors()
    {
        return $this->hasMany(Donor::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
