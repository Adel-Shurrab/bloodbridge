<x-layout title="تسجيل متبرع جديد - {{ $settings->site_name }}">
  @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-donor.css') }}" />
    <script>
      window.donationRules = {
        minAge: {{ $settings->min_donor_age }},
        maxAge: {{ $settings->max_donor_age }},
        minWeight: {{ $settings->min_donor_weight }},
        minHeight: {{ $settings->min_donor_height }},
        minHeight: {{ $settings->min_donor_height }},
        minDaysBetweenDonations: {{ $settings->min_days_between_donations }},
        minDaysAfterSurgery: {{ $settings->min_days_after_surgery }}
            };
    </script>
  @endpush

  <section class="registration-donor-section">
    <div class="registration-container">
      <div class="registration-header">
        <div class="header-icon">❤️</div>
        <h1>{{ $settings->donor_register_title }}</h1>
        <p>{{ $settings->donor_register_subtitle }}</p>
      </div>

      <div class="progress-steps">
        <div class="step active" data-step="1">
          <div class="step-number">1</div>
          <div class="step-label">المعلومات الشخصية</div>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="2">
          <div class="step-number">2</div>
          <div class="step-label">الملف الصحي</div>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="3">
          <div class="step-number">3</div>
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
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                  placeholder="you@example.com" />
                <span class="error-message"></span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="national_id">رقم الهوية الوطنية <span class="required">*</span></label>
                <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required
                  placeholder="123456789" />
                <span class="error-message"></span>
              </div>

              <div class="form-group">
                <label for="phone">رقم الجوال <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                  placeholder="0599xxxxxx" />
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
                <label for="governorate_id">المحافظة <span class="required">*</span></label>
                <select id="governorate_id" name="governorate_id" required>
                  <option value="" disabled selected>اختر المحافظة</option>
                  @foreach ($governorates as $gov)
                    <option value="{{ $gov->id }}" {{ old('governorate_id') == $gov->id ? 'selected' : '' }}>
                      {{ $gov->name }}
                    </option>
                  @endforeach
                </select>
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
                  <input type="password" id="password_confirmation" name="password_confirmation" required
                    placeholder="••••••••" />
                  <button type="button" class="toggle-password" data-target="password_confirmation">
                    <span class="eye-icon">👁️</span>
                  </button>
                </div>
                <span class="error-message"></span>
              </div>
            </div>
          </div>

          <!-- Step 2: Health Profile -->
          <div class="form-step" id="step2">
            <h2 class="step-title">الملف الصحي</h2>

            <div class="info-box" style="background: #e0f2fe; border-color: #0284c7;">
              <div class="info-icon">ℹ️</div>
              <div class="info-content">
                <strong style="color: #0284c7;">معلومات صحية مهمة</strong>
                <p>هذه المعلومات تساعدنا على التحقق من صحتك وتحديد توافقك للتبرع</p>
              </div>
            </div>

            <!-- Section 1: Basic Measurements -->
            <div style="margin-bottom: 2rem;">
              <h3
                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                القياسات الأساسية
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="weight">الوزن (كغ) <span class="required">*</span></label>
                  <input type="number" id="weight" name="weight" min="{{ $settings->min_donor_weight }}" max="200"
                    placeholder="70" />
                  <span class="helper-text">يجب أن يكون {{ $settings->min_donor_weight }} كغ على الأقل</span>
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="height">الطول (سم) <span class="required">*</span></label>
                  <input type="number" id="height" name="height" min="{{ $settings->min_donor_height }}" max="220"
                    placeholder="180" />
                  <span class="helper-text">يجب أن يكون {{ $settings->min_donor_height }} سم على الأقل</span>
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group full-width">
                  <label for="blood_type">فصيلة الدم <span
                      style="color: #999; font-weight: normal;">(اختياري)</span></label>
                  <select id="blood_type" name="blood_type">
                    <option value="" selected>لا أعرف / غير متأكد</option>
                    @foreach($bloodTypes as $bloodType)
                      <option value="{{ $bloodType->value }}" {{ old('blood_type') == $bloodType->value ? 'selected' : '' }}>
                        {{ $bloodType->getLabel() }}
                      </option>
                    @endforeach
                  </select>
                  <span class="helper-text">سيقوم المستشفى بإجراء فحص للتأكد من الفصيلة عند التبرع</span>
                  <span class="error-message"></span>
                </div>
              </div>
            </div>

            <!-- Section 2: Medical History -->
            <div style="margin-bottom: 2rem;">
              <h3
                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                السجل الطبي
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label class="checkbox-label">
                    <input type="checkbox" id="chronic_disease" name="chronic_disease" value="1" />
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">هل تعاني من أي مرض مزمن؟</span>
                  </label>
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label class="checkbox-label">
                    <input type="checkbox" id="infection" name="infection" value="1" />
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">هل تعاني من عدوى حالياً؟</span>
                  </label>
                  <span class="error-message"></span>
                </div>
              </div>
            </div>

            <!-- Section 3: Donation & Surgery History -->
            <div style="margin-bottom: 2rem;">
              <h3
                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                التبرعات والعمليات الجراحية السابقة
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="recent_donation">هل قمت بالتبرع بالدم سابقاً؟ <span class="required">*</span></label>
                  <select id="recent_donation" name="recent_donation" required>
                    <option value="" disabled selected>اختر الإجابة</option>
                    <option value="1" {{ old('recent_donation') == '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ old('recent_donation') == '0' ? 'selected' : '' }}>لا</option>
                  </select>
                  <span class="error-message"></span>
                </div>

                <div class="form-group" id="last_donation_date_container" style="display: none;">
                  <label for="last_donation_date">تاريخ آخر تبرع <span class="required">*</span></label>
                  <div class="date-input-wrapper" style="position: relative;">
                    <input type="date" id="last_donation_date" name="last_donation_date" max="{{ date('Y-m-d') }}"
                      style="width: 100%;" />
                    <button type="button" class="clear-date-btn" data-target="last_donation_date"
                      style="display: none;">
                      ✕
                    </button>
                  </div>
                  <span class="helper-text">يجب الانتظار {{ $settings->min_days_between_donations }} يوم بين
                    التبرعات</span>
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="has_recent_surgery">هل أجريت أي عملية جراحية سابقاً؟ <span
                      class="required">*</span></label>
                  <select id="has_recent_surgery" name="has_recent_surgery" required>
                    <option value="" disabled selected>اختر الإجابة</option>
                    <option value="1" {{ old('has_recent_surgery') == '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ old('has_recent_surgery') == '0' ? 'selected' : '' }}>لا</option>
                  </select>
                  <span class="error-message"></span>
                </div>

                <div class="form-group" id="surgery_date_container" style="display: none;">
                  <label for="surgery_date">تاريخ آخر عملية جراحية <span class="required">*</span></label>
                  <div class="date-input-wrapper" style="position: relative;">
                    <input type="date" id="surgery_date" name="surgery_date" max="{{ date('Y-m-d') }}"
                      style="width: 100%;" />
                    <button type="button" class="clear-date-btn" data-target="surgery_date" style="display: none;">
                      ✕
                    </button>
                  </div>
                  <span class="helper-text">يجب الانتظار {{ $settings->min_days_after_surgery }} يوم بعد العملية</span>
                  <span class="error-message"></span>
                </div>
              </div>
            </div>

            <!-- Eligibility Status -->
            <div id="eligibility-status-box" style="display: none;" class="info-box">
              <div class="info-icon">⚠️</div>
              <div class="info-content">
                <strong id="eligibility-status-title">حالة التأهيل</strong>
                <p id="eligibility-status-message"></p>
              </div>
            </div>
          </div>

          <div class="form-step" id="step3">
            <h2 class="step-title">مراجعة المعلومات</h2>

            <!-- Personal Information Section -->
            <div style="margin-bottom: 2.5rem;">
              <h3
                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                <span>👤</span>
                <span>المعلومات الشخصية</span>
              </h3>
              <div class="review-grid" id="personalInfoReview"></div>
            </div>

            <!-- Health Information Section -->
            <div style="margin-bottom: 2.5rem;">
              <h3
                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                <span>💚</span>
                <span>الملف الصحي</span>
              </h3>
              <div class="review-grid" id="healthInfoReview"></div>
            </div>

            <!-- Eligibility Status Section -->
            <div style="margin-bottom: 2.5rem;" id="eligibility-review-container">
              <div id="eligibility-review-box" style="display: none;" class="info-box">
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                  <div class="info-icon" id="eligibility-review-icon" style="font-size: 1.5rem; flex-shrink: 0;">✓</div>
                  <div class="info-content" style="flex: 1;">
                    <strong id="eligibility-review-title"
                      style="font-size: 1.1rem; display: block; margin-bottom: 0.5rem;">حالة التأهيل</strong>
                    <p id="eligibility-review-message" style="margin: 0; line-height: 1.6; color: #4b5563;"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Important Note Section -->
            <div style="margin-bottom: 2.5rem;">
              <div class="info-box" style="background: #f3f4f6; border-color: #d1d5db;">
                <div class="info-icon">ℹ️</div>
                <div class="info-content">
                  <strong>ملاحظة مهمة:</strong>
                  <p style="margin: 0.5rem 0 0 0; line-height: 1.6;">
                    تم التحقق من جميع البيانات أعلاه. سيتم إرسال رسالة تأكيد إلى بريدك الإلكتروني.
                    <br>
                    يمكنك تحديث معلوماتك الإضافية (زمرة الدم والمزيد) لاحقاً من لوحة التحكم الخاصة بك.
                  </p>
                </div>
              </div>
            </div>

            <!-- Terms & Conditions Section -->
            <div
              style="padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 2rem;">
              <div class="form-group checkbox-group" style="margin: 0;">
                <label class="checkbox-label"
                  style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                  <input type="checkbox" id="termsAgree" name="terms" required style="margin-top: 0.25rem;" />
                  <span class="checkbox-custom" style="margin-top: 0.25rem;"></span>
                  <span class="checkbox-text" style="padding: 0;">
                    أقرّ بأنني قد قرأت ووافقت على
                    <a href="javascript:void(0)" @click.prevent="$dispatch('open-modal', 'privacyModal')"
                      class="terms-link" style="color: #dc2626; text-decoration: underline;">الشروط والأحكام</a>
                    والسياسات المتعلقة بالبيانات الشخصية
                    <span class="required">*</span>
                  </span>
                </label>
                <span class="error-message" style="display: block; margin-top: 0.5rem;"></span>
              </div>
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
        <div class="modal-overlay" id="ineligibleModal">
          <div class="modal-content">
            <div class="modal-icon" style="background: #fef3c7; color: #d97706;">❤️</div>
            <h2>شكراً لمبادرتك النبيلة</h2>
            <p>
              نقدر رغبتك العالية في المساعدة وإنقاذ الأرواح.
              <br><br>
              بناءً على المعلومات الصحية المقدمة (وجود أمراض مزمنة)، وحرصاً منا على سلامتك الشخصية أولاً، لا يمكننا قبول
              تسجيلك كمتبرع في الوقت الحالي.
              <br><br>
              يمكنك دائماً المساهمة في نشر الوعي ودعم القضية بطرق أخرى.
            </p>
            <button class="btn btn-outline" onclick="window.location.href='{{ route('home') }}'">
              العودة للرئيسية
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  @push('scripts')
    <script src="{{ asset('assets/scripts/pages/registration-donor.js') }}"></script>
  @endpush
</x-layout>