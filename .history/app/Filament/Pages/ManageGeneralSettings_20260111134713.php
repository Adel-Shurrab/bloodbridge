<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $title = 'الإعدادات العامة';
    protected static ?string $description = 'إدارة الإعدادات العامة';
    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Settings')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Identity & System')
                            ->label('هوية الموقع والنظام')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('هوية الموقع')
                                    ->schema([
                                        TextInput::make('site_name')
                                            ->label('اسم الموقع')
                                            ->required(),
                                        TextInput::make('site_slogan')
                                            ->label('شعار الموقع (Slogan)'),
                                        \Filament\Forms\Components\FileUpload::make('site_logo')
                                            ->label('لوجو الموقع')
                                            ->image()
                                            ->directory('settings'),
                                        \Filament\Forms\Components\FileUpload::make('site_favicon')
                                            ->label('أيقونة الموقع (Favicon)')
                                            ->image()
                                            ->directory('settings'),
                                    ])->columns(2),
                                Section::make('حالة الموقع')
                                    ->schema([
                                        Toggle::make('site_active')
                                            ->label('الموقع متاح')
                                            ->helperText('تعطيل هذا الخيار سيضع الموقع في وضع الصيانة.')
                                            ->live(),
                                        TextInput::make('maintenance_message')
                                            ->label('رسالة الصيانة')
                                            ->visible(fn($get) => ! $get('site_active'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Contact & Social')
                            ->label('التواصل والاجتماعي')
                            ->icon('heroicon-o-at-symbol')
                            ->schema([
                                Section::make('معلومات التواصل')
                                    ->schema([
                                        TextInput::make('support_email')
                                            ->label('البريد الإلكتروني للدعم')
                                            ->email()
                                            ->required(),
                                        TextInput::make('support_phone')
                                            ->label('رقم هاتف الدعم'),
                                        TextInput::make('address')
                                            ->label('العنوان'),
                                        TextInput::make('working_days')
                                            ->label('أيام العمل'),
                                        TextInput::make('working_hours')
                                            ->label('ساعات العمل'),
                                    ])->columns(2),
                                Section::make('روابط التواصل الاجتماعي')
                                    ->schema([
                                        TextInput::make('facebook_url')->label('Facebook URL'),
                                        TextInput::make('twitter_url')->label('Twitter (X) URL'),
                                        TextInput::make('instagram_url')->label('Instagram URL'),
                                        TextInput::make('linkedin_url')->label('LinkedIn URL'),
                                        TextInput::make('youtube_url')->label('YouTube URL'),
                                    ])->columns(2),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('SEO')
                            ->label('تحسين محركات البحث')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('seo_title')
                                    ->label('عنوان SEO الافتراضي'),
                                \Filament\Forms\Components\Textarea::make('seo_description')
                                    ->label('وصف SEO الافتراضي'),
                                TextInput::make('seo_keywords')
                                    ->label('كلمات SEO المفتاحية'),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Home Page')
                            ->label('الصفحة الرئيسية')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Section::make('قسم البطل (Hero)')
                                    ->schema([
                                        TextInput::make('home_hero_title')->label('العنوان الرئيسي'),
                                        \Filament\Forms\Components\Textarea::make('home_hero_subtitle')->label('العنوان الفرعي'),
                                        \Filament\Forms\Components\FileUpload::make('home_hero_image')->label('صورة القسم')->image(),
                                    ]),
                                Section::make('المميزات')
                                    ->schema([
                                        TextInput::make('home_features_title')->label('عنوان القسم'),
                                        TextInput::make('home_features_subtitle')->label('وصف القسم'),
                                        \Filament\Forms\Components\Repeater::make('home_features')
                                            ->label('قائمة المميزات')
                                            ->schema([
                                                TextInput::make('icon')->label('الأيقونة (Emoji or HTML)'),
                                                TextInput::make('title')->label('العنوان'),
                                                TextInput::make('text')->label('الوصف'),
                                            ])->columns(3),
                                    ]),
                                Section::make('كيف يعمل (للمتبرعين)')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('home_how_it_works_donor')
                                            ->label('خطوات المتبرع')
                                            ->schema([
                                                TextInput::make('title')->label('العنوان'),
                                                TextInput::make('text')->label('الوصف'),
                                            ])->columns(2),
                                    ]),
                                Section::make('كيف يعمل (للمنظمات)')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('home_how_it_works_org')
                                            ->label('خطوات المنظمة')
                                            ->schema([
                                                TextInput::make('title')->label('العنوان'),
                                                TextInput::make('text')->label('الوصف'),
                                            ])->columns(2),
                                    ]),
                                Section::make('قسم التسجيل (CTA)')
                                    ->schema([
                                        TextInput::make('home_cta_title')->label('العنوان'),
                                        TextInput::make('home_cta_subtitle')->label('العنوان الفرعي'),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('About Page')
                            ->label('من نحن')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('افتتاحية الصفحة')
                                    ->schema([
                                        TextInput::make('about_hero_title')->label('العنوان'),
                                        TextInput::make('about_hero_subtitle')->label('العنوان الفرعي'),
                                    ]),
                                Section::make('المهمة والرؤية')
                                    ->schema([
                                        \Filament\Schemas\Components\Grid::make(2)
                                            ->schema([
                                                \Filament\Schemas\Components\Group::make([
                                                    TextInput::make('about_mission_title1')->label('عنوان المهمة'),
                                                    \Filament\Forms\Components\Textarea::make('about_mission_text1')->label('نص المهمة'),
                                                    \Filament\Forms\Components\FileUpload::make('about_mission_image1')->label('صورة المهمة')->image(),
                                                ]),
                                                \Filament\Schemas\Components\Group::make([
                                                    TextInput::make('about_mission_title2')->label('عنوان الرؤية'),
                                                    \Filament\Forms\Components\Textarea::make('about_mission_text2')->label('نص الرؤية'),
                                                    \Filament\Forms\Components\FileUpload::make('about_mission_image2')->label('صورة الرؤية')->image(),
                                                ]),
                                            ]),
                                    ]),
                                Section::make('القيم')
                                    ->schema([
                                        TextInput::make('about_values_title')->label('عنوان القسم'),
                                        TextInput::make('about_values_subtitle')->label('وصف القسم'),
                                        \Filament\Forms\Components\Repeater::make('about_values')
                                            ->label('قائمة القيم')
                                            ->schema([
                                                TextInput::make('title')->label('العنوان'),
                                                TextInput::make('text')->label('الوصف'),
                                                \Filament\Forms\Components\FileUpload::make('image')->label('الصورة')->image(),
                                            ])->columns(3),
                                    ]),
                                Section::make('الفريق')
                                    ->schema([
                                        TextInput::make('about_team_title')->label('عنوان القسم'),
                                        TextInput::make('about_team_subtitle')->label('وصف القسم'),
                                        \Filament\Forms\Components\Repeater::make('about_team_members')
                                            ->label('أعضاء الفريق')
                                            ->schema([
                                                TextInput::make('name')->label('الاسم'),
                                                TextInput::make('role')->label('الوظيفة'),
                                                \Filament\Forms\Components\Textarea::make('bio')->label('نبذة'),
                                                \Filament\Forms\Components\FileUpload::make('image')->label('الصورة')->image(),
                                            ])->columns(2),
                                    ]),
                                Section::make('التأثير والانضمام')
                                    ->schema([
                                        TextInput::make('about_impact_title')->label('عنوان التأثير'),
                                        \Filament\Forms\Components\Textarea::make('about_impact_text')->label('نص التأثير'),
                                        TextInput::make('about_join_title')->label('عنوان الانضمام'),
                                        TextInput::make('about_join_subtitle')->label('وصف الانضمام'),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Contact & FAQs')
                            ->label('تواصل معنا')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make('افتتاحية الصفحة')
                                    ->schema([
                                        TextInput::make('contact_hero_title')->label('العنوان'),
                                        TextInput::make('contact_hero_subtitle')->label('العنوان الفرعي'),
                                    ]),
                                Section::make('الأسئلة الشائعة')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('contact_faqs')
                                            ->label('الأسئلة الشائعة')
                                            ->schema([
                                                TextInput::make('question')->label('السؤال'),
                                                \Filament\Forms\Components\Textarea::make('answer')->label('الإجابة'),
                                            ])->columns(1),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Auth Pages')
                            ->label('صفحات الدخول')
                            ->icon('heroicon-o-user-plus')
                            ->schema([
                                Section::make('تسجيل الدخول')
                                    ->schema([
                                        TextInput::make('login_title')->label('العنوان'),
                                        TextInput::make('login_subtitle')->label('الوصف'),
                                        \Filament\Forms\Components\FileUpload::make('login_image')->label('الصورة')->image(),
                                    ]),
                                Section::make('حساب جديد (الاختيار)')
                                    ->schema([
                                        TextInput::make('signup_title')->label('العنوان'),
                                        TextInput::make('signup_subtitle')->label('الوصف'),
                                    ]),
                                Section::make('أنا متبرع (في خيار التسجيل)')
                                    ->schema([
                                        TextInput::make('signup_donor_title')->label('العنوان'),
                                        TextInput::make('signup_donor_subtitle')->label('الوصف'),
                                        \Filament\Forms\Components\FileUpload::make('signup_donor_image')->label('الصورة')->image(),
                                        \Filament\Forms\Components\TagsInput::make('signup_donor_tasks')->label('المهام/المميزات'),
                                    ]),
                                Section::make('أنا منظمة (في خيار التسجيل)')
                                    ->schema([
                                        TextInput::make('signup_org_title')->label('العنوان'),
                                        TextInput::make('signup_org_subtitle')->label('الوصف'),
                                        \Filament\Forms\Components\FileUpload::make('signup_org_image')->label('الصورة')->image(),
                                        \Filament\Forms\Components\TagsInput::make('signup_org_tasks')->label('المهام/المميزات'),
                                    ]),
                                Section::make('صفحة التسجيل كمتبرع')
                                    ->schema([
                                        TextInput::make('donor_register_title')->label('العنوان'),
                                        TextInput::make('donor_register_subtitle')->label('الوصف'),
                                        \Filament\Forms\Components\FileUpload::make('donor_register_image')->label('الصورة')->image(),
                                    ]),
                                Section::make('صفحة التسجيل كمنظمة')
                                    ->schema([
                                        TextInput::make('org_register_title')->label('العنوان'),
                                        TextInput::make('org_register_subtitle')->label('الوصف'),
                                        \Filament\Forms\Components\FileUpload::make('org_register_image')->label('الصورة')->image(),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
