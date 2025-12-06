<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="تسجيل متبرع جديد في BloodBridge" />
    <title>تسجيل المتبرع - BloodBridge</title>
    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap"
      rel="stylesheet"
    />
    <!-- Main CSS -->
    <link rel="stylesheet" href="./assets/styles/main.css" />
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="./assets/styles/layout/navbar.css" />
    <!-- Footer CSS -->
    <link rel="stylesheet" href="./assets/styles/layout/footer.css" />
    <!-- Registration Donor Page CSS -->
    <link
      rel="stylesheet"
      href="./assets/styles/pages/registration-donor.css"
    />
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
      <div class="nav-container">
        <div class="logo">
          <div class="logo-icon">🩸</div>
          BloodBridge
        </div>
        <ul class="nav-links">
          <li><a href="index.html">الرئيسية</a></li>
          <li><a href="about.html">من نحن</a></li>
          <li><a href="contact.html">اتصل بنا</a></li>
        </ul>
        <div class="nav-buttons">
          <a href="login.html" class="btn btn-outline">تسجيل الدخول</a>
        </div>
        <button class="mobile-menu-btn" id="mobile-menu-btn">☰</button>
      </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="overlay" id="overlay"></div>
    <div class="mobile-nav" id="mobile-nav">
      <button class="mobile-menu-close" id="mobile-menu-close">×</button>
      <ul class="mobile-nav-links">
        <li><a href="index.html">الرئيسية</a></li>
        <li><a href="about.html">من نحن</a></li>
        <li><a href="contact.html">اتصل بنا</a></li>
      </ul>
      <div class="mobile-nav-buttons">
        <a href="login.html" class="btn btn-outline">تسجيل الدخول</a>
      </div>
    </div>

    <!-- Registration Section -->
    <section class="registration-donor-section">
      <div class="registration-container">
        <!-- Header -->
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
          <form id="donorRegistrationForm">
            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step1">
              <h2 class="step-title">المعلومات الشخصية</h2>

              <div class="form-row">
                <div class="form-group">
                  <label for="fullName"
                    >الاسم كاملاً <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="fullName"
                    name="fullName"
                    required
                    placeholder="محمد أحمد"
                  />
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="email"
                    >البريد الإلكتروني <span class="required">*</span></label
                  >
                  <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    placeholder="you@example.com"
                  />
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="nationalId"
                    >رقم الهوية الوطنية <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="nationalId"
                    name="nationalId"
                    required
                    placeholder="123456789"
                  />
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="phone"
                    >رقم الجوال <span class="required">*</span></label
                  >
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    required
                    placeholder="+970 599 999 999"
                  />
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="birthDate"
                    >تاريخ الميلاد <span class="required">*</span></label
                  >
                  <input type="date" id="birthDate" name="birthDate" required />
                  <span class="error-message"></span>
                </div>

                <div class="form-group">
                  <label for="gender"
                    >الجنس <span class="required">*</span></label
                  >
                  <select id="gender" name="gender" required>
                    <option value="" disabled selected>اختر الجنس</option>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                  </select>
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group full-width">
                  <label for="address"
                    >العنوان كاملاً <span class="required">*</span></label
                  >
                  <input
                    type="text"
                    id="address"
                    name="address"
                    required
                    placeholder="123 شارع الأمل، خان يونس"
                  />
                  <span class="error-message"></span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="password"
                    >كلمة السر <span class="required">*</span></label
                  >
                  <div class="password-input">
                    <input
                      type="password"
                      id="password"
                      name="password"
                      required
                      placeholder="••••••••"
                    />
                    <button
                      type="button"
                      class="toggle-password"
                      data-target="password"
                    >
                      <span class="eye-icon">👁️</span>
                    </button>
                  </div>
                  <span class="error-message"></span>
                  <span class="helper-text">يجب أن تكون 8 أحرف على الأقل</span>
                </div>

                <div class="form-group">
                  <label for="confirmPassword"
                    >تأكيد كلمة السر <span class="required">*</span></label
                  >
                  <div class="password-input">
                    <input
                      type="password"
                      id="confirmPassword"
                      name="confirmPassword"
                      required
                      placeholder="••••••••"
                    />
                    <button
                      type="button"
                      class="toggle-password"
                      data-target="confirmPassword"
                    >
                      <span class="eye-icon">👁️</span>
                    </button>
                  </div>
                  <span class="error-message"></span>
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
                  <strong>ملاحظة:</strong>
                  <p>
                    سيتم إضافة المعلومات الطبية (زمرة الدم، الحالات الصحية)
                    لاحقاً من لوحة التحكم الخاصة بك بعد التسجيل
                  </p>
                </div>
              </div>

              <div class="form-group checkbox-group">
                <label class="checkbox-label">
                  <input
                    type="checkbox"
                    id="termsAgree"
                    name="termsAgree"
                    required
                  />
                  <span class="checkbox-custom"></span>
                  <span class="checkbox-text"
                    >لقد قرأت ووافقت على
                    <a href="#" class="terms-link">الشروط والأحكام</a>
                    <span class="required">*</span></span
                  >
                </label>
                <span class="error-message"></span>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="form-navigation">
              <button
                type="button"
                class="btn btn-outline btn-prev"
                id="prevBtn"
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                <span>السابق</span>
              </button>
              <button
                type="button"
                class="btn btn-primary btn-next"
                id="nextBtn"
              >
                <span>التالي</span>
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
              </button>
              <button
                type="submit"
                class="btn btn-primary btn-submit"
                id="submitBtn"
                style="display: none"
              >
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
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  fill="#4285F4"
                />
                <path
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  fill="#34A853"
                />
                <path
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  fill="#FBBC05"
                />
                <path
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  fill="#EA4335"
                />
              </svg>
              <span>التسجيل باستخدام جوجل</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <p>جميع الحقوق محفوظة 2025 &copy;</p>
      <p>إنقاذ الأرواح، قطرة واحدة في كل مرة</p>
    </footer>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
      <div class="modal-content">
        <div class="modal-icon success">✓</div>
        <h2>تم التسجيل بنجاح!</h2>
        <p>
          تم إنشاء حسابك بنجاح. يرجى التحقق من بريدك الإلكتروني لتفعيل الحساب.
        </p>
        <button
          class="btn btn-primary"
          onclick="window.location.href='login.html'"
        >
          تسجيل الدخول
        </button>
      </div>
    </div>

    <!-- Page Specific JavaScript -->
    <script src="./assets/scripts/pages/registration-donor.js"></script>
  </body>
</html>
