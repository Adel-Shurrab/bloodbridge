<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->addOrUpdate('general.site_slogan', 'أعط الحياة قطرة قطرة');
        $this->addOrUpdate('general.maintenance_message', 'الموقع حالياً في وضع الصيانة، سنعود قريباً.');
        $this->addOrUpdate('general.site_logo', null);
        $this->addOrUpdate('general.site_favicon', null);

        // Contact Info
        if ($this->migrator->exists('general.support_email')) {
            $this->migrator->delete('general.support_email');
        }
        $this->migrator->add('general.support_email', 'info@bloodbridge.com');

        $this->addOrUpdate('general.support_phone', '+970-59-123-4567');
        $this->addOrUpdate('general.address', 'فلسطين، غزة');
        $this->addOrUpdate('general.working_days', 'الأحد - الخميس');
        $this->addOrUpdate('general.working_hours', 'من 8:00 صباحاً - 5:00 مساءً');

        // Social links
        $this->addOrUpdate('general.facebook_url', '#');
        $this->addOrUpdate('general.twitter_url', '#');
        $this->addOrUpdate('general.instagram_url', '#');
        $this->addOrUpdate('general.linkedin_url', '#');
        $this->addOrUpdate('general.youtube_url', '#');

        // SEO
        $this->addOrUpdate('general.seo_title', 'BloodBridge - إعدادات الموقع');
        $this->addOrUpdate('general.seo_description', 'نظام ذكي يربط المتبرعين بالمحتاجين، مما يساعد في إنقاذ الأرواح قطرة قطرة.');
        $this->addOrUpdate('general.seo_keywords', 'تبرع بالدم, حياة, منظمة طبية, غزة');

        // Home Page Content
        $this->addOrUpdate('general.home_hero_title', 'مرحباً بك في موقعنا');
        $this->addOrUpdate('general.home_hero_subtitle', 'هذا هو النص الترحيبي الذي يظهر في مقدمة الصفحة.');
        $this->addOrUpdate('general.home_hero_image', null);

        $this->addOrUpdate('general.home_features_title', 'مميزاتنا');
        $this->addOrUpdate('general.home_features_subtitle', 'اكتشف ما يجعلنا مميزين');
        $this->addOrUpdate('general.home_features', [
            ['icon' => '📁', 'title' => 'سهل الاستخدام', 'text' => 'واجهة بسيطة وسهلة'],
            ['icon' => '📁', 'title' => 'آمن وموثوق', 'text' => 'حماية كاملة لبياناتك'],
            ['icon' => '📁', 'title' => 'دعم متواصل', 'text' => 'نحن هنا لمساعدتك دائماً'],
        ]);

        $this->addOrUpdate('general.home_how_it_works_donor', [
            ['title' => 'سجل حسابك', 'text' => 'أنشئ حساباً بسهولة'],
            ['title' => 'ابحث عن طلب', 'text' => 'اعثر على طلب مناسب'],
            ['title' => 'تبرع وأنقذ حياة', 'text' => 'كن بطلاً لشخص ما'],
        ]);

        $this->addOrUpdate('general.home_how_it_works_org', [
            ['title' => 'سجل منظمتك', 'text' => 'أنشئ حساب منظمة'],
            ['title' => 'أنشئ طلباً', 'text' => 'حدد احتياجاتك'],
            ['title' => 'تلقَّ التبرعات', 'text' => 'احصل على المساعدة'],
        ]);

        $this->addOrUpdate('general.home_cta_title', 'ابدأ رحلتك معنا اليوم');
        $this->addOrUpdate('general.home_cta_subtitle', 'انضم إلى مجتمعنا وكن جزءاً من التغيير');

        // About Page
        $this->addOrUpdate('general.about_hero_title', 'من نحن');
        $this->addOrUpdate('general.about_hero_subtitle', 'نحن منصة تربط بين المتبرعين بالدم والمنظمات الطبية لإنقاذ الأرواح');

        $this->addOrUpdate('general.about_mission_title1', 'مهمتنا');
        $this->addOrUpdate('general.about_mission_text1', 'توفير منصة آمنة وسهلة لربط المتبرعين بالمنظمات الطبية');
        $this->addOrUpdate('general.about_mission_image1', null);

        $this->addOrUpdate('general.about_mission_title2', 'رؤيتنا');
        $this->addOrUpdate('general.about_mission_text2', 'عالم لا يموت فيه أحد بسبب نقص الدم');
        $this->addOrUpdate('general.about_mission_image2', null);

        $this->addOrUpdate('general.about_values_title', 'قيمنا الأساسية');
        $this->addOrUpdate('general.about_values_subtitle', 'المبادئ التي نؤمن بها ونعمل بها');
        $this->addOrUpdate('general.about_values', [
            ['title' => 'الشفافية', 'text' => 'نحن ملتزمون بالشفافية الكاملة في جميع عملياتنا', 'image' => null],
            ['title' => 'الأمان', 'text' => 'حماية بيانات المستخدمين هي أولويتنا القصوى', 'image' => null],
            ['title' => 'السرعة', 'text' => 'الاستجابة السريعة لطلبات التبرع العاجلة', 'image' => null],
            ['title' => 'التعاون', 'text' => 'نعمل مع جميع الأطراف لإنقاذ الأرواح', 'image' => null],
        ]);

        $this->addOrUpdate('general.about_team_title', 'تعرف على فريقنا');
        $this->addOrUpdate('general.about_team_subtitle', 'فريق متخصص من المحترفين الملتزمين بإنقاذ الأرواح');
        $this->addOrUpdate('general.about_team_members', [
            ['name' => 'أحمد محمود', 'role' => 'المدير التنفيذي', 'bio' => 'قيادة الفريق وتطوير الاستراتيجيات لتحقيق رؤية المنصة.', 'image' => null],
            ['name' => 'فاطمة أحمد', 'role' => 'مديرة العمليات', 'bio' => 'إدارة العمليات اليومية وضمان جودة الخدمة المقدمة.', 'image' => null],
            ['name' => 'محمد عبدالله', 'role' => 'مدير التقنية', 'bio' => 'تطوير وصيانة البنية التحتية التقنية للمنصة.', 'image' => null],
        ]);

        $this->addOrUpdate('general.about_impact_title', 'تأثيرنا في المجتمع');
        $this->addOrUpdate('general.about_impact_text', 'نحن فخورون بالإنجازات التي حققناها في مساعدة المحتاجين وإنقاذ الأرواح');

        $this->addOrUpdate('general.about_join_title', 'كن جزءاً من التغيير');
        $this->addOrUpdate('general.about_join_subtitle', 'انضم إلينا اليوم وكن جزءاً من مجتمع ينقذ الأرواح');

        // Contact Page
        $this->addOrUpdate('general.contact_hero_title', 'تواصل معنا');
        $this->addOrUpdate('general.contact_hero_subtitle', 'نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت');
        $this->addOrUpdate('general.contact_faqs', [
            ['question' => 'ما هي شروط التبرع؟', 'answer' => 'يجب أن يكون عمرك بين 18-65 سنة ووزنك أكثر من 50 كجم.'],
            ['question' => 'كم مرة يمكنني التبرع؟', 'answer' => 'يمكنك التبرع كل 3 أشهر.'],
            ['question' => 'هل التبرع آمن؟', 'answer' => 'نعم، التبرع آمن تماماً باستخدام معدات معقمة.'],
        ]);

        // Auth Pages
        $this->addOrUpdate('general.login_title', 'مرحباً بعودتك');
        $this->addOrUpdate('general.login_subtitle', 'قم بتسجيل الدخول للوصول إلى حسابك ومتابعة رحلتك في إنقاذ الأرواح.');
        $this->addOrUpdate('general.login_image', null);

        $this->addOrUpdate('general.signup_title', 'انضم إلينا اليوم');
        $this->addOrUpdate('general.signup_subtitle', 'اختر نوع الحساب المناسب لك وابدأ رحلتك في إنقاذ الأرواح.');

        $this->addOrUpdate('general.signup_donor_title', 'كن متبرعاً');
        $this->addOrUpdate('general.signup_donor_subtitle', 'انقذ الأرواح بتبرعك');
        $this->addOrUpdate('general.signup_donor_image', null);
        $this->addOrUpdate('general.signup_donor_tasks', ['تسجيل سريع وسهل', 'ابحث عن طلبات قريبة منك', 'تتبع تبرعاتك']);

        $this->addOrUpdate('general.signup_org_title', 'سجل منظمتك');
        $this->addOrUpdate('general.signup_org_subtitle', 'احصل على التبرعات التي تحتاجها');
        $this->addOrUpdate('general.signup_org_image', null);
        $this->addOrUpdate('general.signup_org_tasks', ['إنشاء طلبات التبرع', 'إدارة المتبرعين', 'تقارير مفصلة']);

        $this->addOrUpdate('general.donor_register_title', 'سجل كمتبرع');
        $this->addOrUpdate('general.donor_register_subtitle', 'املأ البيانات التالية لإنشاء حسابك كمتبرع وابدأ في إنقاذ الأرواح.');
        $this->addOrUpdate('general.donor_register_image', null);

        $this->addOrUpdate('general.org_register_title', 'سجل منظمتك');
        $this->addOrUpdate('general.org_register_subtitle', 'املأ بيانات منظمتك للانضمام إلى شبكتنا والحصول على التبرعات التي تحتاجها.');
        $this->addOrUpdate('general.org_register_image', null);

        if ($this->migrator->exists('general.main_content')) {
            $this->migrator->delete('general.main_content');
        }
    }

    private function addOrUpdate(string $key, mixed $value): void
    {
        if ($this->migrator->exists($key)) {
            $this->migrator->delete($key);
        }
        $this->migrator->add($key, $value);
    }
};
