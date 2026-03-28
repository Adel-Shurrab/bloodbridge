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
use Dotswan\MapPicker\Fields\Map;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

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
                            ->label(__('admin.system_rules'))
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Section::make(__('admin.donor_eligibility'))
                                    ->schema([
                                        TextInput::make('min_donor_age')->label(__('admin.min_donor_age_years'))->numeric()->required(),
                                        TextInput::make('max_donor_age')->label(__('admin.max_donor_age_years'))->numeric()->required(),
                                        TextInput::make('min_donor_weight')->label(__('admin.min_donor_weight_kg'))->numeric()->required(),
                                        TextInput::make('min_donor_height')->label(__('admin.min_donor_height_cm'))->numeric()->required(),
                                        TextInput::make('min_days_between_donations')->label(__('admin.min_days_between_donations_days'))->numeric()->required(),
                                        TextInput::make('min_days_after_surgery')->label(__('admin.min_days_after_surgery_days'))->numeric()->required(),
                                    ])->columns(2),
                                Section::make(__('admin.organization_constraints'))
                                    ->schema([
                                        TextInput::make('org_max_requests_per_day')->label(__('admin.max_requests_per_day'))->numeric()->required(),
                                    ]),
                            ]),

                        Tab::make('Identity & System')
                            ->label(__('admin.identity_and_system'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make(__('admin.site_identity'))
                                    ->schema([
                                        Tabs::make('Site Identity Tabs')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('site_name.ar')
                                                            ->label(__('admin.site_name_arabic'))
                                                            ->required(),
                                                        TextInput::make('site_slogan.ar')
                                                            ->label(__('admin.site_slogan_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('site_name.en')
                                                            ->label(__('admin.site_name_english'))
                                                            ->required(),
                                                        TextInput::make('site_slogan.en')
                                                            ->label(__('admin.site_slogan_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('site_logo')
                                            ->label(__('admin.site_logo'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings'),
                                        FileUpload::make('site_favicon')
                                            ->label(__('admin.site_favicon'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings'),
                                    ])->columns(2),
                                Section::make(__('admin.site_status'))
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label(__('admin.maintenance_mode'))
                                            ->helperText(__('admin.enable_this_option_to_put_the_site_in_maintenance_mode'))
                                            ->live(),
                                        Tabs::make('Maintenance Message Tabs')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('maintenance_message.ar')
                                                            ->label(__('admin.maintenance_message_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('maintenance_message.en')
                                                            ->label(__('admin.maintenance_message_english')),
                                                    ]),
                                            ])
                                            ->visible(fn($get) => $get('maintenance_mode'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Contact & Social')
                            ->label(__('admin.contact_and_social'))
                            ->icon('heroicon-o-at-symbol')
                            ->schema([
                                Section::make(__('admin.contact_information'))
                                    ->schema([
                                        TextInput::make('support_email')
                                            ->label(__('admin.support_email'))
                                            ->rule('email')
                                            ->required(),
                                        TextInput::make('support_phone')
                                            ->label(__('admin.support_phone')),
                                        Tabs::make('Contact Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('address.ar')
                                                            ->label(__('admin.address_arabic')),
                                                        TextInput::make('working_days.ar')
                                                            ->label(__('admin.working_days_arabic')),
                                                        TextInput::make('working_hours.ar')
                                                            ->label(__('admin.working_hours_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('address.en')
                                                            ->label(__('admin.address_english')),
                                                        TextInput::make('working_days.en')
                                                            ->label(__('admin.working_days_english')),
                                                        TextInput::make('working_hours.en')
                                                            ->label(__('admin.working_hours_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ])->columns(2),
                                Section::make(__('admin.social_media_links'))
                                    ->schema([
                                        TextInput::make('facebook_url')->label(__('admin.facebook_url')),
                                        TextInput::make('twitter_url')->label(__('admin.twitter_x_url')),
                                        TextInput::make('instagram_url')->label(__('admin.instagram_url')),
                                        TextInput::make('linkedin_url')->label(__('admin.linkedin_url')),
                                        TextInput::make('youtube_url')->label(__('admin.youtube_url')),
                                    ])->columns(2),
                            ]),

                        Tab::make('SEO')
                            ->label(__('admin.seo'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Tabs::make('SEO Localized')
                                    ->tabs([
                                        Tab::make('Arabic')
                                            ->label(__('admin.arabic'))
                                            ->schema([
                                                TextInput::make('seo_title.ar')
                                                    ->label(__('admin.default_seo_title_arabic')),
                                                Textarea::make('seo_description.ar')
                                                    ->label(__('admin.default_seo_description_arabic')),
                                                TextInput::make('seo_keywords.ar')
                                                    ->label(__('admin.seo_keywords_arabic')),
                                            ]),
                                        Tab::make('English')
                                            ->label(__('admin.english'))
                                            ->schema([
                                                TextInput::make('seo_title.en')
                                                    ->label(__('admin.default_seo_title_english')),
                                                Textarea::make('seo_description.en')
                                                    ->label(__('admin.default_seo_description_english')),
                                                TextInput::make('seo_keywords.en')
                                                    ->label(__('admin.seo_keywords_english')),
                                            ]),
                                    ])->columnSpanFull(),
                            ]),

                        Tab::make('Home Page')
                            ->label(__('admin.home_page'))
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make(__('admin.hero_section'))
                                    ->schema([
                                        Tabs::make('Hero Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('home_hero_title.ar')->label(__('admin.main_title_arabic')),
                                                        Textarea::make('home_hero_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('home_hero_title.en')->label(__('admin.main_title_english')),
                                                        Textarea::make('home_hero_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('home_hero_image')->label(__('admin.section_image'))->image()->disk('public')->directory('settings'),
                                    ]),
                                Section::make(__('admin.features'))
                                    ->schema([
                                        Tabs::make('Features Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('home_features_title.ar')->label(__('admin.section_title_arabic')),
                                                        TextInput::make('home_features_subtitle.ar')->label(__('admin.section_description_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('home_features_title.en')->label(__('admin.section_title_english')),
                                                        TextInput::make('home_features_subtitle.en')->label(__('admin.section_description_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Features List Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('home_features.ar')
                                                            ->label(__('admin.features_list_arabic'))
                                                            ->schema([
                                                                TextInput::make('icon')->label(__('admin.icon_emoji_or_html')),
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(3),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('home_features.en')
                                                            ->label(__('admin.features_list_english'))
                                                            ->schema([
                                                                TextInput::make('icon')->label(__('admin.icon_emoji_or_html')),
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(3),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.how_it_works_for_donors'))
                                    ->schema([
                                        Tabs::make('Donor Steps Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_donor.ar')
                                                            ->label(__('admin.donor_steps_arabic'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_donor.en')
                                                            ->label(__('admin.donor_steps_english'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.how_it_works_for_organizations'))
                                    ->schema([
                                        Tabs::make('Org Steps Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_org.ar')
                                                            ->label(__('admin.organization_steps_arabic'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('home_how_it_works_org.en')
                                                            ->label(__('admin.organization_steps_english'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.signup_section_cta'))
                                    ->schema([
                                        Tabs::make('CTA Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('home_cta_title.ar')->label(__('admin.title_arabic')),
                                                        TextInput::make('home_cta_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('home_cta_title.en')->label(__('admin.title_english')),
                                                        TextInput::make('home_cta_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('About Page')
                            ->label(__('admin.about_page'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('admin.page_intro'))
                                    ->schema([
                                        Tabs::make('About Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('about_hero_title.ar')->label(__('admin.title_arabic')),
                                                        TextInput::make('about_hero_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('about_hero_title.en')->label(__('admin.title_english')),
                                                        TextInput::make('about_hero_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.mission_and_vision'))
                                    ->schema([
                                        Tabs::make('Mission Vision Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Group::make([
                                                                    TextInput::make('about_mission_title1.ar')->label(__('admin.mission_title_arabic')),
                                                                    Textarea::make('about_mission_text1.ar')->label(__('admin.mission_text_arabic')),
                                                                ]),
                                                                Group::make([
                                                                    TextInput::make('about_mission_title2.ar')->label(__('admin.vision_title_arabic')),
                                                                    Textarea::make('about_mission_text2.ar')->label(__('admin.vision_text_arabic')),
                                                                ]),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Group::make([
                                                                    TextInput::make('about_mission_title1.en')->label(__('admin.mission_title_english')),
                                                                    Textarea::make('about_mission_text1.en')->label(__('admin.mission_text_english')),
                                                                ]),
                                                                Group::make([
                                                                    TextInput::make('about_mission_title2.en')->label(__('admin.vision_title_english')),
                                                                    Textarea::make('about_mission_text2.en')->label(__('admin.vision_text_english')),
                                                                ]),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('about_mission_image1')->label(__('admin.mission_image'))->image()->disk('public')->directory('settings'),
                                                FileUpload::make('about_mission_image2')->label(__('admin.vision_image'))->image()->disk('public')->directory('settings'),
                                            ]),
                                    ]),
                                Section::make(__('admin.values'))
                                    ->schema([
                                        Tabs::make('Values Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('about_values_title.ar')->label(__('admin.section_title_arabic')),
                                                        TextInput::make('about_values_subtitle.ar')->label(__('admin.section_description_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('about_values_title.en')->label(__('admin.section_title_english')),
                                                        TextInput::make('about_values_subtitle.en')->label(__('admin.section_description_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Values List Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('about_values.ar')
                                                            ->label(__('admin.values_list_arabic'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                                TextInput::make('icon')->label(__('admin.icon_emoji_or_html')),
                                                                FileUpload::make('image')->label(__('admin.image'))->image()->disk('public')->directory('settings'),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('about_values.en')
                                                            ->label(__('admin.values_list_english'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                                TextInput::make('icon')->label(__('admin.icon_emoji_or_html')),
                                                                FileUpload::make('image')->label(__('admin.image'))->image()->disk('public')->directory('settings'),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.team'))
                                    ->schema([
                                        Tabs::make('Team Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('about_team_title.ar')->label(__('admin.section_title_arabic')),
                                                        TextInput::make('about_team_subtitle.ar')->label(__('admin.section_description_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('about_team_title.en')->label(__('admin.section_title_english')),
                                                        TextInput::make('about_team_subtitle.en')->label(__('admin.section_description_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        Tabs::make('Team Members Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('about_team_members.ar')
                                                            ->label(__('admin.team_members_arabic'))
                                                            ->schema([
                                                                TextInput::make('name')->label(__('admin.name')),
                                                                TextInput::make('role')->label(__('admin.role')),
                                                                Textarea::make('bio')->label(__('admin.bio')),
                                                                FileUpload::make('image')->label(__('admin.image'))->image(),
                                                            ])->columns(2),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('about_team_members.en')
                                                            ->label(__('admin.team_members_english'))
                                                            ->schema([
                                                                TextInput::make('name')->label(__('admin.name')),
                                                                TextInput::make('role')->label(__('admin.role')),
                                                                Textarea::make('bio')->label(__('admin.bio')),
                                                                FileUpload::make('image')->label(__('admin.image'))->image(),
                                                            ])->columns(2),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.impact_and_join'))
                                    ->schema([
                                        Tabs::make('Impact Join Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('about_impact_title.ar')->label(__('admin.impact_title_arabic')),
                                                        Textarea::make('about_impact_text.ar')->label(__('admin.impact_text_arabic')),
                                                        TextInput::make('about_join_title.ar')->label(__('admin.join_title_arabic')),
                                                        TextInput::make('about_join_subtitle.ar')->label(__('admin.join_subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('about_impact_title.en')->label(__('admin.impact_title_english')),
                                                        Textarea::make('about_impact_text.en')->label(__('admin.impact_text_english')),
                                                        TextInput::make('about_join_title.en')->label(__('admin.join_title_english')),
                                                        TextInput::make('about_join_subtitle.en')->label(__('admin.join_subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Contact & FAQs')
                            ->label(__('admin.contact_and_faqs'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(__('admin.receiving_messages'))
                                    ->schema([
                                        Toggle::make('enable_contact_messages')
                                            ->label(__('admin.enable_contact_messages'))
                                            ->helperText(__('admin.this_option_allows_visitors_to_send_messages_via_the_contact_us_form'))
                                            ->default(true),
                                    ]),
                                Section::make(__('admin.page_intro'))
                                    ->schema([
                                        Tabs::make('Contact Page Intro Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('contact_hero_title.ar')->label(__('admin.title_arabic')),
                                                        TextInput::make('contact_hero_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('contact_hero_title.en')->label(__('admin.title_english')),
                                                        TextInput::make('contact_hero_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.faqs'))
                                    ->schema([
                                        Tabs::make('FAQs Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('contact_faqs.ar')
                                                            ->label(__('admin.faqs_arabic'))
                                                            ->schema([
                                                                TextInput::make('question')->label(__('admin.question')),
                                                                Textarea::make('answer')->label(__('admin.answer')),
                                                            ])->columns(1),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('contact_faqs.en')
                                                            ->label(__('admin.faqs_english'))
                                                            ->schema([
                                                                TextInput::make('question')->label(__('admin.question')),
                                                                Textarea::make('answer')->label(__('admin.answer')),
                                                            ])->columns(1),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Auth Pages')
                            ->label(__('admin.auth_pages'))
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make(__('admin.login_page'))
                                    ->schema([
                                        Tabs::make('Login Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('login_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('login_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('login_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('login_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('login_image')->label(__('admin.image'))->image(),
                                    ]),
                                Section::make(__('admin.signup_choice_page'))
                                    ->schema([
                                        Tabs::make('Signup Choice Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('signup_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('signup_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('signup_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.donor_signup_page'))
                                    ->schema([
                                        Tabs::make('Donor Signup Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_donor_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('signup_donor_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('signup_donor_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('signup_donor_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('signup_donor_image')->label(__('admin.image'))->image(),
                                        Tabs::make('Donor Tasks Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('signup_donor_tasks.ar')
                                                            ->label(__('admin.tasks_arabic'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('signup_donor_tasks.en')
                                                            ->label(__('admin.tasks_english'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.organization_signup_page'))
                                    ->schema([
                                        Tabs::make('Org Signup Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('signup_org_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('signup_org_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('signup_org_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('signup_org_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('signup_org_image')->label(__('admin.image'))->image(),
                                        Tabs::make('Org Tasks Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        Repeater::make('signup_org_tasks.ar')
                                                            ->label(__('admin.tasks_arabic'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ]),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        Repeater::make('signup_org_tasks.en')
                                                            ->label(__('admin.tasks_english'))
                                                            ->schema([
                                                                TextInput::make('title')->label(__('admin.title')),
                                                                TextInput::make('text')->label(__('admin.description')),
                                                            ]),
                                                    ]),
                                            ])->columnSpanFull(),
                                    ]),
                                Section::make(__('admin.donor_registration_welcome'))
                                    ->schema([
                                        Tabs::make('Donor Reg Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('donor_register_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('donor_register_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('donor_register_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('donor_register_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('donor_register_image')->label(__('admin.image'))->image(),
                                    ]),
                                Section::make(__('admin.organization_registration_welcome'))
                                    ->schema([
                                        Tabs::make('Org Reg Localized')
                                            ->tabs([
                                                Tab::make('Arabic')
                                                    ->label(__('admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('org_register_title.ar')->label(__('admin.title_arabic')),
                                                        Textarea::make('org_register_subtitle.ar')->label(__('admin.subtitle_arabic')),
                                                    ]),
                                                Tab::make('English')
                                                    ->label(__('admin.english'))
                                                    ->schema([
                                                        TextInput::make('org_register_title.en')->label(__('admin.title_english')),
                                                        Textarea::make('org_register_subtitle.en')->label(__('admin.subtitle_english')),
                                                    ]),
                                            ])->columnSpanFull(),
                                        FileUpload::make('org_register_image')->label(__('admin.image'))->image(),
                                    ]),
                            ]),

                        Tab::make('Map Settings')
                            ->label(__('admin.map_settings'))
                            ->icon('heroicon-o-map')
                            ->schema([
                                Section::make(__('admin.default_map_location'))
                                    ->description(__('admin.default_location_for_map_displays'))
                                    ->schema([
                                        Map::make('default_location')
                                            ->label(__('admin.select_location_on_map'))
                                            ->columnSpanFull()
                                            ->defaultLocation(
                                                latitude: 31.5,
                                                longitude: 34.4667,
                                            )
                                            ->afterStateUpdated(function (Get $get, Set $set, ?array $state): void {
                                                if ($state) {
                                                    $set('map_default_lat', $state['lat']);
                                                    $set('map_default_lng', $state['lng']);
                                                }
                                            })
                                            ->afterStateHydrated(function ($state, Set $set, $record): void {
                                                $set('default_location', [
                                                    'lat' => $record?->map_default_lat ?? 31.5,
                                                    'lng' => $record?->map_default_lng ?? 34.4667,
                                                ]);
                                            })
                                            ->extraStyles([
                                                'min-height: 400px',
                                                'border-radius: 8px',
                                            ])
                                            ->liveLocation(true, true, 5000)
                                            ->showMarker(true)
                                            ->markerColor('#3b82f6')
                                            ->showFullscreenControl(true)
                                            ->showZoomControl(true)
                                            ->draggable(true)
                                            ->tilesUrl('https://tile.openstreetmap.org/{z}/{x}/{y}.png')
                                            ->zoom(10)
                                            ->detectRetina(true)
                                            ->showMyLocationButton(true)
                                            ->clickable(true),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('map_default_lat')
                                                    ->label(__('admin.default_latitude'))
                                                    ->numeric()
                                                    ->step(0.0001)
                                                    ->required(),
                                                TextInput::make('map_default_lng')
                                                    ->label(__('admin.default_longitude'))
                                                    ->numeric()
                                                    ->step(0.0001)
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make(__('admin.coordinate_bounds'))
                                    ->description(__('admin.valid_coordinate_range_for_user_input'))
                                    ->schema([
                                        TextInput::make('map_lat_min')
                                            ->label(__('admin.minimum_latitude'))
                                            ->numeric()
                                            ->step(0.0001)
                                            ->required(),
                                        TextInput::make('map_lat_max')
                                            ->label(__('admin.maximum_latitude'))
                                            ->numeric()
                                            ->step(0.0001)
                                            ->required(),
                                        TextInput::make('map_lng_min')
                                            ->label(__('admin.minimum_longitude'))
                                            ->numeric()
                                            ->step(0.0001)
                                            ->required(),
                                        TextInput::make('map_lng_max')
                                            ->label(__('admin.maximum_longitude'))
                                            ->numeric()
                                            ->step(0.0001)
                                            ->required(),
                                    ])->columns(2),

                                Section::make(__('admin.map_zoom_levels'))
                                    ->description(__('admin.zoom_level_for_map_displays'))
                                    ->schema([
                                        TextInput::make('map_zoom_region')
                                            ->label(__('admin.region_zoom_level'))
                                            ->helperText(__('admin.for_blood_request_locations'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(20)
                                            ->required(),
                                        TextInput::make('map_zoom_city')
                                            ->label(__('admin.city_zoom_level'))
                                            ->helperText(__('admin.for_user_profiles'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(20)
                                            ->required(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
