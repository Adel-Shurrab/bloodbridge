<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>عن جسر الدم</h4>
                <p>نظام ذكي يربط المتبرعين بالمحتاجين، مما يساعد في إنقاذ الأرواح قطرة قطرة.</p>
                <div class="social-links">
                    <a href="#" aria-label="فيسبوك" title="تابعنا على فيسبوك"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="تويتر" title="تابعنا على تويتر"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="إنستجرام" title="تابعنا على إنستجرام"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="لينكدإن" title="تابعنا على لينكدإن"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h4>الروابط السريعة</h4>
                <ul>
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    <li><a href="{{ route('about') }}">من نحن</a></li>
                    <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
                    <li><a href="{{ route('privacy') }}">سياسة الخصوصية</a></li>
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
            <p>&copy; {{ date('Y') }} BloodBridge. جميع الحقوق محفوظة.</p>
            <p>إنقاذ الأرواح، قطرة واحدة في كل مرة 🩸</p>
        </div>
    </div>
</footer>