<x-layout title="تسجيل منظمة جديدة - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-organization.css') }}" />
    @endpush

    <section class="registration-organization-section">
        <div class="registration-container">
            <div class="registration-header">
                <div class="header-icon">🏥</div>
                <h1>كن شريكاً كمنظمة</h1>
                <p>انضم إلى شبكتنا المنقذة للحياة. سجّل مؤسستك لإدارة تبرعات الدم بكفاءة</p>
            </div>

            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">معلومات المنظمة</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">معلومات الاتصال</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">التوثيق والإدارة</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">المراجعة والتأكيد</div>
                </div>
            </div>

            <div class="form-container">
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

                <form id="organizationRegistrationForm" method="POST"
                    action="{{ route('register.organization.store') }}" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="info-box" style="background: #fee2e2; border-color: #ef4444; margin-bottom: 2rem;">
                            <div class="info-icon">⚠️</div>
                            <div class="info-content">
                                <strong style="color: #b91c1c;">يرجى تصحيح الأخطاء التالية لتقديم الطلب:</strong>
                                <ul style="margin: 0.5rem 1rem 0 0; padding: 0; color: #b91c1c; font-size: 0.95rem;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="form-step active" id="step1">
                        <h2 class="step-title">معلومات المنظمة</h2>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationName">اسم المنظمة <span class="required">*</span></label>
                                <input type="text" id="organizationName" name="organizationName"
                                    value="{{ old('organizationName') }}" placeholder="مستشفى المجتمع العام" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="checkbox-label inline">
                                    <input type="checkbox" id="isOpen247" name="is_24_hours" value="1" />
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">تعمل المنظمة على مدار 24 ساعة (24/7)</span>
                                </label>
                            </div>
                        </div>

                        <div id="operatingHoursContainer">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="opening_time">وقت الافتتاح</label>
                                    <input type="time" id="opening_time" name="opening_time"
                                        value="{{ old('opening_time') }}" />
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="closing_time">وقت الإغلاق</label>
                                    <input type="time" id="closing_time" name="closing_time"
                                        value="{{ old('closing_time') }}" />
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="info-box mini" style="margin: 0 0 1.5rem 0; padding: 0.75rem 1rem;">
                                <div class="info-icon">💡</div>
                                <div class="info-content">
                                    <p style="font-size: 0.85rem;">تزويدنا بساعات العمل يساعد المتبرعين على اختيار الوقت المناسب لزيارتكم.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>أيام العمل <span class="required">*</span></label>
                                <div class="checkbox-grid">
                                    @php
                                        $days = [
                                            'Saturday' => 'السبت',
                                            'Sunday' => 'الأحد',
                                            'Monday' => 'الاثنين',
                                            'Tuesday' => 'الثلاثاء',
                                            'Wednesday' => 'الأربعاء',
                                            'Thursday' => 'الخميس',
                                            'Friday' => 'الجمعة',
                                        ];
                                    @endphp
                                    @foreach ($days as $value => $label)
                                        <label class="checkbox-label inline">
                                            <input type="checkbox" name="working_days[]" value="{{ $value }}"
                                                {{ is_array(old('working_days')) && in_array($value, old('working_days')) ? 'checked' : '' }} />
                                            <span class="checkbox-custom"></span>
                                            <span class="checkbox-text">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="daily_capacity">القدرة الاستيعابية اليومية (عدد المتبرعين) <span class="required">*</span></label>
                                <input type="number" id="daily_capacity" name="daily_capacity"
                                    value="{{ old('daily_capacity') }}" min="1" placeholder="مثال: 50" />
                                <span class="error-message"></span>
                                <span class="helper-text">العدد التقديري للمتبرعين الذين يمكن للمؤسسة استقبالهم يومياً. إدخال هذا الرقم يساعدنا في تنظيم تدفق المتبرعين.</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationDescription">وصف المنظمة</label>
                                <textarea id="organizationDescription" name="organizationDescription" rows="4"
                                    placeholder="قم بوصف دور مؤسستك بشكل موجز">{{ old('organizationDescription') }}</textarea>
                                <span class="helper-text">سيساعدنا هذا على فهم كيفية التعاون معك بشكل أفضل</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <h2 class="step-title">معلومات الاتصال</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contactEmail">البريد الإلكتروني للتواصل <span
                                        class="required">*</span></label>
                                <input type="email" id="contactEmail" name="contactEmail"
                                    value="{{ old('contactEmail') }}" placeholder="contact@organization.com" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactPhone">رقم الجوال للتواصل <span class="required">*</span></label>
                                <input type="tel" id="contactPhone" name="contactPhone"
                                    value="{{ old('contactPhone') }}" placeholder="0599xxxxxx" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="streetAddress">اسم الشارع <span class="required">*</span></label>
                                <input type="text" id="streetAddress" name="streetAddress"
                                    value="{{ old('streetAddress') }}" placeholder="123 الشارع الرئيسي" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="governorate_id">المحافظة <span class="required">*</span></label>
                                <select id="governorate_id" name="governorate_id">
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

                    </div>

                    <div class="form-step" id="step3">
                        <h2 class="step-title">التوثيق والإدارة</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="licenseNumber">رقم الترخيص الرسمي <span class="required">*</span></label>
                                <input type="text" id="licenseNumber" name="licenseNumber"
                                    value="{{ old('licenseNumber') }}" placeholder="LIC-123456789" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="licenseUpload">تحميل الرخصة <span class="required">*</span></label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="licenseUpload" name="licenseUpload"
                                        accept=".pdf,.jpg,.jpeg,.png" class="file-input" />
                                    <div class="file-upload-display" id="fileUploadDisplay">
                                        <div class="file-icon">📄</div>
                                        <div class="file-text">
                                            <span class="file-prompt">انقر للتحميل</span>
                                            <span class="file-hint">PDF, JPG, PNG حتى 5 ميغا بايت</span>
                                        </div>
                                    </div>
                                    <div class="file-selected" id="fileSelected" style="display: none">
                                        <span class="file-name" id="fileName"></span>
                                        <button type="button" class="file-remove" id="fileRemove">×</button>
                                    </div>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="adminName">اسم جهة الاتصال الإدارية <span class="required">*</span></label>
                                <input type="text" id="adminName" name="adminName" value="{{ old('adminName') }}"
                                    placeholder="أحمد محمد" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="responsible_person_position">المسمى الوظيفي <span
                                        class="required">*</span></label>
                                <input type="text" id="responsible_person_position" name="responsible_person_position"
                                    value="{{ old('responsible_person_position') }}"
                                    placeholder="مدير العلاقات العامة" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="adminEmail">البريد الإلكتروني للمسؤول <span
                                        class="required">*</span></label>
                                <input type="email" id="adminEmail" name="adminEmail" value="{{ old('adminEmail') }}"
                                    placeholder="admin@organization.com" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="adminPassword">كلمة السر <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="adminPassword" name="adminPassword"
                                        placeholder="••••••••" />
                                    <button type="button" class="toggle-password" data-target="adminPassword">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                                <span class="helper-text">يجب أن تكون 8 أحرف على الأقل</span>
                            </div>

                            <div class="form-group">
                                <label for="adminPassword_confirmation">تأكيد كلمة السر <span
                                        class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="adminPassword_confirmation"
                                        name="adminPassword_confirmation" placeholder="••••••••" />
                                    <button type="button" class="toggle-password"
                                        data-target="adminPassword_confirmation">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step4">
                        <h2 class="step-title">مراجعة المعلومات</h2>

                        <div class="review-section">
                            <h3>معلومات المنظمة</h3>
                            <div class="review-grid" id="organizationInfoReview"></div>
                        </div>

                        <div class="review-section">
                            <h3>معلومات الاتصال</h3>
                            <div class="review-grid" id="contactInfoReview"></div>
                        </div>

                        <div class="review-section">
                            <h3>التفاصيل الإدارية</h3>
                            <div class="review-grid" id="adminInfoReview"></div>
                        </div>

                        <div class="info-box">
                            <div class="info-icon">ℹ️</div>
                            <div class="info-content">
                                <strong>ملاحظة:</strong>
                                <p>سيتم مراجعة طلبك من قبل فريقنا. سنرسل لك بريداً إلكترونياً بمجرد تفعيل حسابك بالكامل.
                                </p>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="termsAgree" name="termsAgree" required value="1" />
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">لقد قرأت ووافقت على <a href="#" class="terms-link">الشروط
                                        والأحكام</a> <span class="required">*</span></span>
                            </label>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-outline btn-prev" id="prevBtn" style="display: none">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7" />
                            </svg>
                            <span>السابق</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                            <span>التالي</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display: none">
                            <span>تقديم الطلب</span>
                            <span class="btn-loader"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/registration-organization.js') }}"></script>
    @endpush
</x-layout>