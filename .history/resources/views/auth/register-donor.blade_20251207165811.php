<x-layout title="تسجيل متبرع جديد - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-donor.css') }}" />
    @endpush

    <section class="registration-donor-section">
        <div class="registration-container">
            <div class="registration-header">
                <div class="header-icon">❤️</div>
                <h1>كن متبرعًا</h1>
                <p>تبرعك قد ينقذ حياة ثلاثة أشخاص. شكرًا لكرمك</p>
            </div>

            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">المعلومات الشخصية</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">المراجعة والتأكيد</div>
                </div>
            </div>

            <div class="form-container">
                <form id="donorRegistrationForm" method="POST" action="{{ route('register.donor.store') }}">
                    @csrf
                    
                    <div class="form-step active" id="step1">
                        <h2 class="step-title">المعلومات الشخصية</h2>

                        @if ($errors->any())
                            <div class="info-box" style="background: #fee2e2; border-color: #ef4444; margin-bottom: 1.5rem;">
                                <div class="info-content">
                                    <strong style="color: #b91c1c;">يرجى تصحيح الأخطاء التالية:</strong>
                                    <ul style="margin: 0; padding-right: 1rem; color: #b91c1c;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">الاسم كاملاً <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="محمد أحمد" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="national_id">رقم الهوية الوطنية <span class="required">*</span></label>
                                <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required placeholder="123456789" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="phone">رقم الجوال <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="0599xxxxxx" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="birth_date">تاريخ الميلاد <span class="required">*</span></label>
                                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="gender">الجنس <span class="required">*</span></label>
                                <select id="gender" name="gender" required>
                                    <option value="" disabled selected>اختر الجنس</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="city">المدينة / العنوان <span class="required">*</span></label>
                                <input type="text" id="city" name="city" value="{{ old('city') }}" required placeholder="غزة، الرمال" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">كلمة السر <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="password" name="password" required placeholder="••••••••" />
                                    <button type="button" class="toggle-password" data-target="password">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                                <span class="helper-text">يجب أن تكون 8 أحرف على الأقل</span>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">تأكيد كلمة السر <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" />
                                    <button type="button" class="toggle-password" data-target="password_confirmation">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <h2 class="step-title">مراجعة المعلومات</h2>

                        <div class="review-section">
                            <h3>المعلومات الشخصية</h3>
                            <div class="review-grid" id="personalInfoReview"></div>
                        </div>

                        <div class="info-box">
                            <div class="info-icon">ℹ️</div>
                            <div class="info-content">
                                <strong>ملاحظة:</strong>
                                <p>
                                    سيتم إضافة المعلومات الطبية (زمرة الدم، الحالات الصحية)
                                    لاحقاً من لوحة التحكم الخاصة بك بعد التسجيل
                                </p>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="termsAgree" name="terms" required />
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">
                                    لقد قرأت ووافقت على
                                    <a href="#" class="terms-link">الشروط والأحكام</a>
                                    <span class="required">*</span>
                                </span>
                            </label>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-outline btn-prev" id="prevBtn" style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7" />
                            </svg>
                            <span>السابق</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                            <span>التالي</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display: none;">
                            <span>إنشاء الحساب</span>
                            <span class="btn-loader"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/registration-donor.js') }}"></script>
    @endpush
</x-layout>