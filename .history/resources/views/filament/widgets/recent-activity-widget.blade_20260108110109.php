<x-filament-widgets::widget>
    <div class="recent-activity-card">
        <div class="card-header">
            <h2 class="card-title">الأنشطة الأخيرة</h2>
        </div>

        <div class="timeline-container">
            <div class="timeline-line"></div>

            <div class="activities-list">
                @foreach($this->getActivities() as $activity)
                    <div class="activity-item">
                        <div class="activity-content">
                            <h3 class="activity-text">{{ $activity['title'] }}</h3>
                            <span class="activity-time">{{ $activity['time'] }}</span>
                        </div>
                        <div class="activity-icon-box {{ $activity['color'] }}">
                            <x-filament::icon :icon="$activity['icon']" class="activity-icon" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer">
            <button class="view-all-btn" wire:click="showMore" wire:loading.attr="disabled">
                <span wire:loading.remove>عرض المزيد من الأنشطة</span>
                <span wire:loading>جاري التحميل...</span>
            </button>
        </div>
    </div>

    <style>
        .recent-activity-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            direction: rtl;
        }

        .dark .recent-activity-card {
            background: #111827;
            border: 1px solid #1f2937;
        }

        .card-header {
            margin-bottom: 2rem;
            text-align: right;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
        }

        .dark .card-title {
            color: #f3f4f6;
        }

        .timeline-container {
            position: relative;
            flex-grow: 1;
            padding-right: 1.5rem;
        }

        .timeline-line {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #ef4444, #fca5a5, transparent);
            border-radius: 3px;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .activity-item {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1.5rem;
            position: relative;
        }

        .activity-content {
            text-align: right;
            flex-grow: 1;
        }

        .activity-text {
            font-size: 1rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .dark .activity-text {
            color: #d1d5db;
        }

        .activity-time {
            font-size: 0.85rem;
            color: #9ca3af;
            font-weight: 600;
        }

        .activity-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            z-index: 1;
        }

        .activity-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Colors */
        .activity-icon-box.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .activity-icon-box.emerald {
            background: #ecfdf5;
            color: #10b981;
        }

        .activity-icon-box.orange {
            background: #fff7ed;
            color: #f97316;
        }

        .activity-icon-box.red {
            background: #fef2f2;
            color: #ef4444;
        }

        .activity-icon-box.pink {
            background: #fdf2f8;
            color: #ec4899;
        }

        .dark .activity-icon-box {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .card-footer {
            margin-top: 2.5rem;
        }

        .view-all-btn {
            width: 100%;
            padding: 1rem;
            background: transparent;
            border: 2px solid #ef4444;
            color: #ef4444;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
        }

        .view-all-btn:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }
    </style>
</x-filament-widgets::widget>