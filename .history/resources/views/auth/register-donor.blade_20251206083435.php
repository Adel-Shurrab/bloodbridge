<x-layout title="تسجيل متبرع جديد - BloodBridge">
  @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-donor.css') }}" />
  @endpush

  <section class="registration-donor-section">
    <div class="registration-container">
      <!-- Registration Header -->
      <div class="registration-header">
        <div class="header-icon">❤️</div>
        <h1>كن متبرعًا</h1>
        <p>تبرعك قد ينقذ حياة ثلاثة أشخاص. شكرًا لكرمك</p>
      </div>

      <!-- Progress Steps -->
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

      <!-- Form Container -->
      <div class="form-container">
        <form id="donorRegistrationForm" method="POST" action="{{ route('register.donor.store') }}">
          @csrf

          <!-- Step 1: Personal Information -->
          <div class="form-step active" id="step1">
            <h2 class="step-title">المعلومات الشخصية</h2>

            <div class="form-row">
              <div class="form-group">
                <label for="name">الاسم كاملاً <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="محمد أحمد" />
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com" />
                @error('email') <span class="error-message">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="national_id">رقم الهوية الوطنية <span class="required">*</span></label>
                <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required placeholder="123456789" />
                @error('national_id') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="phone">رقم الجوال <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="0599xxxxxx" />
                @error('phone') <span class="error-message">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="birth_date">تاريخ الميلاد <span class="required">*</span></label>
                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required />
                @error('birth_date') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="gender">الجنس <span class="required">*</span></label>
                <select id="gender" name="gender" required>
                  <option value="" disabled selected>اختر الجنس</option>
                  <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                  <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                </select>
                @error('gender') <span class="error-message">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group full-width">
                <label for="city">المدينة / العنوان <span class="required">*</span></label>
                <input type="text" id="city" name="city" value="{{ old('city') }}" required placeholder="غزة، الرمال" />
                @error('city') <span class="error-message">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="password">كلمة السر <span class="required">*</span></label>
                <input type="password" id="password" name="password" required />
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة السر <span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required />
              </div>
            </div>
          </div>

          <!-- Step 2: Review & Confirm -->
          <div class="form-step" id="step2">
            <h2 class="step-title">مراجعة المعلومات</h2>

            <div class="review-section">
              <h3>المعلومات الشخصية</h3>
              <div class="review-grid" id="personalInfoReview"></div>
            </div>

            <div class="info-box">
              <div class="info-icon">ℹ️</div>
              <div class="info-content">
                <strong>تأكيد المعلومات</strong>
                <p>يرجى التحقق من صحة جميع المعلومات المدخلة قبل إرسال التسجيل. لا يمكن تغيير بعض البيانات بعد التسجيل.</p>
              </div>
            </div>

            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" id="termsCheckbox" name="terms_agreement" required />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">
                  أوافق على <a href="#" class="terms-link">شروط الخدمة والخصوصية</a>
                </span>
              </label>
              <span class="error-message"></span>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="form-navigation">
            <button type="button" class="btn btn-outline btn-prev" id="prevBtn" style="display: none">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 19l-7-7 7-7"></path>
              </svg>
              السابق
            </button>
            <button type="button" class="btn btn-primary btn-next" id="nextBtn">
              <span>التالي</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display: none">
              <span>إنشاء الحساب</span>
              <span class="btn-loader"></span>
            </button>
          </div>
        </form>

        <!-- Social Login -->
        <div class="social-section">
          <div class="divider">
            <span>أو</span>
          </div>
          <button type="button" class="btn btn-social">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <span>التسجيل باستخدام جوجل</span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Success Modal -->
  <div class="modal-overlay" id="successModal">
    <div class="modal-content">
      <div class="modal-icon success">✓</div>
      <h2>تم التسجيل بنجاح!</h2>
      <p>تم إنشاء حسابك بنجاح. يرجى التحقق من بريدك الإلكتروني لتفعيل الحساب.</p>
      <button class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">
        تسجيل الدخول
      </button>
    </div>
  </div>

  @push('scripts')
    <script src="{{ asset('assets/scripts/pages/registration-donor.js') }}"></script>
  @endpush
</x-layout>