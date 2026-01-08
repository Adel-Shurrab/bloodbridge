<x-filament-widgets::widget>
    <div class="custom-dashboard-header">
        <div class="header-content">
            {{-- Left side: Profile and Notifications --}}
            <div class="header-left">
                {{-- Profile Section --}}
                <div class="profile-box">
                    <div class="profile-text">
                        <span class="profile-name">{{ auth()->user()->name }}</span>
                        <span class="profile-role">
                            {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'مسؤول النظام' : 'مؤسسة' }}
                        </span>
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
                    مرحباً بعودتك، {{ auth()->user()->name }}. إليك ما يحدث مع <span class="brand-text">BloodBridge</span>.
                </p>
            </div>
        </div>
    </div>

    <style>
        .custom-dashboard-header {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
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
            font-size: 1.75rem;
            font-weight: 900;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
            line-height: 1;
        }
        .dark .dashboard-title {
            color: #f9fafb;
        }
        .welcome-text {
            color: #9ca3af;
            font-size: 1rem;
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
            gap: 1rem;
        }
        .profile-box {
            background: #fff5f5;
            padding: 0.5rem 1rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            line-height: 1.1;
        }
        .profile-name {
            font-weight: 800;
            color: #1f2937;
            font-size: 1rem;
            display: block;
        }
        .dark .profile-name {
            color: #f3f4f6;
        }
        .profile-role {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            display: block;
        }
        .dark .profile-role {
            color: #9ca3af;
        }
        .profile-avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dark .profile-avatar {
            border-color: #1f2937;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bell-container {
            position: relative;
        }
        .bell-button {
            background: #fff5f5;
            border: 1px solid #fee2e2;
            border-radius: 1rem;
            width: 3rem;
            height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.3s;
        }
        .dark .bell-button {
            background: #1f1212;
            border-color: #450a0a;
        }
        .bell-button:hover {
            transform: scale(1.05);
        }
        .bell-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #fca5a5;
        }
        .bell-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            border-radius: 50%;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }
        .dark .bell-badge {
            border-color: #111827;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1.5rem;
            }
            .header-right {
                text-align: center;
            }
        }
    </style>
</x-filament-widgets::widget>