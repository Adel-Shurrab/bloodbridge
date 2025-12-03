<nav class="navbar" id="navbar">
    <div class="nav-container">
        <div class="logo">
            <div class="logo-icon">🩸</div>
            BloodBridge
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}">الرئيسية</a></li>
            <li><a href="{{ route('home') }}#features">المميزات</a></li>
            <li><a href="{{ route('home') }}#how-it-works">كيف يعمل</a></li>
            <li><a href="{{ route('about') }}">من نحن</a></li>
            <li><a href="{{ route('contact') }}">اتصل بنا</a></li>
        </ul>
        <div class="nav-buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">لوحة التحكم</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">تسجيل الدخول</a>
                <a href="{{ route('register.selection') }}" class="btn btn-primary">حساب جديد</a>
            @endauth
        </div>
        <button class="mobile-menu-btn" id="mobile-menu-btn">☰</button>
    </div>
</nav>

<div class="mobile-nav" id="mobile-nav">
    <button class="mobile-menu-close" id="mobile-menu-close">×</button>
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
        @else
            <a href="{{ route('login') }}" class="btn btn-outline">تسجيل الدخول</a>
            <a href="{{ route('register.selection') }}" class="btn btn-primary">حساب جديد</a>
        @endauth
    </div>
</div>