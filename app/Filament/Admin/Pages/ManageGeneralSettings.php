<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.manage-general-settings.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system-reports');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('filament.pages.manage-general-settings.title');
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.pages.manage-general-settings.description');
    }

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('System Rules')
                            ->label(__('System Rules'))
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Section::make(__('Donor Eligibility'))
                                    ->schema([
                                        TextInput::make('min_donor_age')->label(__('Min Donor Age (Years)'))->numeric()->required(),
                                        TextInput::make('max_donor_age')->label(__('Max Donor Age (Years)'))->numeric()->required(),
                                        TextInput::make('min_donor_weight')->label(__('Min Donor Weight (kg)'))->numeric()->required(),
                                        TextInput::make('min_donor_height')->label(__('Min Donor Height (cm)'))->numeric()->required(),
                                        TextInput::make('min_days_between_donations')->label(__('Min Days Between Donations (Days)'))->numeric()->required(),
                                        TextInput::make('min_days_after_surgery')->label(__('Min Days After Surgery (Days)'))->numeric()->required(),
                                    ])->columns(2),
                                Section::make(__('Organization Constraints'))
                                    ->schema([
                                        TextInput::make('org_max_requests_per_day')->label(__('Max Requests Per Day'))->numeric()->required(),
                                    ]),
                            ]),

                        Tab::make('Identity & System')
                            ->label(__('Identity & System'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make(__('Site Identity'))
                                    ->schema([
                                        Tabs::make('Site Identity Tabs')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('site_name.ar')
                                                            ->label(__('Site Name (Arabic)'))
                                                            ->required(),
                                                        TextInput::make('site_slogan.ar')
                                                            ->label(__('Site Slogan (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('site_name.en')
                                                            ->label(__('Site Name (English)'))
                                                            ->required(),
                                                        TextInput::make('site_slogan.en')
                                                            ->label(__('Site Slogan (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('site_logo')
                                            ->label(__('Site Logo'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings'),
                                        FileUpload::make('site_favicon')
                                            ->label(__('Site Favicon'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings'),
                                    ])->columns(2),
                                Section::make(__('Site Status'))
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label(__('Maintenance Mode'))
                                            ->helperText(__('Enable this option to put the site in maintenance mode.'))
                                            ->live(),
                                        Tabs::make('Maintenance Message Tabs')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('maintenance_message.ar')
                                                            ->label(__('Maintenance Message (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('maintenance_message.en')
                                                            ->label(__('Maintenance Message (English)')),
                                                    ]),
                                            ])
                                            ->visible(fn($get) => $get('maintenance_mode'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Contact & Social')
                            ->label(__('Contact & Social'))
                            ->icon('heroicon-o-at-symbol')
                            ->schema([
                                Section::make(__('Contact Information'))
                                    ->schema([
                                        TextInput::make('support_email')
                                            ->label(__('Support Email'))
                                            ->rule('email')
                                            ->required(),
                                        TextInput::make('support_phone')
                                            ->label(__('Support Phone')),
                                        Tabs::make('Contact Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('address.ar')
                                                            ->label(__('Address (Arabic)')),
                                                        TextInput::make('working_days.ar')
                                                            ->label(__('Working Days (Arabic)')),
                                                        TextInput::make('working_hours.ar')
                                                            ->label(__('Working Hours (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('address.en')
                                                            ->label(__('Address (English)')),
                                                        TextInput::make('working_days.en')
                                                            ->label(__('Working Days (English)')),
                                                        TextInput::make('working_hours.en')
                                                            ->label(__('Working Hours (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ])->columns(2),
                                Section::make(__('Social Media Links'))
                                    ->schema([
                                        TextInput::make('facebook_url')->label(__('Facebook URL')),
                                        TextInput::make('twitter_url')->label(__('Twitter (X) URL')),
                                        TextInput::make('instagram_url')->label(__('Instagram URL')),
                                        TextInput::make('linkedin_url')->label(__('LinkedIn URL')),
                                        TextInput::make('youtube_url')->label(__('YouTube URL')),
                                    ])->columns(2),
                            ]),

                        Tab::make('SEO')
                            ->label(__('SEO'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Tabs::make('SEO Localized')
                                    ->tabs([
                                        Tab::make('Arabic')
                                            ->label(__('Arabic'))
                                            ->schema([
                                                TextInput::make('seo_title.ar')
                                                    ->label(__('Default SEO Title (Arabic)')),
                                                Textarea::make('seo_description.ar')
                                                    ->label(__('Default SEO Description (Arabic)')),
                                                TextInput::make('seo_keywords.ar')
                                                    ->label(__('SEO Keywords (Arabic)')),
                                            ]),
                                        Tab::make('English')
                                            ->label(__('English'))
                                            ->schema([
                                                TextInput::make('seo_title.en')
                                                    ->label(__('Default SEO Title (English)')),
                                                Textarea::make('seo_description.en')
                                                    ->label(__('Default SEO Description (English)')),
                                                TextInput::make('seo_keywords.en')
                                                    ->label(__('SEO Keywords (English)')),
                                            ]),
                                    ])->columnSpanFull(),
                            ]),

                        Tab::make('Home Page')
                            ->label(__('Home Page'))
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make(__('Hero Section'))
                                    ->schema([
                                        Tabs::make('Hero Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('home_hero_title.ar')->label(__('Main Title (Arabic)')),
                                                        Textarea::make('home_hero_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('home_hero_title.en')->label(__('Main Title (English)')),
                                                        Textarea::make('home_hero_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('home_hero_image')->label(__('Section Image'))->image()->disk('public')->directory('settings'),
                                    ]),
                                Section::make(__('Features'))
                                    ->schema([
                                        Tabs::make('Features Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('home_features_title.ar')->label(__('Section Title (Arabic)')),
                                                        TextInput::make('home_features_subtitle.ar')->label(__('Section Description (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('home_features_title.en')->label(__('Section Title (English)')),
                                                        TextInput::make('home_features_subtitle.en')->label(__('Section Description (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Features List Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('home_features.ar')
                                                            ->label(__('Features List (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('icon')->label(__('Icon (Emoji or HTML)')),
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(3),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('home_features.en')
                                                            ->label(__('Features List (English)'))
                                                            ->schema([
                                                                TextInput::make('icon')->label(__('Icon (Emoji or HTML)')),
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(3),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('How It Works (For Donors)'))
                                    ->schema([
                                        Tabs::make('Donor Steps Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_donor.ar')
                                                            ->label(__('Donor Steps (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_donor.en')
                                                            ->label(__('Donor Steps (English)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('How It Works (For Organizations)'))
                                    ->schema([
                                        Tabs::make('Org Steps Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_org.ar')
                                                            ->label(__('Organization Steps (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_org.en')
                                                            ->label(__('Organization Steps (English)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Signup Section (CTA)'))
                                    ->schema([
                                        Tabs::make('CTA Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('home_cta_title.ar')->label(__('Title (Arabic)')),
                                                        TextInput::make('home_cta_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('home_cta_title.en')->label(__('Title (English)')),
                                                        TextInput::make('home_cta_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('About Page')
                            ->label(__('About Page'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('Page Intro'))
                                    ->schema([
                                        Tabs::make('About Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('about_hero_title.ar')->label(__('Title (Arabic)')),
                                                        TextInput::make('about_hero_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('about_hero_title.en')->label(__('Title (English)')),
                                                        TextInput::make('about_hero_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Mission & Vision'))
                                    ->schema([
                                        Tabs::make('Mission Vision Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Group::make([
                                                                    TextInput::make('about_mission_title1.ar')->label(__('Mission Title (Arabic)')),
                                                                    Textarea::make('about_mission_text1.ar')->label(__('Mission Text (Arabic)')),
                                                                ]),
                                                                Group::make([
                                                                    TextInput::make('about_mission_title2.ar')->label(__('Vision Title (Arabic)')),
                                                                    Textarea::make('about_mission_text2.ar')->label(__('Vision Text (Arabic)')),
                                                                ]),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Group::make([
                                                                    TextInput::make('about_mission_title1.en')->label(__('Mission Title (English)')),
                                                                    Textarea::make('about_mission_text1.en')->label(__('Mission Text (English)')),
                                                                ]),
                                                                Group::make([
                                                                    TextInput::make('about_mission_title2.en')->label(__('Vision Title (English)')),
                                                                    Textarea::make('about_mission_text2.en')->label(__('Vision Text (English)')),
                                                                ]),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('about_mission_image1')->label(__('Mission Image'))->image()->disk('public')->directory('settings'),
                                                FileUpload::make('about_mission_image2')->label(__('Vision Image'))->image()->disk('public')->directory('settings'),
                                            ]),
                                    ]),
                                Section::make(__('Values'))
                                    ->schema([
                                        Tabs::make('Values Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('about_values_title.ar')->label(__('Section Title (Arabic)')),
                                                        TextInput::make('about_values_subtitle.ar')->label(__('Section Description (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('about_values_title.en')->label(__('Section Title (English)')),
                                                        TextInput::make('about_values_subtitle.en')->label(__('Section Description (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Values List Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('about_values.ar')
                                                            ->label(__('Values List (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                                TextInput::make('icon')->label(__('Icon (Emoji or HTML)')),
                                                                FileUpload::make('image')->label(__('Image'))->image()->disk('public')->directory('settings'),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('about_values.en')
                                                            ->label(__('Values List (English)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                                TextInput::make('icon')->label(__('Icon (Emoji or HTML)')),
                                                                FileUpload::make('image')->label(__('Image'))->image()->disk('public')->directory('settings'),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Team'))
                                    ->schema([
                                        Tabs::make('Team Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('about_team_title.ar')->label(__('Section Title (Arabic)')),
                                                        TextInput::make('about_team_subtitle.ar')->label(__('Section Description (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('about_team_title.en')->label(__('Section Title (English)')),
                                                        TextInput::make('about_team_subtitle.en')->label(__('Section Description (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Team Members Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('about_team_members.ar')
                                                            ->label(__('Team Members (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('name')->label(__('Name')),
                                                                TextInput::make('role')->label(__('Role')),
                                                                Textarea::make('bio')->label(__('Bio')),
                                                                FileUpload::make('image')->label(__('Image'))->image(),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('about_team_members.en')
                                                            ->label(__('Team Members (English)'))
                                                            ->schema([
                                                                TextInput::make('name')->label(__('Name')),
                                                                TextInput::make('role')->label(__('Role')),
                                                                Textarea::make('bio')->label(__('Bio')),
                                                                FileUpload::make('image')->label(__('Image'))->image(),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Impact & Join'))
                                    ->schema([
                                        Tabs::make('Impact Join Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('about_impact_title.ar')->label(__('Impact Title (Arabic)')),
                                                        Textarea::make('about_impact_text.ar')->label(__('Impact Text (Arabic)')),
                                                        TextInput::make('about_join_title.ar')->label(__('Join Title (Arabic)')),
                                                        TextInput::make('about_join_subtitle.ar')->label(__('Join Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('about_impact_title.en')->label(__('Impact Title (English)')),
                                                        Textarea::make('about_impact_text.en')->label(__('Impact Text (English)')),
                                                        TextInput::make('about_join_title.en')->label(__('Join Title (English)')),
                                                        TextInput::make('about_join_subtitle.en')->label(__('Join Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Contact & FAQs')
                            ->label(__('Contact & FAQs'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(__('Receiving Messages'))
                                    ->schema([
                                        Toggle::make('enable_contact_messages')
                                            ->label(__('Enable Contact Messages'))
                                            ->helperText(__('This option allows visitors to send messages via the Contact Us form.'))
                                            ->default(true),
                                    ]),
                                Section::make(__('Page Intro'))
                                    ->schema([
                                        Tabs::make('Contact Page Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('contact_hero_title.ar')->label(__('Title (Arabic)')),
                                                        TextInput::make('contact_hero_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('contact_hero_title.en')->label(__('Title (English)')),
                                                        TextInput::make('contact_hero_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('FAQs'))
                                    ->schema([
                                        Tabs::make('FAQs Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('contact_faqs.ar')
                                                            ->label(__('FAQs (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('question')->label(__('Question')),
                                                                Textarea::make('answer')->label(__('Answer')),
                                                            ])->columns(1),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('contact_faqs.en')
                                                            ->label(__('FAQs (English)'))
                                                            ->schema([
                                                                TextInput::make('question')->label(__('Question')),
                                                                Textarea::make('answer')->label(__('Answer')),
                                                            ])->columns(1),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Auth Pages')
                            ->label(__('Auth Pages'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make(__('Login Page'))
                                    ->schema([
                                        Tabs::make('Login Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('login_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('login_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('login_title.en')->label(__('Title (English)')),
                                                        Textarea::make('login_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('login_image')->label(__('Image'))->image(),
                                    ]),
                                Section::make(__('Signup Choice Page'))
                                    ->schema([
                                        Tabs::make('Signup Choice Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('signup_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('signup_title.en')->label(__('Title (English)')),
                                                        Textarea::make('signup_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Donor Signup Page'))
                                    ->schema([
                                        Tabs::make('Donor Signup Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_donor_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('signup_donor_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('signup_donor_title.en')->label(__('Title (English)')),
                                                        Textarea::make('signup_donor_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('signup_donor_image')->label(__('Image'))->image(),
                                        Tabs::make('Donor Tasks Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('signup_donor_tasks.ar')
                                                            ->label(__('Tasks (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('signup_donor_tasks.en')
                                                            ->label(__('Tasks (English)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Organization Signup Page'))
                                    ->schema([
                                        Tabs::make('Org Signup Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_org_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('signup_org_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('signup_org_title.en')->label(__('Title (English)')),
                                                        Textarea::make('signup_org_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('signup_org_image')->label(__('Image'))->image(),
                                        Tabs::make('Org Tasks Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        Repeater::make('signup_org_tasks.ar')
                                                            ->label(__('Tasks (Arabic)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        Repeater::make('signup_org_tasks.en')
                                                            ->label(__('Tasks (English)'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('Title')),
                                                                TextInput::make('text')->label(__('Description')),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('Donor Registration Welcome'))
                                    ->schema([
                                        Tabs::make('Donor Reg Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('donor_register_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('donor_register_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('donor_register_title.en')->label(__('Title (English)')),
                                                        Textarea::make('donor_register_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('donor_register_image')->label(__('Image'))->image(),
                                    ]),
                                Section::make(__('Organization Registration Welcome'))
                                    ->schema([
                                        Tabs::make('Org Reg Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('Arabic'))
                                                    ->schema([
                                                        TextInput::make('org_register_title.ar')->label(__('Title (Arabic)')),
                                                        Textarea::make('org_register_subtitle.ar')->label(__('Subtitle (Arabic)')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('English'))
                                                    ->schema([
                                                        TextInput::make('org_register_title.en')->label(__('Title (English)')),
                                                        Textarea::make('org_register_subtitle.en')->label(__('Subtitle (English)')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('org_register_image')->label(__('Image'))->image(),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
