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

      <div class="form-container">
        <form id="donorRegistrationForm" method="POST" action="{{ route('register.createDonor.store') }}">
          @csrf <div class="form-step active">
            <h2 class="step-title">المعلومات الشخصية</h2>

            <div class="form-row">
              <div class="form-group">
                <label for="name">الاسم كاملاً <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="محمد أحمد" />
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                  placeholder="you@example.com" />
                @error('email') <span class="error-message">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="national_id">رقم الهوية الوطنية <span class="required">*</span></label>
                <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" required
                  placeholder="123456789" />
                @error('national_id') <span class="error-message">{{ $message }}</span> @enderror
              </div>

              <div class="form-group">
                <label for="phone">رقم الجوال <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                  placeholder="0599xxxxxx" />
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

            <button type="submit" class="btn btn-primary btn-submit">
              <span>إنشاء الحساب</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</x-layout>