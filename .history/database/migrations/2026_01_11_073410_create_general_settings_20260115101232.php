<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Site Identity
        $this->migrator->add('general.site_name', 'BloodBridge');
        $this->migrator->add('general.site_slogan', 'أعط الحياة قطرة قطرة');
        $this->migrator->add('general.maintenance_mode', false);
        $this->migrator->add('general.maintenance_message', 'الموقع حالياً في وضع الصيانة، سنعود قريباً.');
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.site_favicon', null);

        // Contact Info
        $this->migrator->add('general.support_email', 'info@bloodbridge.com');
        $this->migrator->add('general.support_phone', '+970-59-123-4567');
        $this->migrator->add('general.address', 'فلسطين، غزة');
        $this->migrator->add('general.working_days', 'الأحد - الخميس');
        $this->migrator->add('general.working_hours', 'من 8:00 صباحاً - 5:00 مساءً');

        // Social links
        $this->migrator->add('general.facebook_url', '#');
        $this->migrator->add('general.twitter_url', '#');
        $this->migrator->add('general.instagram_url', '#');
        $this->migrator->add('general.linkedin_url', '#');
        $this->migrator->add('general.youtube_url', '#');

        // SEO
        $this->migrator->add('general.seo_title', 'BloodBridge - إعدادات الموقع');
        $this->migrator->add('general.seo_description', 'نظام ذكي يربط المتبرعين بالمحتاجين، مما يساعد في إنقاذ الأرواح قطرة قطرة.');
        $this->migrator->add('general.seo_keywords', 'تبرع بالدم, حياة, منظمة طبية, غزة');

        // Home Page Content
        $this->migrator->add('general.home_hero_title', 'مرحباً بك في موقعنا');
        $this->migrator->add('general.home_hero_subtitle', 'هذا هو النص الترحيبي الذي يظهر في مقدمة الصفحة.');
        $this->migrator->add('general.home_hero_image', null);

        $this->migrator->add('general.home_features_title', 'مميزاتنا');
        $this->migrator->add('general.home_features_subtitle', 'اكتشف ما يجعلنا مميزين');
        $this->migrator->add('general.home_features', [
            ['icon' => '📁', 'title' => 'سهل الاستخدام', 'text' => 'واجهة بسيطة وسهلة'],
            ['icon' => '📁', 'title' => 'آمن وموثوق', 'text' => 'حماية كاملة لبياناتك'],
            ['icon' => '📁', 'title' => 'دعم متواصل', 'text' => 'نحن هنا لمساعدتك دائماً'],
        ]);

        $this->migrator->add('general.home_how_it_works_donor', [
            ['title' => 'سجل حسابك', 'text' => 'أنشئ حساباً بسهولة'],
            ['title' => 'ابحث عن طلب', 'text' => 'اعثر على طلب مناسب'],
            ['title' => 'تبرع وأنقذ حياة', 'text' => 'كن بطلاً لشخص ما'],
        ]);

        $this->migrator->add('general.home_how_it_works_org', [
            ['title' => 'سجل منظمتك', 'text' => 'أنشئ حساب منظمة'],
            ['title' => 'أنشئ طلباً', 'text' => 'حدد احتياجاتك'],
            ['title' => 'تلقَّ التبرعات', 'text' => 'احصل على المساعدة'],
        ]);

        $this->migrator->add('general.home_cta_title', 'ابدأ رحلتك معنا اليوم');
        $this->migrator->add('general.home_cta_subtitle', 'انضم إلى مجتمعنا وكن جزءاً من التغيير');

        // About Page
        $this->migrator->add('general.about_hero_title', 'من نحن');
        $this->migrator->add('general.about_hero_subtitle', 'نحن منصة تربط بين المتبرعين بالدم والمنظمات الطبية لإنقاذ الأرواح');

        $this->migrator->add('general.about_mission_title1', 'مهمتنا');
        $this->migrator->add('general.about_mission_text1', 'توفير منصة آمنة وسهلة لربط المتبرعين بالمنظمات الطبية');
        $this->migrator->add('general.about_mission_image1', null);

        $this->migrator->add('general.about_mission_title2', 'رؤيتنا');
        $this->migrator->add('general.about_mission_text2', 'عالم لا يموت فيه أحد بسبب نقص الدم');
        $this->migrator->add('general.about_mission_image2', null);

        $this->migrator->add('general.about_values_title', 'قيمنا الأساسية');
        $this->migrator->add('general.about_values_subtitle', 'المبادئ التي نؤمن بها ونعمل بها');
        $this->migrator->add('general.about_values', [
            ['title' => 'الشفافية', 'text' => 'نحن ملتزمون بالشفافية الكاملة في جميع عملياتنا', 'image' => null],
            ['title' => 'الأمان', 'text' => 'حماية بيانات المستخدمين هي أولويتنا القصوى', 'image' => null],
            ['title' => 'السرعة', 'text' => 'الاستجابة السريعة لطلبات التبرع العاجلة', 'image' => null],
            ['title' => 'التعاون', 'text' => 'نعمل مع جميع الأطراف لإنقاذ الأرواح', 'image' => null],
        ]);

        $this->migrator->add('general.about_team_title', 'تعرف على فريقنا');
        $this->migrator->add('general.about_team_subtitle', 'فريق متخصص من المحترفين الملتزمين بإنقاذ الأرواح');
        $this->migrator->add('general.about_team_members', [
            ['name' => 'أحمد محمود', 'role' => 'المدير التنفيذي', 'bio' => 'قيادة الفريق وتطوير الاستراتيجيات لتحقيق رؤية المنصة.', 'image' => null],
            ['name' => 'فاطمة أحمد', 'role' => 'مديرة العمليات', 'bio' => 'إدارة العمليات اليومية وضمان جودة الخدمة المقدمة.', 'image' => null],
            ['name' => 'محمد عبدالله', 'role' => 'مدير التقنية', 'bio' => 'تطوير وصيانة البنية التحتية التقنية للمنصة.', 'image' => null],
        ]);

        $this->migrator->add('general.about_impact_title', 'تأثيرنا في المجتمع');
        $this->migrator->add('general.about_impact_text', 'نحن فخورون بالإنجازات التي حققناها في مساعدة المحتاجين وإنقاذ الأرواح');

        $this->migrator->add('general.about_join_title', 'كن جزءاً من التغيير');
        $this->migrator->add('general.about_join_subtitle', 'انضم إلينا اليوم وكن جزءاً من مجتمع ينقذ الأرواح');

        // Contact Page
        $this->migrator->add('general.contact_hero_title', 'تواصل معنا');
        $this->migrator->add('general.contact_hero_subtitle', 'نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت');
        $this->migrator->add('general.contact_faqs', [
            ['question' => 'ما هي شروط التبرع؟', 'answer' => 'يجب أن يكون عمرك بين 18-65 سنة ووزنك أكثر من 50 كجم.'],
            ['question' => 'كم مرة يمكنني التبرع؟', 'answer' => 'يمكنك التبرع كل 3 أشهر.'],
            ['question' => 'هل التبرع آمن؟', 'answer' => 'نعم، التبرع آمن تماماً باستخدام معدات معقمة.'],
        ]);

        // Auth Pages
        $this->migrator->add('general.login_title', 'مرحباً بعودتك');
        $this->migrator->add('general.login_subtitle', 'قم بتسجيل الدخول للوصول إلى حسابك ومتابعة رحلتك في إنقاذ الأرواح.');
        $this->migrator->add('general.login_image', null);

        $this->migrator->add('general.signup_title', 'انضم إلينا اليوم');
        $this->migrator->add('general.signup_subtitle', 'اختر نوع الحساب المناسب لك وابدأ رحلتك في إنقاذ الأرواح.');

        $this->migrator->add('general.signup_donor_title', 'كن متبرعاً');
        $this->migrator->add('general.signup_donor_subtitle', 'انقذ الأرواح بتبرعك');
        $this->migrator->add('general.signup_donor_image', null);
        $this->migrator->add('general.signup_donor_tasks', ['تسجيل سريع وسهل', 'ابحث عن طلبات قريبة منك', 'تتبع تبرعاتك']);

        $this->migrator->add('general.signup_org_title', 'سجل منظمتك');
        $this->migrator->add('general.signup_org_subtitle', 'احصل على التبرعات التي تحتاجها');
        $this->migrator->add('general.signup_org_image', null);
        $this->migrator->add('general.signup_org_tasks', ['إنشاء طلبات التبرع', 'إدارة المتبرعين', 'تقارير مفصلة']);

        $this->migrator->add('general.donor_register_title', 'سجل كمتبرع');
        $this->migrator->add('general.donor_register_subtitle', 'املأ البيانات التالية لإنشاء حسابك كمتبرع وابدأ في إنقاذ الأرواح.');
        $this->migrator->add('general.donor_register_image', null);

        $this->migrator->add('general.org_register_title', 'سجل منظمتك');
        $this->migrator->add('general.org_register_subtitle', 'املأ بيانات منظمتك للانضمام إلى شبكتنا والحصول على التبرعات التي تحتاجها.');
        $this->migrator->add('general.org_register_image', null);

        $this->migrator->add('general.min_donor_age', 18);
        $this->migrator->add('general.max_donor_age', 65);
        $this->migrator->add('general.min_donor_weight', 50);
        $this->migrator->add('general.min_days_between_donations', 90);
        $this->migrator->add('general.min_donor_height', 140);
        $this->migrator->add('general.min_days_after_surgery', 28);
        $this->migrator->add('general.org_max_requests_per_day', 5);
    }
};
