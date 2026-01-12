<?php
\nuse Spatie\LaravelSettings\Migrations\SettingsMigration;
\nreturn new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_slogan', 'قطرة من دمك.. حياة لغيرك');
        $this->migrator->add('general.maintenance_message', 'الموقع حالياً في وضع الصيانة، سنعود قريباً.');
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.site_favicon', null);
        $this->migrator->add('general.support_phone', '+970590000000');
        $this->migrator->add('general.address', 'فلسطين، غزة');
        $this->migrator->add('general.working_days', 'السبت - الخميس');
        $this->migrator->add('general.working_hours', '8:00 صباحاً - 4:00 مساءً');
        $this->migrator->add('general.facebook_url', 'https://facebook.com/bloodbridge');
        $this->migrator->add('general.twitter_url', 'https://twitter.com/bloodbridge');
        $this->migrator->add('general.instagram_url', 'https://instagram.com/bloodbridge');
        $this->migrator->add('general.linkedin_url', 'https://linkedin.com/company/bloodbridge');
        $this->migrator->add('general.youtube_url', 'https://youtube.com/bloodbridge');
        $this->migrator->add('general.seo_title', 'جسر الدم - BloodBridge');
        $this->migrator->add('general.seo_description', 'منصة ذكية للربط بين المتبرعين والمحتاجين للدم');
        $this->migrator->add('general.seo_keywords', 'دم، تبرع، حياة، فلسطين، متبرع، طلب دم');
\n        // Home Page
        $this->migrator->add('general.home_hero_title', 'أعط الحياة قطرة قطرة');
        $this->migrator->add('general.home_hero_subtitle', 'انضم إلى آلاف المتبرعين الذين ينقذون الأرواح يوميًا. نظام ذكي يربط المتبرعين بالمحتاجين في الوقت المناسب.');
        $this->migrator->add('general.home_hero_image', null);
        $this->migrator->add('general.home_features_title', 'لماذا التبرع؟');
        $this->migrator->add('general.home_features_subtitle', 'تبرعك هو شريان حياة في حالات الطوارئ ولمن يحتاجون إلى علاجات طويلة الأمد');
        $this->migrator->add('general.home_features', [
            ['icon' => '🔒', 'title' => 'عملية آمنة وبسيطة', 'text' => 'تم تصميم عملية التبرع لدينا لضمان راحتك وسلامتك، وضمان تجربة سلسة من البداية إلى النهاية'],
            ['icon' => '📅', 'title' => 'الجدولة في الوقت الفعلي', 'text' => 'يمكنك بسهولة جدولة مواعيد التبرع الخاصة بك في الوقت الفعلي، واختيار الوقت والمكان الأنسب لك'],
            ['icon' => '🏥', 'title' => 'موثوق به من قبل المستشفيات', 'text' => 'نتعاون مع المستشفيات الرائدة لضمان تأثير تبرعاتكم بشكل مباشر على المحتاجين'],
        ]);
        $this->migrator->add('general.home_how_it_works_donor', [
            ['title' => 'تسجيل الدخول', 'text' => 'أنشئ ملفك الشخصي وانضم إلى مجتمعنا من المنقذين'],
            ['title' => 'قبول الطلبات', 'text' => 'استقبل طلبات التبرع من المنظمات بناءً على فصيلة دمك وموقعك'],
            ['title' => 'التبرع', 'text' => 'تفضل بزيارة المركز المخصص للتبرع وإنقاذ الأرواح'],
        ]);
        $this->migrator->add('general.home_how_it_works_org', [
            ['title' => 'تسجيل المنظمة', 'text' => 'سجل منظمتك وأكمل الوثائق المطلوبة للتحقق'],
            ['title' => 'إنشاء طلبات التبرع', 'text' => 'حدد احتياجاتك من فصائل الدم وأنشئ طلبات التبرع'],
            ['title' => 'إدارة التبرعات', 'text' => 'تواصل مع المتبرعين وأدر عمليات التبرع بكفاءة'],
        ]);
        $this->migrator->add('general.home_cta_title', 'ابدأ رحلة الإنقاذ اليوم');
        $this->migrator->add('general.home_cta_subtitle', 'كل تبرع يمكن أن ينقذ حياة ثلاثة أشخاص. انضم إلينا الآن وكن بطلاً');
\n        // About Page
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
        ]);
        $this->migrator->add('general.about_impact_title', 'تأثيرنا في المجتمع');
        $this->migrator->add('general.about_impact_text', 'نحن فخورون بالإنجازات التي حققناها في مساعدة المحتاجين وإنقاذ الأرواح');
        $this->migrator->add('general.about_join_title', 'كن جزءاً من التغيير');
        $this->migrator->add('general.about_join_subtitle', 'انضم إلينا اليوم وكن جزءاً من مجتمع ينقذ الأرواح');
\n        // Contact Page
        $this->migrator->add('general.contact_hero_title', 'تواصل معنا');
        $this->migrator->add('general.contact_hero_subtitle', 'نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت');
        $this->migrator->add('general.contact_faqs', [
            ['question' => 'ما هي شروط التبرع؟', 'answer' => 'يجب أن يكون عمرك بين 18-65 سنة ووزنك أكثر من 50 كجم.'],
            ['question' => 'كم مرة يمكنني التبرع؟', 'answer' => 'يمكنك التبرع كل 3 أشهر.'],
        ]);
\n        // Auth Pages
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
\n        $this->migrator->delete('general.main_content');
    }
};
