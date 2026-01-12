<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>عن {{ $settings->site_name }}</h4>
                <p>{{ $settings->seo_description }}</p>
                <div class="social-links">
                    @if($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" aria-label="فيسبوك" title="تابعنا على فيسبوك"><i
                                class="fab fa-facebook"></i></a>
                    @endif
                    @if($settings->twitter_url)
                        <a href="{{ $settings->twitter_url }}" aria-label="تويتر" title="تابعنا على تويتر"><i
                                class="fab fa-twitter"></i></a>
                    @endif
                    @if($settings->instagram_url)
                        <a href="{{ $settings->instagram_url }}" aria-label="إنستجرام" title="تابعنا على إنستجرام"><i
                                class="fab fa-instagram"></i></a>
                    @endif
                    @if($settings->linkedin_url)
                        <a href="{{ $settings->linkedin_url }}" aria-label="لينكدإن" title="تابعنا على لينكدإن"><i
                                class="fab fa-linkedin"></i></a>
                    @endif
                </div>
            </div>

            <div class="footer-section">
                <h4>الروابط السريعة</h4>
                <ul>
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('about') }}">من نحن</a></li>
                    <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
                    <li><a href="javascript:void(0)" @click.prevent="$dispatch('open-modal', 'privacyModal')">سياسة
                            الخصوصية</a></li>
                    <li><a href="#">شروط الخدمة</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>للمتبرعين</h4>
                <ul>
                    <li><a href="{{ route('register.donor') }}">سجل كمتبرع</a></li>
                    <li><a href="#">متطلبات الأهلية</a></li>
                    <li><a href="#">الأسئلة الشائعة</a></li>
                    <li><a href="#">مركز المساعدة</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>للمنظمات</h4>
                <ul>
                    <li><a href="{{ route('register.selection') }}">سجل كمنظمة</a></li>
                    <li><a href="#">الشراكات</a></li>
                    <li><a href="#">الدعم الفني</a></li>
                    <li><a href="#">الموارد</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $settings->site_name }}. جميع الحقوق محفوظة.</p>
            <p>{{ $settings->site_slogan }} 🩸</p>
        </div>
    </div>
</footer>