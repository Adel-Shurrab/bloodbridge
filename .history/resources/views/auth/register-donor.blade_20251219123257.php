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
              <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                القياسات الأساسية
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="weight">الوزن (كغ) <span class="required">*</span></label>
                  <input type="number" id="weight" name="weight" min="50" max="200" placeholder="70" />
                  <span class="helper-text">يجب أن يكون 50 كغ على الأقل</span>
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="height">الطول (سم) <span class="required">*</span></label>
                  <input type="number" id="height" name="height" min="140" max="220" placeholder="180" />
                  <span class="helper-text">يجب أن يكون 140 سم على الأقل</span>
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group full-width">
                  <label for="blood_type">فصيلة الدم <span style="color: #999; font-weight: normal;">(اختياري)</span></label>
                  <select id="blood_type" name="blood_type">
                    <option value="" selected>لا أعرف / غير متأكد</option>
                    <option value="O+">O+ (موجب)</option>
                    <option value="O-">O- (سالب)</option>
                    <option value="A+">A+ (موجب)</option>
                    <option value="A-">A- (سالب)</option>
                    <option value="B+">B+ (موجب)</option>
                    <option value="B-">B- (سالب)</option>
                    <option value="AB+">AB+ (موجب)</option>
                    <option value="AB-">AB- (سالب)</option>
                  </select>
                  <span class="helper-text">سيقوم المستشفى بإجراء فحص للتأكد من الفصيلة عند التبرع</span>
                  <span class="error-message"></span>
                </div>
              </div>
            </div>

            <!-- Section 2: Medical History -->
            <div style="margin-bottom: 2rem;">
              <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
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
              <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                التبرعات والعمليات الجراحية السابقة
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="last_donation_date">تاريخ آخر تبرع <span style="color: #999; font-weight: normal;">(إن وجد)</span></label>
                  <input type="date" id="last_donation_date" name="last_donation_date" max="{{ date('Y-m-d') }}" />
                  <span class="helper-text">يجب الانتظار 90 يوم بين التبرعات</span>
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="surgery_date">تاريخ آخر عملية جراحية <span style="color: #999; font-weight: normal;">(إن وجد)</span></label>
                  <input type="date" id="surgery_date" name="surgery_date" max="{{ date('Y-m-d') }}" />
                  <span class="helper-text">يجب الانتظار 28 يوم بعد العملية</span>
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

            <div class="review-section">
              <h3>المعلومات الشخصية</h3>
              <div class="review-grid" id="personalInfoReview"></div>
            </div>

            <div class="review-section">
              <h3>الملف الصحي</h3>
              <div class="review-grid" id="healthInfoReview"></div>
            </div>

            <div id="eligibility-review-box" style="display: none;" class="info-box">
              <div class="info-icon" id="eligibility-review-icon">✓</div>
              <div class="info-content">
                <strong id="eligibility-review-title">حالة التأهيل</strong>
                <p id="eligibility-review-message"></p>
              </div>
            </div>

            <div class="info-box">
              <div class="info-icon">ℹ️</div>
              <div class="info-content">
                <strong>ملاحظة:</strong>
                <p>
                  سيتم إضافة زمرة الدم والمزيد من المعلومات الطبية
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