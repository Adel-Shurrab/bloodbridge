<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Register New Donor')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/registration-donor.css')); ?>" />
        <script>
            window.donationRules = {
                minAge: <?php echo e($settings->min_donor_age); ?>,
                maxAge: <?php echo e($settings->max_donor_age); ?>,
                minWeight: <?php echo e($settings->min_donor_weight); ?>,
                minHeight: <?php echo e($settings->min_donor_height); ?>,
                minDaysBetweenDonations: <?php echo e($settings->min_days_between_donations); ?>,
                minDaysAfterSurgery: <?php echo e($settings->min_days_after_surgery); ?>

            };

            window.translations = {
                'Name is required': '<?php echo e(__('Name is required')); ?>',
                'Email is required': '<?php echo e(__('Email is required')); ?>',
                'Invalid email address': '<?php echo e(__('Invalid email address')); ?>',
                'National ID is required': '<?php echo e(__('National ID is required')); ?>',
                'National ID must be 9 digits': '<?php echo e(__('National ID must be 9 digits')); ?>',
                'Mobile number is required': '<?php echo e(__('Mobile number is required')); ?>',
                'Invalid mobile number': '<?php echo e(__('Invalid mobile number')); ?>',
                'Birth date is required': '<?php echo e(__('Birth date is required')); ?>',
                'Must be at least :age years old': '<?php echo e(__('Must be at least :age years old')); ?>',
                'Must not exceed :age years old': '<?php echo e(__('Must not exceed :age years old')); ?>',
                'Gender is required': '<?php echo e(__('Gender is required')); ?>',
                'Governorate is required': '<?php echo e(__('Governorate is required')); ?>',
                'Password is required': '<?php echo e(__('Password is required')); ?>',
                'Password must be at least 8 characters': '<?php echo e(__('Password must be at least 8 characters')); ?>',
                'Confirm password is required': '<?php echo e(__('Confirm password is required')); ?>',
                'Passwords do not match': '<?php echo e(__('Passwords do not match')); ?>',
                'Weight is required': '<?php echo e(__('Weight is required')); ?>',
                'Weight must be at least :weight kg': '<?php echo e(__('Weight must be at least :weight kg')); ?>',
                'Height is required': '<?php echo e(__('Height is required')); ?>',
                'Height must be at least :height cm': '<?php echo e(__('Height must be at least :height cm')); ?>',
                'Please answer this question': '<?php echo e(__('Please answer this question')); ?>',
                'Last donation date is required': '<?php echo e(__('Last donation date is required')); ?>',
                'Please enter the full date': '<?php echo e(__('Please enter the full date')); ?>',
                'Surgery date is required': '<?php echo e(__('Surgery date is required')); ?>',
                'Must agree to terms': '<?php echo e(__('You must agree to the terms and conditions')); ?>',
                'Male': '<?php echo e(__('Male')); ?>',
                'Female': '<?php echo e(__('Female')); ?>',
                'Yes': '<?php echo e(__('Yes')); ?>',
                'No': '<?php echo e(__('No')); ?>',
                'Not specified': '<?php echo e(__('Not specified')); ?>',
                'Eligibility Status': '<?php echo e(__('Eligibility Status')); ?>',
                'Permanently Ineligible': '<?php echo e(__('Permanently Ineligible')); ?>',
                'Temporarily Ineligible': '<?php echo e(__('Temporarily Ineligible')); ?>',
                'Reasons:': '<?php echo e(__('Reasons:')); ?>',
                'Eligible from: :date': '<?php echo e(__('Eligible starting from: :date')); ?>',
                'Eligible to donate': '<?php echo e(__('Eligible to donate')); ?>',
                'Eligibility Success Msg': '<?php echo e(__('Congratulations! You are eligible to donate and contribute to saving lives.')); ?>',
                'Weight (kg)': '<?php echo e(__('Weight (kg)')); ?>',
                'Height (cm)': '<?php echo e(__('Height (cm)')); ?>',
                'Name': '<?php echo e(__('Name')); ?>',
                'Email': '<?php echo e(__('Email')); ?>',
                'National ID': '<?php echo e(__('National ID')); ?>',
                'Phone': '<?php echo e(__('Phone')); ?>',
                'Birth Date': '<?php echo e(__('Birth Date')); ?>',
                'Gender': '<?php echo e(__('Gender')); ?>',
                'Governorate': '<?php echo e(__('Governorate')); ?>',
                'Weight': '<?php echo e(__('Weight')); ?>',
                'Height': '<?php echo e(__('Height')); ?>',
                'Blood Type': '<?php echo e(__('Blood Type')); ?>',
                'Chronic Disease': '<?php echo e(__('Chronic Disease')); ?>',
                'Current Infection': '<?php echo e(__('Current Infection')); ?>',
                'Previously Donated': '<?php echo e(__('Previously Donated')); ?>',
                'Previous Surgery': '<?php echo e(__('Previous Surgery')); ?>',
                'kg': '<?php echo e(__('kg')); ?>',
                'cm': '<?php echo e(__('cm')); ?>',
                'Personal Information': '<?php echo e(__('Personal Information')); ?>',
                'Health Profile': '<?php echo e(__('Health Profile')); ?>'
            };

            function __(key, replace = {}) {
                let translation = window.translations[key] || key;
                for (let placeholder in replace) {
                    translation = translation.replace(':' + placeholder, replace[placeholder]);
                }
                return translation;
            }
        </script>
    <?php $__env->stopPush(); ?>

    <section class="registration-donor-section">
        <div class="registration-container">
            <div class="registration-header">
                <div class="header-icon">❤️</div>
                <h1><?php echo e($settings->getTranslation('donor_register_title')); ?></h1>
                <p><?php echo e($settings->getTranslation('donor_register_subtitle')); ?></p>
            </div>

            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label"><?php echo e(__('Personal Information')); ?></div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label"><?php echo e(__('Health Profile')); ?></div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label"><?php echo e(__('Review and Confirmation')); ?></div>
                </div>
            </div>

            <div class="form-container">
                <form id="donorRegistrationForm" method="POST" action="<?php echo e(route('register.donor.store')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-step active" id="step1">
                        <h2 class="step-title"><?php echo e(__('Personal Information')); ?></h2>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                            <div class="info-box"
                                style="background: #fee2e2; border-color: #ef4444; margin-bottom: 1.5rem;">
                                <div class="info-content">
                                    <strong
                                        style="color: #b91c1c;"><?php echo e(__('Please correct the following errors:')); ?></strong>
                                    <ul style="margin: 0; padding-inline-start: 1rem; color: #b91c1c;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><?php echo e(__('Full Name')); ?> <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>"
                                    required placeholder="<?php echo e(__('John Doe')); ?>" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="email"><?php echo e(__('Email Address')); ?> <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                                    required placeholder="you@example.com" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="national_id"><?php echo e(__('National ID Number')); ?> <span
                                        class="required">*</span></label>
                                <input type="text" id="national_id" name="national_id"
                                    value="<?php echo e(old('national_id')); ?>" required placeholder="123456789" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="phone"><?php echo e(__('Mobile Number')); ?> <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
                                    required placeholder="0599xxxxxx" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="birth_date"><?php echo e(__('Date of Birth')); ?> <span
                                        class="required">*</span></label>
                                <input type="date" id="birth_date" name="birth_date" value="<?php echo e(old('birth_date')); ?>"
                                    required />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="gender"><?php echo e(__('Gender')); ?> <span class="required">*</span></label>
                                <select id="gender" name="gender" required>
                                    <option value="" disabled selected><?php echo e(__('Select Gender')); ?></option>
                                    <option value="male" <?php echo e(old('gender') == 'male' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Male')); ?></option>
                                    <option value="female" <?php echo e(old('gender') == 'female' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Female')); ?>

                                    </option>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="governorate_id"><?php echo e(__('Governorate')); ?> <span
                                        class="required">*</span></label>
                                <select id="governorate_id" name="governorate_id" required>
                                    <option value="" disabled selected><?php echo e(__('Select Governorate')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gov->id); ?>"
                                            <?php echo e(old('governorate_id') == $gov->id ? 'selected' : ''); ?>>
                                            <?php echo e(__($gov->name)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="auto_location_address">
                                    <?php echo e(__('Automatic Location')); ?>

                                    <span style="color: #999; font-weight: normal;">(<?php echo e(__('Optional')); ?>)</span>
                                </label>
                                <div style="position: relative; display: flex; gap: 0.5rem;">
                                    <input type="text" id="auto_location_address" name="auto_location_address"
                                        value="<?php echo e(old('auto_location_address')); ?>"
                                        placeholder="<?php echo e(__('Click the location button to determine your location automatically')); ?>"
                                        readonly style="flex: 1; background: #f9fafb; cursor: pointer;" />
                                    <button type="button" id="gps-location-btn" class="btn btn-outline"
                                        style="padding: 0.875rem 1.5rem; white-space: nowrap; min-width: auto;"
                                        title="<?php echo e(__('Determine location automatically')); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" style="display: inline-block;">
                                            <path d="M12 2v4m0 12v4M2 12h4m12 0h4" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span style="margin-inline-start: 0.5rem;"><?php echo e(__('Locate Me')); ?></span>
                                    </button>
                                </div>
                                <span
                                    class="helper-text"><?php echo e(__('Your location will be used to match you with nearby donation requests')); ?></span>
                                <span class="error-message"></span>

                                <!-- Hidden inputs for coordinates -->
                                <input type="hidden" id="auto_lat" name="auto_lat"
                                    value="<?php echo e(old('auto_lat')); ?>">
                                <input type="hidden" id="auto_lng" name="auto_lng"
                                    value="<?php echo e(old('auto_lng')); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password"><?php echo e(__('Password')); ?> <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="password" name="password" required
                                        placeholder="••••••••" />
                                    <button type="button" class="toggle-password" data-target="password">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                                <span class="helper-text"><?php echo e(__('Must be at least 8 characters')); ?></span>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation"><?php echo e(__('Confirm Password')); ?> <span
                                        class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        required placeholder="••••••••" />
                                    <button type="button" class="toggle-password"
                                        data-target="password_confirmation">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Health Profile -->
                    <div class="form-step" id="step2">
                        <h2 class="step-title"><?php echo e(__('Health Profile')); ?></h2>

                        <div class="info-box" style="background: #e0f2fe; border-color: #0284c7;">
                            <div class="info-icon">ℹ️</div>
                            <div class="info-content">
                                <strong style="color: #0284c7;"><?php echo e(__('Important health information')); ?></strong>
                                <p><?php echo e(__('This information helps us verify your health and determine your eligibility for donation')); ?>

                                </p>
                            </div>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                                <?php echo e(__('Basic Measurements')); ?>

                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="weight"><?php echo e(__('Weight (kg)')); ?> <span
                                            class="required">*</span></label>
                                    <input type="number" id="weight" name="weight"
                                        min="<?php echo e($settings->min_donor_weight); ?>" max="200" placeholder="70" />
                                    <span
                                        class="helper-text"><?php echo e(__('Must be at least :weight kg', ['weight' => $settings->min_donor_weight])); ?></span>
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group">
                                    <label for="height"><?php echo e(__('Height (cm)')); ?> <span
                                            class="required">*</span></label>
                                    <input type="number" id="height" name="height"
                                        min="<?php echo e($settings->min_donor_height); ?>" max="220" placeholder="180" />
                                    <span
                                        class="helper-text"><?php echo e(__('Must be at least :height cm', ['height' => $settings->min_donor_height])); ?></span>
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="blood_type"><?php echo e(__('Blood Type')); ?> <span
                                            style="color: #999; font-weight: normal;">(<?php echo e(__('Optional')); ?>)</span></label>
                                    <select id="blood_type" name="blood_type">
                                        <option value="" selected><?php echo e(__("I don't know / Not sure")); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bloodTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>"
                                                <?php echo e(old('blood_type') == $value ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                    <span
                                        class="helper-text"><?php echo e(__('The hospital will perform a test to confirm the blood type during donation')); ?></span>
                                    <span class="error-message"></span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                                <?php echo e(__('Medical History')); ?>

                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="chronic_disease" name="chronic_disease"
                                            value="1" />
                                        <span class="checkbox-custom"></span>
                                        <span
                                            class="checkbox-text"><?php echo e(__('Do you suffer from any chronic disease?')); ?></span>
                                    </label>
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="infection" name="infection" value="1" />
                                        <span class="checkbox-custom"></span>
                                        <span
                                            class="checkbox-text"><?php echo e(__('Are you currently suffering from an infection?')); ?></span>
                                    </label>
                                    <span class="error-message"></span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb;">
                                <?php echo e(__('Previous Donations and Surgeries')); ?>

                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="recent_donation"><?php echo e(__('Have you donated blood before?')); ?> <span
                                            class="required">*</span></label>
                                    <select id="recent_donation" name="recent_donation" required>
                                        <option value="" disabled selected><?php echo e(__('Select answer')); ?></option>
                                        <option value="1" <?php echo e(old('recent_donation') == '1' ? 'selected' : ''); ?>>
                                            <?php echo e(__('Yes')); ?></option>
                                        <option value="0" <?php echo e(old('recent_donation') == '0' ? 'selected' : ''); ?>>
                                            <?php echo e(__('No')); ?></option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group" id="last_donation_date_container" style="display: none;">
                                    <label for="last_donation_date"><?php echo e(__('Last donation date')); ?> <span
                                            class="required">*</span></label>
                                    <div class="date-input-wrapper" style="position: relative;">
                                        <input type="date" id="last_donation_date" name="last_donation_date"
                                            max="<?php echo e(date('Y-m-d')); ?>" style="width: 100%;" />
                                        <button type="button" class="clear-date-btn"
                                            data-target="last_donation_date" style="display: none;">
                                            ✕
                                        </button>
                                    </div>
                                    <span
                                        class="helper-text"><?php echo e(__('Must wait :days days between donations', ['days' => $settings->min_days_between_donations])); ?></span>
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="has_recent_surgery"><?php echo e(__('Have you had any surgery before?')); ?>

                                        <span class="required">*</span></label>
                                    <select id="has_recent_surgery" name="has_recent_surgery" required>
                                        <option value="" disabled selected><?php echo e(__('Select answer')); ?></option>
                                        <option value="1"
                                            <?php echo e(old('has_recent_surgery') == '1' ? 'selected' : ''); ?>>
                                            <?php echo e(__('Yes')); ?></option>
                                        <option value="0"
                                            <?php echo e(old('has_recent_surgery') == '0' ? 'selected' : ''); ?>>
                                            <?php echo e(__('No')); ?></option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group" id="surgery_date_container" style="display: none;">
                                    <label for="surgery_date"><?php echo e(__('Last surgery date')); ?> <span
                                            class="required">*</span></label>
                                    <div class="date-input-wrapper" style="position: relative;">
                                        <input type="date" id="surgery_date" name="surgery_date"
                                            max="<?php echo e(date('Y-m-d')); ?>" style="width: 100%;" />
                                        <button type="button" class="clear-date-btn" data-target="surgery_date"
                                            style="display: none;">
                                            ✕
                                        </button>
                                    </div>
                                    <span
                                        class="helper-text"><?php echo e(__('Must wait :days days after surgery', ['days' => $settings->min_days_after_surgery])); ?></span>
                                    <span class="error-message"></span>
                                </div>
                            </div>
                        </div>

                        <div id="eligibility-status-box" style="display: none;" class="info-box">
                            <div class="info-icon">⚠️</div>
                            <div class="info-content">
                                <strong id="eligibility-status-title"><?php echo e(__('Eligibility Status')); ?></strong>
                                <p id="eligibility-status-message"></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step3">
                        <h2 class="step-title"><?php echo e(__('Review Information')); ?></h2>

                        <!-- Personal Information Section -->
                        <div style="margin-bottom: 2.5rem;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                                <span>👤</span>
                                <span><?php echo e(__('Personal Information')); ?></span>
                            </h3>
                            <div class="review-grid" id="personalInfoReview"></div>
                        </div>

                        <!-- Health Information Section -->
                        <div style="margin-bottom: 2.5rem;">
                            <h3
                                style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
                                <span>💚</span>
                                <span><?php echo e(__('Health Profile')); ?></span>
                            </h3>
                            <div class="review-grid" id="healthInfoReview"></div>
                        </div>

                        <div style="margin-bottom: 2.5rem;" id="eligibility-review-container">
                            <div id="eligibility-review-box" style="display: none;" class="info-box">
                                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                                    <div class="info-icon" id="eligibility-review-icon"
                                        style="font-size: 1.5rem; flex-shrink: 0;">✓</div>
                                    <div class="info-content" style="flex: 1;">
                                        <strong id="eligibility-review-title"
                                            style="font-size: 1.1rem; display: block; margin-bottom: 0.5rem;"><?php echo e(__('Eligibility Status')); ?></strong>
                                        <p id="eligibility-review-message"
                                            style="margin: 0; line-height: 1.6; color: #4b5563;"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Note Section -->
                        <div style="margin-bottom: 2.5rem;">
                            <div class="info-box" style="background: #f3f4f6; border-color: #d1d5db;">
                                <div class="info-icon">ℹ️</div>
                                <div class="info-content">
                                    <strong><?php echo e(__('Important note:')); ?></strong>
                                    <p style="margin: 0.5rem 0 0 0; line-height: 1.6;">
                                        <?php echo e(__('All data above has been verified. A confirmation message will be sent to your email.')); ?>

                                        <br>
                                        <?php echo e(__('You can update your additional information (blood type and more) later from your dashboard.')); ?>

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
                                    <input type="checkbox" id="termsAgree" name="terms" required
                                        style="margin-top: 0.25rem;" />
                                    <span class="checkbox-custom" style="margin-top: 0.25rem;"></span>
                                    <span class="checkbox-text" style="padding: 0;">
                                        <?php echo e(__('I acknowledge that I have read and agreed to')); ?>

                                        <a href="<?php echo e(route('terms')); ?>" target="_blank" class="terms-link"
                                            style="color: #dc2626; text-decoration: underline;"><?php echo e(__('Terms of Service')); ?></a>
                                        <?php echo e(__('and')); ?> <a href="javascript:void(0)"
                                            @click.prevent="$dispatch('open-modal', 'privacyModal')"
                                            class="terms-link"
                                            style="color: #dc2626; text-decoration: underline;"><?php echo e(__('Privacy Policy')); ?></a>
                                        <?php echo e(__('and policies related to personal data')); ?>

                                        <span class="required">*</span>
                                    </span>
                                </label>
                                <span class="error-message" style="display: block; margin-top: 0.5rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-outline btn-prev" id="prevBtn"
                            style="display: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7" />
                            </svg>
                            <span><?php echo e(__('Previous')); ?></span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                            <span><?php echo e(__('Next')); ?></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button type="submit" class="btn btn-primary btn-submit" id="submitBtn"
                            style="display: none;">
                            <span><?php echo e(__('Create Account')); ?></span>
                            <span class="btn-loader"></span>
                        </button>
                    </div>
                </form>
                <div class="modal-overlay" id="ineligibleModal">
                    <div class="modal-content">
                        <div class="modal-icon" style="background: #fef3c7; color: #d97706;">❤️</div>
                        <h2><?php echo e(__('Thank you for your noble initiative')); ?></h2>
                        <p>
                            <?php echo e(__('We appreciate your high desire to help and save lives.')); ?>

                            <br><br>
                            <?php echo e(__('Based on the health information provided (existence of chronic diseases), and out of concern for your personal safety first, we cannot accept your registration as a donor at this time.')); ?>

                            <br><br>
                            <?php echo e(__('You can always contribute to spreading awareness and supporting the cause in other ways.')); ?>

                        </p>
                        <button class="btn btn-outline" onclick="window.location.href='<?php echo e(route('home')); ?>'">
                            <?php echo e(__('Return to Home')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/scripts/pages/registration-donor.js')); ?>?v=1.3"></script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $attributes = $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $component = $__componentOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\register-donor.blade.php ENDPATH**/ ?>