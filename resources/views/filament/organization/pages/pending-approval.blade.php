<x-filament-panels::page>
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $isRejected = $tenant && $tenant->approval_status === \App\Enums\OrganizationStatus::REJECTED;
        $statusColor = $isRejected ? 'danger' : 'warning';
        $icon = $isRejected ? 'heroicon-o-x-circle' : 'heroicon-o-clock';
        $heading = $isRejected ? __('organization.organization_request_rejected') : __('organization.pending_approval');
        $description = $isRejected
            ? __('organization.pending_rejected_description')
            : __('organization.pending_approval_description');
    @endphp

    <div class="pending-page-container">
        <div class="pending-content-wrapper">
            
            {{-- Main Card --}}
            <div class="pending-main-card">
                <div class="pending-card-content">
                    
                    {{-- Status Icon with Animation --}}
                    <div class="status-icon-container">
                        <div class="status-icon-wrapper">
                            {{-- Pulsing Background --}}
                            @if(!$isRejected)
                                <div class="status-icon-ping bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900/30 animate-ping"></div>
                            @endif
                            
                            {{-- Icon Container --}}
                            <div class="status-icon-circle bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900/50 ring-{{ $statusColor }}-50 dark:ring-{{ $statusColor }}-950">
                                <x-filament::icon 
                                    :icon="$icon"
                                    class="w-10 h-10 text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400"
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Organization Name --}}
                    <h1 class="org-name">
                        {{ $tenant->localized_org_name }}
                    </h1>

                    {{-- Status Heading --}}
                    <div class="status-badge-container">
                        <div class="status-badge {{ $isRejected ? 'danger' : 'warning' }}">
                            <div class="status-dot {{ $isRejected ? 'danger' : 'warning' }} {{ $isRejected ? '' : 'animate-pulse' }}"></div>
                            <span class="status-badge-text {{ $isRejected ? 'danger' : 'warning' }}">
                                {{ $heading }}
                            </span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p class="page-description">
                        {{ $description }}
                    </p>

                    {{-- Progress Steps (Only for Pending) --}}
                    @if(!$isRejected)
                        <div class="progress-container">
                            <div class="progress-wrapper">
                                {{-- Horizontal Line --}}
                                <div class="progress-line"></div>
                                
                                <div class="progress-steps">
                                    
                                    {{-- Step 1: Completed --}}
                                    <div class="progress-step">
                                        <div class="step-icon-container">
                                            <div class="step-icon completed shadow-success-500/30">
                                                <x-filament::icon 
                                                    icon="heroicon-m-check" 
                                                    class="w-5 h-5 text-white"
                                                />
                                            </div>
                                        </div>
                                        <div class="step-content">
                                            <h3 class="step-title">
                                                {{ __('organization.submit_registration_request') }}
                                            </h3>
                                            <p class="step-description">
                                                {{ __('organization.data_documents_received_successfully') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Step 2: In Progress --}}
                                    <div class="progress-step">
                                        <div class="step-icon-container">
                                            <div class="step-icon active shadow-success-500/30 animate-pulse">
                                                <x-filament::loading-indicator 
                                                    class="w-5 h-5 text-white"
                                                />
                                            </div>
                                        </div>
                                        <div class="step-content">
                                            <h3 class="step-title">
                                                {{ __('organization.review_documents_and_data') }}
                                            </h3>
                                            <p class="step-description active">
                                                {{ __('organization.verification_in_progress') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Step 3: Pending --}}
                                    <div class="progress-step opacity-60">
                                        <div class="step-icon-container">
                                            <div class="step-icon pending">
                                                <div class="w-3 h-3 rounded-full bg-gray-400 dark:bg-gray-500"></div>
                                            </div>
                                        </div>
                                        <div class="step-content">
                                            <h3 class="step-title">
                                                {{ __('organization.account_activation') }}
                                            </h3>
                                            <p class="step-description">
                                                {{ __('organization.you_will_be_notified_when_review_complete') }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="info-box">
                            <div class="info-box-content">
                                <x-filament::icon 
                                    icon="heroicon-m-information-circle" 
                                    class="info-icon blue"
                                />
                                <div class="info-text">
                                    <p class="info-title">{{ __('organization.what_happens_now') }}</p>
                                    <p class="info-description">
                                        {{ __('organization.team_reviewing_information') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Rejection Reasons Box --}}
                        <div class="info-box danger">
                            <div class="info-box-content">
                                <x-filament::icon 
                                    icon="heroicon-m-exclamation-triangle" 
                                    class="info-icon danger"
                                />
                                <div class="info-text">
                                    <p class="info-title danger">{{ __('organization.why_request_rejected') }}</p>
                                    <p class="info-description danger">
                                        {{ __('organization.reason_might_be_incomplete_data') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="button-container">
                        
                        <a href="mailto:support@bloodbridge.ps" class="btn btn-gray">
                            <x-filament::icon icon="heroicon-m-lifebuoy" class="w-5 h-5" />
                            {{ __('organization.contact_support') }}
                        </a>

                        <form action="{{ route('filament.organization.auth.logout') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger-outline">
                                <x-filament::icon icon="heroicon-m-arrow-left-on-rectangle" class="w-5 h-5" />
                                {{ __('organization.log_out') }}
                            </button>
                        </form>
                        
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <p class="page-footer">
                {{ __('organization.all_rights_reserved') }} &copy; {{ date('Y') }} {{ __('organization.organization_portal') }}
            </p>

        </div>
    </div>

    {{-- Custom Styles --}}
    <style>
        /* Animations */
        @keyframes ping {
            75%, 100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-ping {
            animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Page Container */
        .pending-page-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            
            overflow: hidden;
            padding: 1rem 0;
        }
        
        .pending-content-wrapper {
            width: 100%;
            max-width: 42rem;
            padding: 0 1rem;
            max-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* Main Card */
        .pending-main-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            overflow: hidden;
        }
        
        .dark .pending-main-card {
            background: rgb(24 24 27);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.3);
        }
        
        .pending-card-content {
            padding: 1.5rem 2rem 1.25rem;
        }
        
        @media (min-width: 768px) {
            .pending-card-content {
                padding: 2rem 2.5rem 1.5rem;
            }
        }
        
        /* Status Icon Container */
        .status-icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .status-icon-wrapper {
            position: relative;
        }
        
        .status-icon-ping {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            opacity: 0.75;
        }
        
        .status-icon-circle {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            border-radius: 9999px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        /* Warning Colors */
        .bg-warning-100 {
            background-color: rgb(254 243 199);
        }
        
        .dark .bg-warning-900\/50 {
            background-color: rgba(120 53 15 / 0.5);
        }
        
        .dark .bg-warning-950 {
            background-color: rgb(66 32 6);
        }
        
        .ring-warning-50 {
            ring-color: rgb(255 251 235);
            box-shadow: 0 0 0 4px rgb(255 251 235);
        }
        
        .dark .ring-warning-950 {
            box-shadow: 0 0 0 4px rgb(66 32 6);
        }
        
        .text-warning-600 {
            color: rgb(217 119 6);
        }
        
        .dark .text-warning-400 {
            color: rgb(251 191 36);
        }
        
        /* Danger Colors */
        .bg-danger-100 {
            background-color: rgb(254 226 226);
        }
        
        .dark .bg-danger-900\/50 {
            background-color: rgba(127 29 29 / 0.5);
        }
        
        .ring-danger-50 {
            box-shadow: 0 0 0 4px rgb(254 242 242);
        }
        
        .dark .ring-danger-950 {
            box-shadow: 0 0 0 4px rgb(69 10 10);
        }
        
        .text-danger-600 {
            color: rgb(220 38 38);
        }
        
        .dark .text-danger-400 {
            color: rgb(248 113 113);
        }
        
        /* Success Colors */
        .bg-success-500 {
            background-color: rgb(34 197 94);
        }
        
        .dark .bg-success-600 {
            background-color: rgb(22 163 74);
        }
        
        .shadow-success-500\/30 {
            box-shadow: 0 10px 15px -3px rgba(34 197 94 / 0.3), 0 4px 6px -4px rgba(34 197 94 / 0.3);
        }
        
        /* Typography */
        .org-name {
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
            color: rgb(17 24 39);
        }
        
        .dark .org-name {
            color: white;
        }
        
        /* Status Badge */
        .status-badge-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        
        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border-width: 1px;
        }
        
        .status-badge.warning {
            background-color: rgb(255 251 235);
            border-color: rgb(254 240 138);
        }
        
        .dark .status-badge.warning {
            background-color: rgba(120 53 15 / 0.3);
            border-color: rgb(133 77 14);
        }
        
        .status-badge.danger {
            background-color: rgb(254 242 242);
            border-color: rgb(254 202 202);
        }
        
        .dark .status-badge.danger {
            background-color: rgba(127 29 29 / 0.3);
            border-color: rgb(153 27 27);
        }
        
        .status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
        }
        
        .status-dot.warning {
            background-color: rgb(217 119 6);
        }
        
        .status-badge-text {
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
        }
        
        .status-badge-text.warning {
            color: rgb(180 83 9);
        }
        
        .dark .status-badge-text.warning {
            color: rgb(251 191 36);
        }
        
        .status-badge-text.danger {
            color: rgb(185 28 28);
        }
        
        .dark .status-badge-text.danger {
            color: rgb(252 165 165);
        }
        
        /* Description */
        .page-description {
            text-align: center;
            color: rgb(75 85 99);
            line-height: 1.5rem;
            margin-bottom: 1.25rem;
            max-width: 36rem;
            margin-inline-end: auto;
            margin-inline-start: auto;
            font-size: 0.875rem;
        }
        
        .dark .page-description {
            color: rgb(156 163 175);
        }
        
        /* Progress Steps */
        .progress-container {
            margin-bottom: 1.25rem;
        }
        
        .progress-wrapper {
            position: relative;
        }
        
        .progress-line {
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: linear-gradient(to left, rgb(34 197 94), rgb(217 119 6), rgb(209 213 219));
        }
        
        .dark .progress-line {
            background: linear-gradient(to left, rgb(34 197 94), rgb(251 191 36), rgb(75 85 99));
        }
        
        .progress-steps {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            position: relative;
            padding: 0 1rem;
        }
        
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            text-align: center;
        }
        
        .step-icon-container {
            flex-shrink: 0;
            position: relative;
            z-index: 10;
            margin-bottom: 0.75rem;
        }
        
        .step-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
        }
        
        .step-icon.completed {
            background-color: rgb(34 197 94);
        }
        
        .dark .step-icon.completed {
            background-color: rgb(22 163 74);
        }
        
        .step-icon.active {
            background-color: rgb(217 119 6);
        }
        
        .dark .step-icon.active {
            background-color: rgb(202 138 4);
        }
        
        .step-icon.pending {
            background-color: rgb(229 231 235);
            border: 2px solid rgb(209 213 219);
        }
        
        .dark .step-icon.pending {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-size: 0.8125rem;
            line-height: 1.125rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin-bottom: 0.25rem;
        }
        
        .dark .step-title {
            color: white;
        }
        
        .step-description {
            font-size: 0.75rem;
            line-height: 1rem;
            color: rgb(75 85 99);
        }
        
        .dark .step-description {
            color: rgb(156 163 175);
        }
        
        .step-description.active {
            color: rgb(217 119 6);
            font-weight: 500;
        }
        
        .dark .step-description.active {
            color: rgb(251 191 36);
        }
        
        /* Info Box */
        .info-box {
            margin-bottom: 1.25rem;
            padding: 0.875rem;
            border-radius: 0.75rem;
            border: 1px solid rgb(191 219 254);
            background-color: rgb(239 246 255);
        }
        
        .dark .info-box {
            background-color: rgba(30 58 138 / 0.3);
            border-color: rgb(30 64 175);
        }
        
        .info-box.danger {
            background-color: rgb(254 242 242);
            border: 2px solid rgb(254 202 202);
        }
        
        .dark .info-box.danger {
            background-color: rgba(127 29 29 / 0.3);
            border-color: rgb(153 27 27);
        }
        
        .info-box-content {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
        }
        
        .info-icon {
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }
        
        .info-icon.blue {
            color: rgb(37 99 235);
        }
        
        .dark .info-icon.blue {
            color: rgb(96 165 250);
        }
        
        .info-icon.danger {
            color: rgb(220 38 38);
            width: 1.5rem;
            height: 1.5rem;
        }
        
        .dark .info-icon.danger {
            color: rgb(248 113 113);
        }
        
        .info-text {
            font-size: 0.8125rem;
            line-height: 1.125rem;
        }
        
        .info-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: rgb(30 64 175);
        }
        
        .dark .info-title {
            color: rgb(147 197 253);
        }
        
        .info-title.danger {
            color: rgb(153 27 27);
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 0.375rem;
        }
        
        .dark .info-title.danger {
            color: rgb(254 202 202);
        }
        
        .info-description {
            color: rgb(29 78 216);
            line-height: 1.375rem;
        }
        
        .dark .info-description {
            color: rgb(96 165 250);
        }
        
        .info-description.danger {
            color: rgb(185 28 28);
            line-height: 1.5rem;
        }
        
        .dark .info-description.danger {
            color: rgb(252 165 165);
        }
        
        /* Buttons */
        .button-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: 1px solid transparent;
            white-space: nowrap;
            cursor: pointer;
        }
        
        .btn-gray {
            background-color: rgb(249 250 251);
            color: rgb(55 65 81);
            border-color: rgb(209 213 219);
        }
        
        .btn-gray:hover {
            background-color: rgb(243 244 246);
        }
        
        .dark .btn-gray {
            background-color: rgb(31 41 55);
            color: rgb(209 213 219);
            border-color: rgb(55 65 81);
        }
        
        .dark .btn-gray:hover {
            background-color: rgb(55 65 81);
        }
        
        .btn-danger-outline {
            background-color: transparent;
            color: rgb(220 38 38);
            border-color: rgb(220 38 38);
        }
        
        .btn-danger-outline:hover {
            background-color: rgb(254 242 242);
        }
        
        .dark .btn-danger-outline {
            color: rgb(248 113 113);
            border-color: rgb(153 27 27);
        }
        
        .dark .btn-danger-outline:hover {
            background-color: rgba(127 29 29 / 0.3);
        }
        
        /* Footer */
        .page-footer {
            text-align: center;
            font-size: 0.75rem;
            line-height: 1rem;
            color: rgb(107 114 128);
            margin-top: 0.75rem;
        }
        
        .dark .page-footer {
            color: rgb(107 114 128);
        }
        
        /* Utility Classes */
        .opacity-60 {
            opacity: 0.6;
        }
    </style>

</x-filament-panels::page>
