<x-filament-widgets::widget>
    <div class="custom-dashboard-header">
        <div class="header-content">
            {{-- Left side: Profile --}}
            <div class="header-left">
                <div class="profile-box">
                    <div class="profile-text">
                        <span class="profile-name">{{ auth()->user()->name }}</span>
                        <span class="profile-role">متبرع</span>
                    </div>
                    <div class="profile-avatar">
                        <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="Profile">
                    </div>
                </div>
            </div>

            {{-- Right side: Title and Welcome --}}
            <div class="header-right">
                <h1 class="dashboard-title">لوحة التحكم</h1>
                <p class="welcome-text">
                    مرحباً بك يا <span class="brand-text">{{ auth()->user()->name }}</span>. شكراً لمساهمتك في إنقاذ
                    الأرواح.
                </p>
            </div>
        </div>
    </div>

    <style>
        .custom-dashboard-header {
            background: #ffffff;
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            width: 100%;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .dark .custom-dashboard-header {
            background: #111827;
            border: 1px solid #1f2937;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            direction: rtl;
        }

        .header-right {
            text-align: right;
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1f2937;
            margin: 0 0 0.5rem 0;
            line-height: 1;
        }

        .dark .dashboard-title {
            color: #f9fafb;
        }

        .welcome-text {
            color: #9ca3af;
            font-size: 1.25rem;
            font-weight: 500;
            margin: 0;
        }

        .dark .welcome-text {
            color: #6b7280;
        }

        .brand-text {
            color: #ef4444;
            font-weight: 700;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .profile-box {
            background: #fff5f5;
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid #fee2e2;
        }

        .dark .profile-box {
            background: #1f1212;
            border-color: #450a0a;
        }

        .profile-text {
            display: flex;
            flex-direction: column;
            text-align: right;
            line-height: 1.2;
        }

        .profile-name {
            font-weight: 800;
            color: #1f2937;
            font-size: 1.1rem;
            display: block;
        }

        .dark .profile-name {
            color: #f3f4f6;
        }

        .profile-role {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 600;
            display: block;
        }

        .dark .profile-role {
            color: #9ca3af;
        }

        .profile-avatar {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dark .profile-avatar {
            border-color: #1f2937;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 2rem;
            }

            .header-right {
                text-align: center;
            }
        }
    </style>
</x-filament-widgets::widget>