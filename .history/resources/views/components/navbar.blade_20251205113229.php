<nav class="navbar" id="navbar" role="navigation" aria-label="الملاحة الرئيسية">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="logo" aria-label="BloodBridge - الصفحة الرئيسية">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="BloodBridge Logo" class="logo-icon" />
            <span>BloodBridge</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}" aria-current="page">الرئيسية</a></li>
            <li><a href="{{ route('home') }}#features">المميزات</a></li>
            <li><a href="{{ route('home') }}#how-it-works">كيف يعمل</a></li>
            <li><a href="{{ route('about') }}">من نحن</a></li>
            <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
        </ul>
        <div class="nav-buttons">
            @auth
                <a href="{{ url('/admin') }}" class="btn btn-primary">لوحة التحكم</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline">تسجيل الخروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">تسجيل الدخول</a>
                <a href="{{ rou }}" class="btn btn-primary">حساب جديد</a>
            @endauth
        </div>
        <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="فتح القائمة" aria-expanded="false" aria-controls="mobile-nav">☰</button>
    </div>
</nav>

<div class="mobile-nav" id="mobile-nav" aria-hidden="true">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="إغلاق القائمة">×</button>
    <ul class="mobile-nav-links">
        <li><a href="{{ route('home') }}">الرئيسية</a></li>
        <li><a href="{{ route('home') }}#features">المميزات</a></li>
        <li><a href="{{ route('home') }}#how-it-works">كيف يعمل</a></li>
        <li><a href="{{ route('about') }}">من نحن</a></li>
        <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
    </ul>
    <div class="mobile-nav-buttons">
        @auth
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">لوحة التحكم</a>
            <form method="POST" action="{{ route('logout') }}" style="display: block; width: 100%;">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%;">تسجيل الخروج</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline">تسجيل الدخول</a>
            <a href="" class="btn btn-primary">حساب جديد</a>
        @endauth
    </div>
</div>