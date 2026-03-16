<x-layout title="{{ __('Register New Organization') }} - {{ $settings->getTranslation('site_name') }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-organization.css') }}" />
        <script>
            window.translations = {
                'Org name required': '{{ __('Organization name is required') }}',
                'Opening time required': '{{ __('Opening time is required') }}',
                'Closing time required': '{{ __('Closing time is required') }}',
                'Min one working day': '{{ __('Please choose at least one working day') }}',
                'Daily capacity required': '{{ __('Daily capacity is required and must be greater than 0') }}',
                'Email is required': '{{ __('Email is required') }}',
                'Invalid email address': '{{ __('Invalid email address') }}',
                'Mobile number is required': '{{ __('Mobile number is required') }}',
                'Invalid mobile number': '{{ __('Invalid mobile number') }}',
                'Governorate is required': '{{ __('Governorate is required') }}',
                'License number required': '{{ __('License number is required') }}',
                'Please upload license': '{{ __('Please upload the license') }}',
                'Contact name required': '{{ __('Contact name is required') }}',
                'Job title required': '{{ __('Job title is required') }}',
                'Password is required': '{{ __('Password is required') }}',
                'Password must be at least 8 characters': '{{ __('Password must be at least 8 characters') }}',
                'Confirm password is required': '{{ __('Confirm password is required') }}',
                'Passwords do not match': '{{ __('Passwords do not match') }}',
                'Must agree to terms': '{{ __('You must agree to the terms and conditions') }}',
                'Not specified': '{{ __('Not specified') }}',
                'Working 24/7': '{{ __('Works 24/7') }}',
                'File too large': '{{ __('File size must be less than 5MB') }}',
                'File type not supported': '{{ __('File type not supported. Please choose PDF or image') }}'
            };

            function __(key, replace = {}) {
                let translation = window.translations[key] || key;
                for (let placeholder in replace) {
                    translation = translation.replace(':' + placeholder, replace[placeholder]);
                }
                return translation;
            }
        </script>
    @endpush

    <section class="registration-organization-section">
        <div class="registration-container">
            <div class="registration-header">
                <div class="header-icon">🏥</div>
                <h1>{{ $settings->getTranslation('org_register_title') }}</h1>
                <p>{{ $settings->getTranslation('org_register_subtitle') }}</p>
            </div>

            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">{{ __('Organization Information') }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">{{ __('Contact Information') }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">{{ __('Documentation & Management') }}</div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">{{ __('Review and Confirmation') }}</div>
                </div>
            </div>

            <div class="form-container">
                <form id="organizationRegistrationForm" method="POST"
                    action="{{ route('register.organization.store') }}" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="info-box" style="background: #fee2e2; border-color: #ef4444; margin-bottom: 2rem;">
                            <div class="info-icon">⚠️</div>
                            <div class="info-content">
                                <strong
                                    style="color: #b91c1c;">{{ __('Please correct the following errors to submit the request:') }}</strong>
                                <ul style="margin: 0.5rem 1rem 0 0; padding: 0; color: #b91c1c; font-size: 0.95rem;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="form-step active" id="step1">
                        <h2 class="step-title">{{ __('Organization Information') }}</h2>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationName">{{ __('Organization Name') }} <span
                                        class="required">*</span></label>
                                <input type="text" id="organizationName" name="organizationName"
                                    value="{{ old('organizationName') }}"
                                    placeholder="{{ __('General Community Hospital') }}" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="checkbox-label inline">
                                    <input type="checkbox" id="isOpen247" name="is_24_hours" value="1" />
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">{{ __('The organization operates 24/7') }}</span>
                                </label>
                            </div>
                        </div>

                        <div id="operatingHoursContainer">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="opening_time">{{ __('Opening Time') }} <span
                                            class="required">*</span></label>
                                    <input type="time" id="opening_time" name="opening_time"
                                        value="{{ old('opening_time') }}" />
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="closing_time">{{ __('Closing Time') }} <span
                                            class="required">*</span></label>
                                    <input type="time" id="closing_time" name="closing_time"
                                        value="{{ old('closing_time') }}" />
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="info-box mini" style="margin: 0 0 1.5rem 0; padding: 0.75rem 1rem;">
                                <div class="info-icon">💡</div>
                                <div class="info-content">
                                    <p style="font-size: 0.85rem;">
                                        {{ __('Providing operating hours helps donors choose the appropriate time to visit you.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label>{{ __('Working Days') }} <span class="required">*</span></label>
                                <div class="checkbox-grid">
                                    @php
                                        $days = [
                                            'Saturday' => __('Saturday'),
                                            'Sunday' => __('Sunday'),
                                            'Monday' => __('Monday'),
                                            'Tuesday' => __('Tuesday'),
                                            'Wednesday' => __('Wednesday'),
                                            'Thursday' => __('Thursday'),
                                            'Friday' => __('Friday'),
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
                                <label for="daily_capacity">{{ __('Daily Capacity (Number of Donors)') }} <span
                                        class="required">*</span></label>
                                <input type="number" id="daily_capacity" name="daily_capacity"
                                    value="{{ old('daily_capacity') }}" min="1"
                                    placeholder="{{ __('Example: 50') }}" />
                                <span class="error-message"></span>
                                <span
                                    class="helper-text">{{ __('The estimated number of donors the organization can receive daily. Entering this number helps us organize the flow of donors.') }}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationDescription">{{ __('Organization Description') }}</label>
                                <textarea id="organizationDescription" name="organizationDescription" rows="4"
                                    placeholder="{{ __('Briefly describe your organization\'s role') }}">{{ old('organizationDescription') }}</textarea>
                                <span
                                    class="helper-text">{{ __('This will help us understand how to collaborate with you better') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <h2 class="step-title">{{ __('Contact Information') }}</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contactEmail">{{ __('Contact Email') }} <span
                                        class="required">*</span></label>
                                <input type="email" id="contactEmail" name="contactEmail"
                                    value="{{ old('contactEmail') }}" placeholder="contact@organization.com" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactPhone">{{ __('Contact Mobile Number') }} <span
                                        class="required">*</span></label>
                                <input type="tel" id="contactPhone" name="contactPhone"
                                    value="{{ old('contactPhone') }}" placeholder="0599xxxxxx" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="governorate_id">{{ __('Governorate') }} <span
                                        class="required">*</span></label>
                                <select id="governorate_id" name="governorate_id">
                                    <option value="" disabled selected>{{ __('Select Governorate') }}</option>
                                    @foreach ($governorates as $gov)
                                        <option value="{{ $gov->id }}"
                                            {{ old('governorate_id') == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="auto_location_address">
                                    {{ __('Organization\'s Automatic Location') }}
                                    <span style="color: #999; font-weight: normal;">({{ __('Optional') }})</span>
                                </label>
                                <div style="position: relative; display: flex; gap: 0.5rem;">
                                    <input type="text" id="auto_location_address" name="auto_location_address"
                                        value="{{ old('auto_location_address') }}"
                                        placeholder="{{ __('Click the location button to determine the organization\'s location automatically') }}"
                                        readonly style="flex: 1; background: #f9fafb; cursor: pointer;" />
                                    <button type="button" id="gps-location-btn" class="btn btn-outline"
                                        style="padding: 0.875rem 1.5rem; white-space: nowrap; min-width: auto;"
                                        title="{{ __('Determine location automatically') }}">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" style="display: inline-block;">
                                            <path d="M12 2v4m0 12v4M2 12h4m12 0h4" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span style="margin-inline-start: 0.5rem;">{{ __('Locate') }}</span>
                                    </button>
                                </div>
                                <span
                                    class="helper-text">{{ __('This will help donors find the organization\'s location easily') }}</span>
                                <span class="error-message"></span>

                                <!-- Hidden inputs for coordinates -->
                                <input type="hidden" id="auto_lat" name="auto_lat" value="{{ old('auto_lat') }}">
                                <input type="hidden" id="auto_lng" name="auto_lng" value="{{ old('auto_lng') }}">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="form-step" id="step3">
                <h2 class="step-title">{{ __('Documentation & Management') }}</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="licenseNumber">{{ __('Official License Number') }} <span
                                class="required">*</span></label>
                        <input type="text" id="licenseNumber" name="licenseNumber"
                            value="{{ old('licenseNumber') }}" placeholder="LIC-123456789" />
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="licenseUpload">{{ __('Upload License') }} <span class="required">*</span></label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="licenseUpload" name="licenseUpload"
                                accept=".pdf,.jpg,.jpeg,.png" class="file-input" />
                            <div class="file-upload-display" id="fileUploadDisplay">
                                <div class="file-icon">📄</div>
                                <div class="file-text">
                                    <span class="file-prompt">{{ __('Click to upload') }}</span>
                                    <span class="file-hint">{{ __('PDF, JPG, PNG up to 5MB') }}</span>
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
                        <label for="adminName">{{ __('Administrative Contact Name') }} <span
                                class="required">*</span></label>
                        <input type="text" id="adminName" name="adminName" value="{{ old('adminName') }}"
                            placeholder="{{ __('John Doe') }}" />
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="responsible_person_position">{{ __('Job Title') }} <span
                                class="required">*</span></label>
                        <input type="text" id="responsible_person_position" name="responsible_person_position"
                            value="{{ old('responsible_person_position') }}"
                            placeholder="{{ __('Public Relations Manager') }}" />
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adminEmail">{{ __('Administrator Email') }} <span
                                class="required">*</span></label>
                        <input type="email" id="adminEmail" name="adminEmail" value="{{ old('adminEmail') }}"
                            placeholder="admin@organization.com" />
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adminPassword">{{ __('Password') }} <span class="required">*</span></label>
                        <div class="password-input">
                            <input type="password" id="adminPassword" name="adminPassword" placeholder="••••••••" />
                            <button type="button" class="toggle-password" data-target="adminPassword">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        <span class="error-message"></span>
                        <span class="helper-text">{{ __('Must be at least 8 characters') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword_confirmation">{{ __('Confirm Password') }} <span
                                class="required">*</span></label>
                        <div class="password-input">
                            <input type="password" id="adminPassword_confirmation" name="adminPassword_confirmation"
                                placeholder="••••••••" />
                            <button type="button" class="toggle-password" data-target="adminPassword_confirmation">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        <span class="error-message"></span>
                    </div>
                </div>
            </div>

            <div class="form-step" id="step4">
                <h2 class="step-title">{{ __('Review Information') }}</h2>

                <div class="review-section">
                    <h3>{{ __('Organization Information') }}</h3>
                    <div class="review-grid" id="organizationInfoReview"></div>
                </div>

                <div class="review-section">
                    <h3>{{ __('Contact Information') }}</h3>
                    <div class="review-grid" id="contactInfoReview"></div>
                </div>

                <div class="review-section">
                    <h3>{{ __('Administrative Details') }}</h3>
                    <div class="review-grid" id="adminInfoReview"></div>
                </div>

                <div class="info-box">
                    <div class="info-icon">ℹ️</div>
                    <div class="info-content">
                        <strong>{{ __('Note:') }}</strong>
                        <p>{{ __('Your request will be reviewed by our team. We will send you an email once your account is fully activated.') }}
                        </p>
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="termsAgree" name="termsAgree" required value="1" />
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">{{ __('I have read and agreed to') }} <a
                                href="{{ route('terms') }}" target="_blank"
                                class="terms-link">{{ __('Terms of Service') }}</a> {{ __('and') }} <a
                                href="javascript:void(0)" @click.prevent="$dispatch('open-modal', 'privacyModal')"
                                class="terms-link">{{ __('Privacy Policy') }}</a> <span
                                class="required">*</span></span>
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
                    <span>{{ __('Previous') }}</span>
                </button>
                <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                    <span>{{ __('Next') }}</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </button>
                <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display: none">
                    <span>{{ __('Submit Request') }}</span>
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
