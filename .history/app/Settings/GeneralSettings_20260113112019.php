<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public ?string $site_slogan;
    public bool $site_active;
    public ?string $maintenance_message;
    public ?string $site_logo;
    public ?string $site_favicon;

    // Contact Information
    public string $support_email;
    public ?string $support_phone;
    public ?string $address;
    public ?string $working_days;
    public ?string $working_hours;

    // Social Media Links
    public ?string $facebook_url;
    public ?string $twitter_url;
    public ?string $instagram_url;
    public ?string $linkedin_url;
    public ?string $youtube_url;

    // SEO Settings
    public ?string $seo_title;
    public ?string $seo_description;
    public ?string $seo_keywords;

    // Content - Home Page
    public ?string $home_hero_title;
    public ?string $home_hero_subtitle;
    public ?string $home_hero_image;
    public ?string $home_features_title;
    public ?string $home_features_subtitle;
    public ?array $home_features; // [{icon, title, text}]
    public ?array $home_how_it_works_donor; // [{title, text}]
    public ?array $home_how_it_works_org; // [{title, text}]
    public ?string $home_cta_title;
    public ?string $home_cta_subtitle;

    // Content - About Page
    public ?string $about_hero_title;
    public ?string $about_hero_subtitle;
    public ?string $about_mission_title1;
    public ?string $about_mission_text1;
    public ?string $about_mission_image1;
    public ?string $about_mission_title2;
    public ?string $about_mission_text2;
    public ?string $about_mission_image2;
    public ?string $about_values_title;
    public ?string $about_values_subtitle;
    public ?array $about_values; // [{title, text, image}]
    public ?string $about_team_title;
    public ?string $about_team_subtitle;
    public ?array $about_team_members; // [{name, role, bio, image}]
    public ?string $about_impact_title;
    public ?string $about_impact_text;
    public ?string $about_join_title;
    public ?string $about_join_subtitle;

    // Content - Contact Page
    public ?string $contact_hero_title;
    public ?string $contact_hero_subtitle;
    public ?array $contact_faqs; // [{question, answer}]

    // Content - Auth Pages
    public ?string $login_title;
    public ?string $login_subtitle;
    public ?string $login_image;
    public ?string $signup_title;
    public ?string $signup_subtitle;
    public ?string $signup_donor_title;
    public ?string $signup_donor_subtitle;
    public ?string $signup_donor_image;
    public ?array $signup_donor_tasks;
    public ?string $signup_org_title;
    public ?string $signup_org_subtitle;
    public ?string $signup_org_image;
    public ?array $signup_org_tasks;
    public ?string $donor_register_title;
    public ?string $donor_register_subtitle;
    public ?string $donor_register_image;
    public ?string $org_register_title;
    public ?string $org_register_subtitle;
    public ?string $org_register_image;

    // Donation Rules & Limits
    public ?int $min_donor_age;
    public ?int $max_donor_age;
    public ?int $min_donor_weight;
    public ?int $min_days_between_donations;
    public ?int $org_max_requests_per_day;

    public static function group(): string
    {
        return 'general';
    }
}
