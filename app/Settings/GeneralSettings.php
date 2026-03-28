<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public mixed $site_name = [];
    public mixed $site_slogan = [];
    public bool $maintenance_mode = false;
    public mixed $maintenance_message = [];
    public ?string $site_logo = null;
    public ?string $site_favicon = null;

    public string $support_email = 'info@bloodbridge.ps';
    public ?string $support_phone = null;
    public mixed $address = [];
    public mixed $working_days = [];
    public mixed $working_hours = [];

    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $instagram_url = null;
    public ?string $linkedin_url = null;
    public ?string $youtube_url = null;

    public mixed $seo_title = [];
    public mixed $seo_description = [];
    public mixed $seo_keywords = [];

    public mixed $home_hero_title = [];
    public mixed $home_hero_subtitle = [];
    public ?string $home_hero_image = null;
    public mixed $home_features_title = [];
    public mixed $home_features_subtitle = [];
    public mixed $home_features = [];
    public mixed $home_how_it_works_donor = [];
    public mixed $home_how_it_works_org = [];
    public mixed $home_cta_title = [];
    public mixed $home_cta_subtitle = [];

    public mixed $about_hero_title = [];
    public mixed $about_hero_subtitle = [];
    public mixed $about_mission_title1 = [];
    public mixed $about_mission_text1 = [];
    public ?string $about_mission_image1 = null;
    public mixed $about_mission_title2 = [];
    public mixed $about_mission_text2 = [];
    public ?string $about_mission_image2 = null;
    public mixed $about_values_title = [];
    public mixed $about_values_subtitle = [];
    public mixed $about_values = [];
    public mixed $about_team_title = [];
    public mixed $about_team_subtitle = [];
    public mixed $about_team_members = [];
    public mixed $about_impact_title = [];
    public mixed $about_impact_text = [];
    public mixed $about_join_title = [];
    public mixed $about_join_subtitle = [];

    public mixed $contact_hero_title = [];
    public mixed $contact_hero_subtitle = [];
    public mixed $contact_faqs = [];
    public bool $enable_contact_messages = true;

    public mixed $login_title = [];
    public mixed $login_subtitle = [];
    public ?string $login_image = null;
    public mixed $signup_title = [];
    public mixed $signup_subtitle = [];
    public mixed $signup_donor_title = [];
    public mixed $signup_donor_subtitle = [];
    public ?string $signup_donor_image = null;
    public mixed $signup_donor_tasks = [];
    public mixed $signup_org_title = [];
    public mixed $signup_org_subtitle = [];
    public ?string $signup_org_image = null;
    public mixed $signup_org_tasks = [];
    public mixed $donor_register_title = [];
    public mixed $donor_register_subtitle = [];
    public ?string $donor_register_image = null;
    public mixed $org_register_title = [];
    public mixed $org_register_subtitle = [];
    public ?string $org_register_image = null;

    public int $min_donor_age = 18;
    public int $max_donor_age = 65;
    public int $min_donor_weight = 50;
    public int $min_days_between_donations = 56;
    public int $min_donor_height = 140;
    public int $min_days_after_surgery = 28;

    public int $org_max_requests_per_day = 5;

    // Map coordinates for Palestine regions
    public float $map_default_lat = 31.5;
    public float $map_default_lng = 34.4667;
    public float $map_lat_min = 31.2;
    public float $map_lat_max = 32.6;
    public float $map_lng_min = 34.2;
    public float $map_lng_max = 35.6;
    public int $map_zoom_region = 10;
    public int $map_zoom_city = 13;

    protected static array $translatableFields = [
        'site_name',
        'site_slogan',
        'maintenance_message',
        'address',
        'working_days',
        'working_hours',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'home_hero_title',
        'home_hero_subtitle',
        'home_features_title',
        'home_features_subtitle',
        'home_cta_title',
        'home_cta_subtitle',
        'about_hero_title',
        'about_hero_subtitle',
        'about_mission_title1',
        'about_mission_text1',
        'about_mission_title2',
        'about_mission_text2',
        'about_values_title',
        'about_values_subtitle',
        'about_team_title',
        'about_team_subtitle',
        'about_impact_title',
        'about_impact_text',
        'about_join_title',
        'about_join_subtitle',
        'contact_hero_title',
        'contact_hero_subtitle',
        'login_title',
        'login_subtitle',
        'signup_title',
        'signup_subtitle',
        'signup_donor_title',
        'signup_donor_subtitle',
        'signup_org_title',
        'signup_org_subtitle',
        'donor_register_title',
        'donor_register_subtitle',
        'org_register_title',
        'org_register_subtitle',
        'home_features',
        'home_how_it_works_donor',
        'home_how_it_works_org',
        'about_values',
        'about_team_members',
        'contact_faqs',
        'signup_donor_tasks',
        'signup_org_tasks',
    ];

    /**
     * Override toArray to ensure plain arrays are returned for translatable fields.
     * This prevents Livewire/Filament from trying to reflect on TranslatableArray objects
     * within the form state, avoiding "ReflectionException: Property ...::$ar does not exist".
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $translatable = static::$translatableFields;

        foreach ($data as $key => $value) {
            if (in_array($key, $translatable)) {
                if ($value instanceof TranslatableArray) {
                    $data[$key] = $value->toArray();
                } elseif (is_object($value) && method_exists($value, 'toArray')) {
                    $data[$key] = $value->toArray();
                }
            }
        }

        return $data;
    }

    public array $translatable = []; // Keep for backward compatibility if needed, but we use static now

    public static function group(): string
    {
        return 'general';
    }

    public static function casts(): array
    {
        return array_fill_keys(static::$translatableFields, TranslatableArrayCast::class);
    }

    /**
     * Get the translated value of a property.
     */
    public function getTranslation(string $name, ?string $locale = null, bool $fallback = true): mixed
    {
        $value = $this->{$name} ?? null;
        $isListField = in_array($name, static::$listFields);

        if ($value instanceof TranslatableArray) {
            if ($isListField) {
                $raw = $value->toArray();
                $locale = $locale ?: app()->getLocale();
                $fallbackLocale = config('app.fallback_locale', 'en');
                return $raw[$locale] ?? ($fallback ? ($raw[$fallbackLocale] ?? []) : []);
            }
            return $value->get($locale, $fallback);
        }

        if (is_array($value)) {
            $locale = $locale ?: app()->getLocale();
            if (isset($value[$locale]) && !empty($value[$locale])) {
                return $isListField ? $value[$locale] : (string) $value[$locale];
            }
            if ($fallback) {
                $fallbackLocale = config('app.fallback_locale', 'en');
                $fallbackValue = $value[$fallbackLocale] ?? (reset($value) ?: ($isListField ? [] : ''));
                if ($isListField) {
                    return is_array($fallbackValue) ? $fallbackValue : [];
                }
                return is_array($fallbackValue) ? json_encode($fallbackValue) : (string) $fallbackValue;
            }
            return $isListField ? [] : '';
        }

        return $isListField ? [] : (string) $value;
    }

    /**
     * Magic getter — when accessing a translatable scalar field directly (e.g. $settings->site_name),
     * resolve it to a string for the current locale. List/repeater fields (home_features, etc.)
     * are returned as iterable TranslatableArray so Blade foreach still works via getIterator().
     */
    protected static array $listFields = [
        'home_features',
        'home_how_it_works_donor',
        'home_how_it_works_org',
        'about_values',
        'about_team_members',
        'contact_faqs',
        'signup_donor_tasks',
        'signup_org_tasks',
    ];

    public function __get($name)
    {
        $value = parent::__get($name);

        if ($value instanceof TranslatableArray && in_array($name, static::$translatableFields)) {
            if (in_array($name, static::$listFields)) {
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale', 'en');
                $raw = $value->toArray();
                return $raw[$locale] ?? $raw[$fallback] ?? [];
            }
            return $value->get();
        }

        return $value;
    }
}
