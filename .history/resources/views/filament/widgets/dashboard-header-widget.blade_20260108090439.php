<x-filament-widgets::widget>
    <div class="top-bar">
        <div class="top-bar-title">
            <h1>لوحة التحكم</h1>
            <p class="top-bar-subtitle">
                مرحباً بعودتك، {{ auth()->user()->name }}. إليك ما يحدث مع <span class="brand-text">BloodBridge</span>.
            </p>
        </div>
    

            {{-- Profile Section --}}
            <div class="profile-section">
                <div class="profile-info">
                    <span class="profile-name">{{ auth()->user()->name }}</span>
                    <span class="profile-role">
                        {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'مسؤول النظام' : 'مؤسسة' }}
                    </span>
                </div>
                <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="Profile" class="profile-img">
            </div>
        </div>
    </div>

    <style>
        .top-bar {
            background: #ffffff;
            padding: 1.5rem 2rem;
            border-radius: 24px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(211, 47, 47, 0.05);
            direction: rtl;
            transition: all 0.3s;
        }
        .dark .top-bar {
            background: #111827;
            border-color: #1f2937;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
        }
        .top-bar:hover {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        }
        .top-bar-title {
            text-align: right;
        }
        .top-bar-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        .dark .top-bar-title h1 {
            color: #f9fafb;
        }
        .top-bar-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }
        .brand-text {
            color: #d32f2f;
            font-weight: 700;
        }
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .notification-btn {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 1px solid rgba(211, 47, 47, 0.1);
            width: 45px;
            height: 45px;
            border-radius: 14px;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0; outline: none;
        }
        .dark .notification-btn {
            background: linear-gradient(135deg, #1f1212, #450a0a);
            border-color: #450a0a;
        }
        .notification-btn:hover {
            transform: scale(1.05) rotate(-5deg);
        }
        .notification-icon-svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #d32f2f;
        }
        .notification-badge {
            position: absolute;
            top: -6px;
            left: -6px;
            background: #ef4444;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }
        .dark .notification-badge {
            border-color: #111827;
        }
        .profile-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-radius: 16px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .dark .profile-section {
            background: linear-gradient(135deg, #1f1212, #450a0a);
        }
        .profile-section:hover {
            transform: translateX(-5px);
        }
        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.2;
        }
        .profile-name {
            font-weight: 700;
            color: #2d3748;
            font-size: 0.95rem;
        }
        .dark .profile-name {
            color: #f3f4f6;
        }
        .profile-role {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 600;
        }
        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #d32f2f;
            box-shadow: 0 4px 10px rgba(211, 47, 47, 0.2);
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem;
            }
            .top-bar-title {
                text-align: center;
            }
            .profile-info {
                align-items: center;
            }
        }
    </style>
</x-filament-widgets::widget>