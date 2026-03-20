<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', ['ar' => 'بلود بريدج', 'en' => 'BloodBridge']);
        $this->migrator->add('general.site_slogan', ['ar' => 'أعط الحياة قطرة قطرة', 'en' => 'Give life, drop by drop']);
        $this->migrator->add('general.maintenance_mode', false);
        $this->migrator->add('general.maintenance_message', ['ar' => 'الموقع حالياً في وضع الصيانة، سنعود قريباً.', 'en' => 'The site is currently in maintenance mode, we will be back soon.']);
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.site_favicon', null);

        $this->migrator->add('general.support_email', 'info@bloodbridge.com');
        $this->migrator->add('general.support_phone', '+970-59-123-4567');
        $this->migrator->add('general.address', ['ar' => 'فلسطين، غزة', 'en' => 'Palestine, Gaza']);
        $this->migrator->add('general.working_days', ['ar' => 'الأحد - الخميس', 'en' => 'Sunday - Thursday']);
        $this->migrator->add('general.working_hours', ['ar' => 'من 8:00 صباحاً - 5:00 مساءً', 'en' => '8:00 AM - 5:00 PM']);

        $this->migrator->add('general.facebook_url', '#');
        $this->migrator->add('general.twitter_url', '#');
        $this->migrator->add('general.instagram_url', '#');
        $this->migrator->add('general.linkedin_url', '#');
        $this->migrator->add('general.youtube_url', '#');

        $this->migrator->add('general.seo_title', ['ar' => 'BloodBridge - إعدادات الموقع', 'en' => 'BloodBridge - Site Settings']);
        $this->migrator->add('general.seo_description', ['ar' => 'نظام ذكي يربط المتبرعين بالمحتاجين، مما يساعد في إنقاذ الأرواح قطرة قطرة.', 'en' => 'A smart system connecting donors with those in need, helping save lives drop by drop.']);
        $this->migrator->add('general.seo_keywords', ['ar' => 'تبرع بالدم, حياة, منظمة طبية, غزة', 'en' => 'blood donation, life, medical organization, Gaza']);

        $this->migrator->add('general.home_hero_title', ['ar' => 'مرحباً بك في موقعنا', 'en' => 'Welcome to our website']);
        $this->migrator->add('general.home_hero_subtitle', ['ar' => 'هذا هو النص الترحيبي الذي يظهر في مقدمة الصفحة.', 'en' => 'This is the welcome text that appears at the top of the page.']);
        $this->migrator->add('general.home_hero_image', null);

        $this->migrator->add('general.home_features_title', ['ar' => 'مميزاتنا', 'en' => 'Our Features']);
        $this->migrator->add('general.home_features_subtitle', ['ar' => 'اكتشف ما يجعلنا مميزين', 'en' => 'Discover what makes us special']);
        $this->migrator->add('general.home_features', [
            'ar' => [
                ['icon' => '📁', 'title' => 'سهل الاستخدام', 'text' => 'واجهة بسيطة وسهلة'],
                ['icon' => '📁', 'title' => 'آمن وموثوق', 'text' => 'حماية كاملة لبياناتك'],
                ['icon' => '📁', 'title' => 'دعم متواصل', 'text' => 'نحن هنا لمساعدتك دائماً'],
            ],
            'en' => [
                ['icon' => '📁', 'title' => 'Easy to Use', 'text' => 'Simple and easy interface'],
                ['icon' => '📁', 'title' => 'Secure and Reliable', 'text' => 'Complete protection for your data'],
                ['icon' => '📁', 'title' => 'Continuous Support', 'text' => 'We are always here to help you'],
            ],
        ]);

        $this->migrator->add('general.home_how_it_works_donor', [
            'ar' => [
                ['title' => 'سجل حسابك', 'text' => 'أنشئ حساباً بسهولة'],
                ['title' => 'ابحث عن طلب', 'text' => 'اعثر على طلب مناسب'],
                ['title' => 'تبرع وأنقذ حياة', 'text' => 'كن بطلاً لشخص ما'],
            ],
            'en' => [
                ['title' => 'Register Your Account', 'text' => 'Create an account easily'],
                ['title' => 'Find a Request', 'text' => 'Find a suitable request'],
                ['title' => 'Donate and Save a Life', 'text' => 'Be a hero to someone'],
            ],
        ]);

        $this->migrator->add('general.home_how_it_works_org', [
            'ar' => [
                ['title' => 'سجل منظمتك', 'text' => 'أنشئ حساب منظمة'],
                ['title' => 'أنشئ طلباً', 'text' => 'حدد احتياجاتك'],
                ['title' => 'تلقَّ التبرعات', 'text' => 'احصل على المساعدة'],
            ],
            'en' => [
                ['title' => 'Register Your Organization', 'text' => 'Create an organization account'],
                ['title' => 'Create a Request', 'text' => 'Specify your needs'],
                ['title' => 'Receive Donations', 'text' => 'Get the help you need'],
            ],
        ]);

        $this->migrator->add('general.home_cta_title', ['ar' => 'ابدأ رحلتك معنا اليوم', 'en' => 'Start your journey with us today']);
        $this->migrator->add('general.home_cta_subtitle', ['ar' => 'انضم إلى مجتمعنا وكن جزءاً من التغيير', 'en' => 'Join our community and be part of the change']);

        $this->migrator->add('general.about_hero_title', ['ar' => 'من نحن', 'en' => 'About Us']);
        $this->migrator->add('general.about_hero_subtitle', ['ar' => 'نحن منصة تربط بين المتبرعين بالدم والمنظمات الطبية لإنقاذ الأرواح', 'en' => 'We are a platform connecting blood donors and medical organizations to save lives']);

        $this->migrator->add('general.about_mission_title1', ['ar' => 'مهمتنا', 'en' => 'Our Mission']);
        $this->migrator->add('general.about_mission_text1', ['ar' => 'توفير منصة آمنة وسهلة لربط المتبرعين بالمنظمات الطبية', 'en' => 'Providing a safe and easy platform to connect donors with medical organizations']);
        $this->migrator->add('general.about_mission_image1', null);

        $this->migrator->add('general.about_mission_title2', ['ar' => 'رؤيتنا', 'en' => 'Our Vision']);
        $this->migrator->add('general.about_mission_text2', ['ar' => 'عالم لا يموت فيه أحد بسبب نقص الدم', 'en' => 'A world where no one dies due to blood shortage']);
        $this->migrator->add('general.about_mission_image2', null);

        $this->migrator->add('general.about_values_title', ['ar' => 'قيمنا الأساسية', 'en' => 'Our Core Values']);
        $this->migrator->add('general.about_values_subtitle', ['ar' => 'المبادئ التي نؤمن بها ونعمل بها', 'en' => 'The principles we believe in and work by']);
        $this->migrator->add('general.about_values', [
            'ar' => [
                ['title' => 'الشفافية', 'text' => 'نحن ملتزمون بالشفافية الكاملة في جميع عملياتنا', 'image' => null],
                ['title' => 'الأمان', 'text' => 'حماية بيانات المستخدمين هي أولويتنا القصوى', 'image' => null],
                ['title' => 'السرعة', 'text' => 'الاستجابة السريعة لطلبات التبرع العاجلة', 'image' => null],
                ['title' => 'التعاون', 'text' => 'نعمل مع جميع الأطراف لإنقاذ الأرواح', 'image' => null],
            ],
            'en' => [
                ['title' => 'Transparency', 'text' => 'We are committed to full transparency in all our operations', 'image' => null],
                ['title' => 'Security', 'text' => 'Protecting user data is our top priority', 'image' => null],
                ['title' => 'Speed', 'text' => 'Quick response to urgent donation requests', 'image' => null],
                ['title' => 'Collaboration', 'text' => 'We work with all parties to save lives', 'image' => null],
            ],
        ]);

        $this->migrator->add('general.about_team_title', ['ar' => 'تعرف على فريقنا', 'en' => 'Meet Our Team']);
        $this->migrator->add('general.about_team_subtitle', ['ar' => 'فريق متخصص من المحترفين الملتزمين بإنقاذ الأرواح', 'en' => 'A dedicated team of professionals committed to saving lives']);
        $this->migrator->add('general.about_team_members', [
            'ar' => [
                ['name' => 'أحمد محمود', 'role' => 'المدير التنفيذي', 'bio' => 'قيادة الفريق وتطوير الاستراتيجيات لتحقيق رؤية المنصة.', 'image' => null],
                ['name' => 'فاطمة أحمد', 'role' => 'مديرة العمليات', 'bio' => 'إدارة العمليات اليومية وضمان جودة الخدمة المقدمة.', 'image' => null],
                ['name' => 'محمد عبدالله', 'role' => 'مدير التقنية', 'bio' => 'تطوير وصيانة البنية التحتية التقنية للمنصة.', 'image' => null],
            ],
            'en' => [
                ['name' => 'Ahmed Mahmoud', 'role' => 'CEO', 'bio' => 'Leading the team and developing strategies to achieve the platform vision.', 'image' => null],
                ['name' => 'Fatima Ahmed', 'role' => 'Operations Manager', 'bio' => 'Managing daily operations and ensuring service quality.', 'image' => null],
                ['name' => 'Mohamed Abdullah', 'role' => 'CTO', 'bio' => 'Developing and maintaining the platform technical infrastructure.', 'image' => null],
            ],
        ]);

        $this->migrator->add('general.about_impact_title', ['ar' => 'تأثيرنا في المجتمع', 'en' => 'Our Impact in the Community']);
        $this->migrator->add('general.about_impact_text', ['ar' => 'نحن فخورون بالإنجازات التي حققناها في مساعدة المحتاجين وإنقاذ الأرواح', 'en' => 'We are proud of the achievements we have made in helping those in need and saving lives']);

        $this->migrator->add('general.about_join_title', ['ar' => 'كن جزءاً من التغيير', 'en' => 'Be Part of the Change']);
        $this->migrator->add('general.about_join_subtitle', ['ar' => 'انضم إلينا اليوم وكن جزءاً من مجتمع ينقذ الأرواح', 'en' => 'Join us today and be part of a community that saves lives']);

        $this->migrator->add('general.contact_hero_title', ['ar' => 'تواصل معنا', 'en' => 'Contact Us']);
        $this->migrator->add('general.contact_hero_subtitle', ['ar' => 'نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت', 'en' => 'We are here to answer your inquiries and help you at any time']);
        $this->migrator->add('general.contact_faqs', [
            'ar' => [
                ['question' => 'ما هي شروط التبرع؟', 'answer' => 'يجب أن يكون عمرك بين 18-65 سنة ووزنك أكثر من 50 كجم.'],
                ['question' => 'كم مرة يمكنني التبرع؟', 'answer' => 'يمكنك التبرع كل 3 أشهر.'],
                ['question' => 'هل التبرع آمن؟', 'answer' => 'نعم، التبرع آمن تماماً باستخدام معدات معقمة.'],
            ],
            'en' => [
                ['question' => 'What are the donation requirements?', 'answer' => 'You must be between 18-65 years old and weigh more than 50 kg.'],
                ['question' => 'How often can I donate?', 'answer' => 'You can donate every 3 months.'],
                ['question' => 'Is donation safe?', 'answer' => 'Yes, donation is completely safe using sterile equipment.'],
            ],
        ]);

        $this->migrator->add('general.login_title', ['ar' => 'مرحباً بعودتك', 'en' => 'Welcome Back']);
        $this->migrator->add('general.login_subtitle', ['ar' => 'قم بتسجيل الدخول للوصول إلى حسابك ومتابعة رحلتك في إنقاذ الأرواح.', 'en' => 'Log in to access your account and continue your journey in saving lives.']);
        $this->migrator->add('general.login_image', null);

        $this->migrator->add('general.signup_title', ['ar' => 'انضم إلينا اليوم', 'en' => 'Join Us Today']);
        $this->migrator->add('general.signup_subtitle', ['ar' => 'اختر نوع الحساب المناسب لك وابدأ رحلتك في إنقاذ الأرواح.', 'en' => 'Choose the right account type for you and start your journey in saving lives.']);

        $this->migrator->add('general.signup_donor_title', ['ar' => 'كن متبرعاً', 'en' => 'Become a Donor']);
        $this->migrator->add('general.signup_donor_subtitle', ['ar' => 'انقذ الأرواح بتبرعك', 'en' => 'Save lives with your donation']);
        $this->migrator->add('general.signup_donor_image', null);
        $this->migrator->add('general.signup_donor_tasks', [
            'ar' => [
                ['title' => 'تسجيل سريع وسهل', 'text' => 'أنشئ حسابك في دقائق'],
                ['title' => 'ابحث عن طلبات قريبة منك', 'text' => 'ساعد المحتاجين في منطقتك'],
                ['title' => 'تتبع تبرعاتك', 'text' => 'شاهد تاريخ تبرعاتك وتأثيرك'],
            ],
            'en' => [
                ['title' => 'Quick and Easy Registration', 'text' => 'Create your account in minutes'],
                ['title' => 'Find Nearby Requests', 'text' => 'Help those in need in your area'],
                ['title' => 'Track Your Donations', 'text' => 'See your donation history and impact'],
            ],
        ]);

        $this->migrator->add('general.signup_org_title', ['ar' => 'سجل منظمتك', 'en' => 'Register Your Organization']);
        $this->migrator->add('general.signup_org_subtitle', ['ar' => 'احصل على التبرعات التي تحتاجها', 'en' => 'Get the donations you need']);
        $this->migrator->add('general.signup_org_image', null);
        $this->migrator->add('general.signup_org_tasks', [
            'ar' => [
                ['title' => 'إنشاء طلبات التبرع', 'text' => 'اطلب الدم الذي تحتاجه منظمتك'],
                ['title' => 'إدارة المتبرعين', 'text' => 'تواصل مع المتبرعين المسجلين'],
                ['title' => 'تقارير مفصلة', 'text' => 'احصل على إحصائيات دقيقة لعملياتك'],
            ],
            'en' => [
                ['title' => 'Create Donation Requests', 'text' => 'Request the blood your organization needs'],
                ['title' => 'Manage Donors', 'text' => 'Communicate with registered donors'],
                ['title' => 'Detailed Reports', 'text' => 'Get accurate statistics for your operations'],
            ],
        ]);

        $this->migrator->add('general.donor_register_title', ['ar' => 'سجل كمتبرع', 'en' => 'Register as a Donor']);
        $this->migrator->add('general.donor_register_subtitle', ['ar' => 'املأ البيانات التالية لإنشاء حسابك كمتبرع وابدأ في إنقاذ الأرواح.', 'en' => 'Fill in the following details to create your donor account and start saving lives.']);
        $this->migrator->add('general.donor_register_image', null);

        $this->migrator->add('general.org_register_title', ['ar' => 'سجل منظمتك', 'en' => 'Register Your Organization']);
        $this->migrator->add('general.org_register_subtitle', ['ar' => 'املأ بيانات منظمتك للانضمام إلى شبكتنا والحصول على التبرعات التي تحتاجها.', 'en' => 'Fill in your organization details to join our network and get the donations you need.']);
        $this->migrator->add('general.org_register_image', null);

        $this->migrator->add('general.min_donor_age', 18);
        $this->migrator->add('general.max_donor_age', 65);
        $this->migrator->add('general.min_donor_weight', 50);
        $this->migrator->add('general.min_days_between_donations', 90);
        $this->migrator->add('general.min_donor_height', 140);
        $this->migrator->add('general.min_days_after_surgery', 28);
        $this->migrator->add('general.org_max_requests_per_day', 5);

        $this->migrator->add('general.enable_contact_messages', true);
    }
};
