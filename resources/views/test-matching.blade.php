<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار خوارزمية المطابقة - BloodBridge</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .search-params {
            background: #f8fafc;
            padding: 2rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .search-params h2 {
            color: #1e293b;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .param-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .param-item {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        .param-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .param-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            background: #f1f5f9;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #be123c;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .donors-list {
            padding: 2rem;
        }

        .donors-list h2 {
            color: #1e293b;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .donor-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .donor-card:hover {
            border-color: #be123c;
            box-shadow: 0 4px 12px rgba(190, 18, 60, 0.1);
        }

        .donor-card.matched {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .donor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .donor-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .donor-distance {
            background: #be123c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
        }

        .donor-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .form-container {
            padding: 2rem;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e293b;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .btn {
            background: #be123c;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #9f1239;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔍 اختبار خوارزمية مطابقة المتبرعين</h1>
            <p>اختبر كيف يعمل نظام البحث عن المتبرعين المتوافقين</p>
        </div>

        <div class="search-params">
            <h2>معايير البحث الحالية</h2>
            <div class="param-grid">
                <div class="param-item">
                    <div class="param-label">خط العرض (Latitude)</div>
                    <div class="param-value">{{ $searchLat }}</div>
                </div>
                <div class="param-item">
                    <div class="param-label">خط الطول (Longitude)</div>
                    <div class="param-value">{{ $searchLng }}</div>
                </div>
                <div class="param-item">
                    <div class="param-label">نصف القطر (كم)</div>
                    <div class="param-value">{{ $radiusKm }} كم</div>
                </div>
                <div class="param-item">
                    <div class="param-label">المحافظة (للمطابقة البديلة)</div>
                    <div class="param-value">
                        @if($selectedGovernorateId && $selectedGov = $allGovernorates->find($selectedGovernorateId))
                            {{ $selectedGov->name }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="param-item">
                    <div class="param-label">فصيلة الدم المطلوبة</div>
                    <div class="param-value">{{ $bloodTypeLabel }}</div>
                </div>
                <div class="param-item">
                    <div class="param-label">الفصائل المتوافقة</div>
                    <div class="param-value">{{ implode(', ', $compatibleTypes) }}</div>
                </div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ $totalDonorsInRadius }}</div>
                <div class="stat-label">متبرعين في نطاق البحث</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $compatibleDonorsCount }}</div>
                <div class="stat-label">متبرعين الفصيلة متوافقة</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $eligibleDonorsCount }}</div>
                <div class="stat-label">متبرعين مؤهلين نهائياً</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    {{ $eligibleDonorsCount > 0 ? round(($eligibleDonorsCount / $totalDonorsInRadius) * 100, 1) : 0 }}%
                </div>
                <div class="stat-label">نسبة التطابق</div>
            </div>
        </div>

        <div class="donors-list">
            <h2>المتبرعين في نطاق البحث ({{ $donors->count() }})</h2>
            @forelse($donors as $donor)
                <div class="donor-card {{ $donor['is_final_match'] ? 'matched' : '' }}">
                    <div class="donor-header">
                        <div class="donor-name">
                            {{ $donor['name'] }}
                            @if($donor['is_final_match'])
                                <span class="badge badge-success">✓ مطابق</span>
                            @endif
                        </div>
                        @if($donor['distance_km'])
                            <div class="donor-distance">📍 {{ $donor['distance_km'] }} كم</div>
                        @endif
                    </div>
                    <div class="donor-info">
                        <div class="info-item">
                            <span class="info-label">📱 الهاتف:</span>
                            <span class="info-value">{{ $donor['phone'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">📧 البريد:</span>
                            <span class="info-value">{{ $donor['email'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">🆔 الهوية:</span>
                            <span class="info-value">{{ $donor['national_id'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">👤 الجنس:</span>
                            <span class="info-value">{{ $donor['gender'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">فصيلة الدم:</span>
                            <span class="info-value">{{ $donor['blood_type'] }}</span>
                            @if($donor['is_compatible'])
                                <span class="badge badge-success">متوافق</span>
                            @else
                                <span class="badge badge-danger">غير متوافق</span>
                            @endif
                        </div>
                        <div class="info-item">
                            <span class="info-label">المحافظة:</span>
                            <span class="info-value">{{ $donor['governorate'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الأهلية:</span>
                            @if($donor['is_eligible'])
                                <span class="badge badge-success">مؤهل</span>
                            @else
                                <span class="badge badge-danger">غير مؤهل</span>
                            @endif
                        </div>
                        @if($donor['has_permanent_exclusion'])
                            <div class="info-item">
                                <span class="badge badge-danger">⚠️ لديه استبعاد دائم</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #64748b; padding: 2rem;">
                    لا يوجد متبرعين في نطاق البحث
                </p>
            @endforelse
        </div>

        <div class="form-container">
            <h2 style="margin-bottom: 1.5rem; color: #1e293b;">جرب معايير بحث مختلفة</h2>
            <form method="GET" action="{{ url('/test-matching') }}">
                <div class="form-grid">
                    <div class="form-group">
                        <label>خط العرض</label>
                        <input type="number" name="lat" step="0.0001" value="{{ $searchLat }}">
                    </div>
                    <div class="form-group">
                        <label>خط الطول</label>
                        <input type="number" name="lng" step="0.0001" value="{{ $searchLng }}">
                    </div>
                    <div class="form-group">
                        <label>نصف القطر (كم)</label>
                        <input type="number" name="radius" value="{{ $radiusKm }}" min="1" max="100" required>
                    </div>
                    <div class="form-group">
                        <label>فصيلة الدم المطلوبة</label>
                        <select name="blood_type" required>
                            @foreach($allBloodTypes as $type)
                                <option value="{{ $type->value }}" {{ $type->value === $bloodType ? 'selected' : '' }}>
                                    {{ $type->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>المحافظة (الخيار البديل عند عدم وجود GPS)</label>
                        <select name="governorate_id">
                            <option value="">-- اختر المحافظة --</option>
                            @foreach($allGovernorates as $gov)
                                <option value="{{ $gov->id }}" {{ (string) $gov->id === (string) $selectedGovernorateId ? 'selected' : '' }}>
                                    {{ $gov->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn">🔍 ابحث</button>
            </form>
        </div>
    </div>
</body>

</html>