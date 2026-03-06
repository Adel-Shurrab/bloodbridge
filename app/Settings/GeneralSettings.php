<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name = 'BloodBridge';
    public ?string $site_slogan = null;
    public bool $maintenance_mode = false;
    public ?string $maintenance_message = null;
    public ?string $site_logo = null;
    public ?string $site_favicon = null;

    public string $support_email = 'info@bloodbridge.ps';
    public ?string $support_phone = null;
    public ?string $address = null;
    public ?string $working_days = null;
    public ?string $working_hours = null;

    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $instagram_url = null;
    public ?string $linkedin_url = null;
    public ?string $youtube_url = null;

    public ?string $seo_title = null;
    public ?string $seo_description = null;
    public ?string $seo_keywords = null;

    public ?string $home_hero_title = null;
    public ?string $home_hero_subtitle = null;
    public ?string $home_hero_image = null;
    public ?string $home_features_title = null;
    public ?string $home_features_subtitle = null;
    public ?array $home_features = [];
    public ?array $home_how_it_works_donor = [];
    public ?array $home_how_it_works_org = [];
    public ?string $home_cta_title = null;
    public ?string $home_cta_subtitle = null;

    public ?string $about_hero_title = null;
    public ?string $about_hero_subtitle = null;
    public ?string $about_mission_title1 = null;
    public ?string $about_mission_text1 = null;
    public ?string $about_mission_image1 = null;
    public ?string $about_mission_title2 = null;
    public ?string $about_mission_text2 = null;
    public ?string $about_mission_image2 = null;
    public ?string $about_values_title = null;
    public ?string $about_values_subtitle = null;
    public ?array $about_values = [];
    public ?string $about_team_title = null;
    public ?string $about_team_subtitle = null;
    public ?array $about_team_members = [];
    public ?string $about_impact_title = null;
    public ?string $about_impact_text = null;
    public ?string $about_join_title = null;
    public ?string $about_join_subtitle = null;

    public ?string $contact_hero_title = null;
    public ?string $contact_hero_subtitle = null;
    public ?array $contact_faqs = [];
    public bool $enable_contact_messages = true;

    public ?string $login_title = null;
    public ?string $login_subtitle = null;
    public ?string $login_image = null;
    public ?string $signup_title = null;
    public ?string $signup_subtitle = null;
    public ?string $signup_donor_title = null;
    public ?string $signup_donor_subtitle = null;
    public ?string $signup_donor_image = null;
    public ?array $signup_donor_tasks = [];
    public ?string $signup_org_title = null;
    public ?string $signup_org_subtitle = null;
    public ?string $signup_org_image = null;
    public ?array $signup_org_tasks = [];
    public ?string $donor_register_title = null;
    public ?string $donor_register_subtitle = null;
    public ?string $donor_register_image = null;
    public ?string $org_register_title = null;
    public ?string $org_register_subtitle = null;
    public ?string $org_register_image = null;

    public int $min_donor_age = 18;
    public int $max_donor_age = 65;
    public int $min_donor_weight = 50;
    public int $min_days_between_donations = 56;
    public int $min_donor_height = 140;
    public int $min_days_after_surgery = 28;

    public int $org_max_requests_per_day = 5;

    public static function group(): string
    {
        return 'general';
    }
}
