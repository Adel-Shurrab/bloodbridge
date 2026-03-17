<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Register New Organization')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/registration-organization.css')); ?>" />
        <script>
            window.translations = {
                'Org name required': '<?php echo e(__('Organization name is required')); ?>',
                'Opening time required': '<?php echo e(__('Opening time is required')); ?>',
                'Closing time required': '<?php echo e(__('Closing time is required')); ?>',
                'Min one working day': '<?php echo e(__('Please choose at least one working day')); ?>',
                'Daily capacity required': '<?php echo e(__('Daily capacity is required and must be greater than 0')); ?>',
                'Email is required': '<?php echo e(__('Email is required')); ?>',
                'Invalid email address': '<?php echo e(__('Invalid email address')); ?>',
                'Mobile number is required': '<?php echo e(__('Mobile number is required')); ?>',
                'Invalid mobile number': '<?php echo e(__('Invalid mobile number')); ?>',
                'Governorate is required': '<?php echo e(__('Governorate is required')); ?>',
                'License number required': '<?php echo e(__('License number is required')); ?>',
                'Please upload license': '<?php echo e(__('Please upload the license')); ?>',
                'Contact name required': '<?php echo e(__('Contact name is required')); ?>',
                'Job title required': '<?php echo e(__('Job title is required')); ?>',
                'Password is required': '<?php echo e(__('Password is required')); ?>',
                'Password must be at least 8 characters': '<?php echo e(__('Password must be at least 8 characters')); ?>',
                'Confirm password is required': '<?php echo e(__('Confirm password is required')); ?>',
                'Passwords do not match': '<?php echo e(__('Passwords do not match')); ?>',
                'Must agree to terms': '<?php echo e(__('You must agree to the terms and conditions')); ?>',
                'Not specified': '<?php echo e(__('Not specified')); ?>',
                'Working 24/7': '<?php echo e(__('Works 24/7')); ?>',
                'File too large': '<?php echo e(__('File size must be less than 5MB')); ?>',
                'File type not supported': '<?php echo e(__('File type not supported. Please choose PDF or image')); ?>'
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

    <section class="registration-organization-section">
        <div class="registration-container">
            <div class="registration-header">
                <div class="header-icon">🏥</div>
                <h1><?php echo e($settings->getTranslation('org_register_title')); ?></h1>
                <p><?php echo e($settings->getTranslation('org_register_subtitle')); ?></p>
            </div>

            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label"><?php echo e(__('Organization Information')); ?></div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label"><?php echo e(__('Contact Information')); ?></div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label"><?php echo e(__('Documentation & Management')); ?></div>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label"><?php echo e(__('Review and Confirmation')); ?></div>
                </div>
            </div>

            <div class="form-container">
                <form id="organizationRegistrationForm" method="POST"
                    action="<?php echo e(route('register.organization.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                        <div class="info-box" style="background: #fee2e2; border-color: #ef4444; margin-bottom: 2rem;">
                            <div class="info-icon">⚠️</div>
                            <div class="info-content">
                                <strong
                                    style="color: #b91c1c;"><?php echo e(__('Please correct the following errors to submit the request:')); ?></strong>
                                <ul style="margin: 0.5rem 1rem 0 0; padding: 0; color: #b91c1c; font-size: 0.95rem;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="form-step active" id="step1">
                        <h2 class="step-title"><?php echo e(__('Organization Information')); ?></h2>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationName"><?php echo e(__('Organization Name')); ?> <span
                                        class="required">*</span></label>
                                <input type="text" id="organizationName" name="organizationName"
                                    value="<?php echo e(old('organizationName')); ?>"
                                    placeholder="<?php echo e(__('General Community Hospital')); ?>" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="checkbox-label inline">
                                    <input type="checkbox" id="isOpen247" name="is_24_hours" value="1" />
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text"><?php echo e(__('The organization operates 24/7')); ?></span>
                                </label>
                            </div>
                        </div>

                        <div id="operatingHoursContainer">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="opening_time"><?php echo e(__('Opening Time')); ?> <span
                                            class="required">*</span></label>
                                    <input type="time" id="opening_time" name="opening_time"
                                        value="<?php echo e(old('opening_time')); ?>" />
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="closing_time"><?php echo e(__('Closing Time')); ?> <span
                                            class="required">*</span></label>
                                    <input type="time" id="closing_time" name="closing_time"
                                        value="<?php echo e(old('closing_time')); ?>" />
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="info-box mini" style="margin: 0 0 1.5rem 0; padding: 0.75rem 1rem;">
                                <div class="info-icon">💡</div>
                                <div class="info-content">
                                    <p style="font-size: 0.85rem;">
                                        <?php echo e(__('Providing operating hours helps donors choose the appropriate time to visit you.')); ?>

                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label><?php echo e(__('Working Days')); ?> <span class="required">*</span></label>
                                <div class="checkbox-grid">
                                    <?php
                                        $days = [
                                            'Saturday' => __('Saturday'),
                                            'Sunday' => __('Sunday'),
                                            'Monday' => __('Monday'),
                                            'Tuesday' => __('Tuesday'),
                                            'Wednesday' => __('Wednesday'),
                                            'Thursday' => __('Thursday'),
                                            'Friday' => __('Friday'),
                                        ];
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="checkbox-label inline">
                                            <input type="checkbox" name="working_days[]" value="<?php echo e($value); ?>"
                                                <?php echo e(is_array(old('working_days')) && in_array($value, old('working_days')) ? 'checked' : ''); ?> />
                                            <span class="checkbox-custom"></span>
                                            <span class="checkbox-text"><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="daily_capacity"><?php echo e(__('Daily Capacity (Number of Donors)')); ?> <span
                                        class="required">*</span></label>
                                <input type="number" id="daily_capacity" name="daily_capacity"
                                    value="<?php echo e(old('daily_capacity')); ?>" min="1"
                                    placeholder="<?php echo e(__('Example: 50')); ?>" />
                                <span class="error-message"></span>
                                <span
                                    class="helper-text"><?php echo e(__('The estimated number of donors the organization can receive daily. Entering this number helps us organize the flow of donors.')); ?></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="organizationDescription"><?php echo e(__('Organization Description')); ?></label>
                                <textarea id="organizationDescription" name="organizationDescription" rows="4"
                                    placeholder="<?php echo e(__('Briefly describe your organization\'s role')); ?>"><?php echo e(old('organizationDescription')); ?></textarea>
                                <span
                                    class="helper-text"><?php echo e(__('This will help us understand how to collaborate with you better')); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <h2 class="step-title"><?php echo e(__('Contact Information')); ?></h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contactEmail"><?php echo e(__('Contact Email')); ?> <span
                                        class="required">*</span></label>
                                <input type="email" id="contactEmail" name="contactEmail"
                                    value="<?php echo e(old('contactEmail')); ?>" placeholder="contact@organization.com" />
                                <span class="error-message"></span>
                            </div>

                            <div class="form-group">
                                <label for="contactPhone"><?php echo e(__('Contact Mobile Number')); ?> <span
                                        class="required">*</span></label>
                                <input type="tel" id="contactPhone" name="contactPhone"
                                    value="<?php echo e(old('contactPhone')); ?>" placeholder="0599xxxxxx" />
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="governorate_id"><?php echo e(__('Governorate')); ?> <span
                                        class="required">*</span></label>
                                <select id="governorate_id" name="governorate_id">
                                    <option value="" disabled selected><?php echo e(__('Select Governorate')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($gov->id); ?>"
                                            <?php echo e(old('governorate_id') == $gov->id ? 'selected' : ''); ?>>
                                            <?php echo e($gov->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="auto_location_address">
                                    <?php echo e(__('Organization\'s Automatic Location')); ?>

                                    <span style="color: #999; font-weight: normal;">(<?php echo e(__('Optional')); ?>)</span>
                                </label>
                                <div style="position: relative; display: flex; gap: 0.5rem;">
                                    <input type="text" id="auto_location_address" name="auto_location_address"
                                        value="<?php echo e(old('auto_location_address')); ?>"
                                        placeholder="<?php echo e(__('Click the location button to determine the organization\'s location automatically')); ?>"
                                        readonly style="flex: 1; background: #f9fafb; cursor: pointer;" />
                                    <button type="button" id="gps-location-btn" class="btn btn-outline"
                                        style="padding: 0.875rem 1.5rem; white-space: nowrap; min-width: auto;"
                                        title="<?php echo e(__('Determine location automatically')); ?>">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" style="display: inline-block;">
                                            <path d="M12 2v4m0 12v4M2 12h4m12 0h4" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span style="margin-inline-start: 0.5rem;"><?php echo e(__('Locate')); ?></span>
                                    </button>
                                </div>
                                <span
                                    class="helper-text"><?php echo e(__('This will help donors find the organization\'s location easily')); ?></span>
                                <span class="error-message"></span>

                                <!-- Hidden inputs for coordinates -->
                                <input type="hidden" id="auto_lat" name="auto_lat" value="<?php echo e(old('auto_lat')); ?>">
                                <input type="hidden" id="auto_lng" name="auto_lng" value="<?php echo e(old('auto_lng')); ?>">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="form-step" id="step3">
                <h2 class="step-title"><?php echo e(__('Documentation & Management')); ?></h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="licenseNumber"><?php echo e(__('Official License Number')); ?> <span
                                class="required">*</span></label>
                        <input type="text" id="licenseNumber" name="licenseNumber"
                            value="<?php echo e(old('licenseNumber')); ?>" placeholder="LIC-123456789" />
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="licenseUpload"><?php echo e(__('Upload License')); ?> <span class="required">*</span></label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="licenseUpload" name="licenseUpload"
                                accept=".pdf,.jpg,.jpeg,.png" class="file-input" />
                            <div class="file-upload-display" id="fileUploadDisplay">
                                <div class="file-icon">📄</div>
                                <div class="file-text">
                                    <span class="file-prompt"><?php echo e(__('Click to upload')); ?></span>
                                    <span class="file-hint"><?php echo e(__('PDF, JPG, PNG up to 5MB')); ?></span>
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
                        <label for="adminName"><?php echo e(__('Administrative Contact Name')); ?> <span
                                class="required">*</span></label>
                        <input type="text" id="adminName" name="adminName" value="<?php echo e(old('adminName')); ?>"
                            placeholder="<?php echo e(__('John Doe')); ?>" />
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="responsible_person_position"><?php echo e(__('Job Title')); ?> <span
                                class="required">*</span></label>
                        <input type="text" id="responsible_person_position" name="responsible_person_position"
                            value="<?php echo e(old('responsible_person_position')); ?>"
                            placeholder="<?php echo e(__('Public Relations Manager')); ?>" />
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adminEmail"><?php echo e(__('Administrator Email')); ?> <span
                                class="required">*</span></label>
                        <input type="email" id="adminEmail" name="adminEmail" value="<?php echo e(old('adminEmail')); ?>"
                            placeholder="admin@organization.com" />
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adminPassword"><?php echo e(__('Password')); ?> <span class="required">*</span></label>
                        <div class="password-input">
                            <input type="password" id="adminPassword" name="adminPassword" placeholder="••••••••" />
                            <button type="button" class="toggle-password" data-target="adminPassword">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        <span class="error-message"></span>
                        <span class="helper-text"><?php echo e(__('Must be at least 8 characters')); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="adminPassword_confirmation"><?php echo e(__('Confirm Password')); ?> <span
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
                <h2 class="step-title"><?php echo e(__('Review Information')); ?></h2>

                <div class="review-section">
                    <h3><?php echo e(__('Organization Information')); ?></h3>
                    <div class="review-grid" id="organizationInfoReview"></div>
                </div>

                <div class="review-section">
                    <h3><?php echo e(__('Contact Information')); ?></h3>
                    <div class="review-grid" id="contactInfoReview"></div>
                </div>

                <div class="review-section">
                    <h3><?php echo e(__('Administrative Details')); ?></h3>
                    <div class="review-grid" id="adminInfoReview"></div>
                </div>

                <div class="info-box">
                    <div class="info-icon">ℹ️</div>
                    <div class="info-content">
                        <strong><?php echo e(__('Note:')); ?></strong>
                        <p><?php echo e(__('Your request will be reviewed by our team. We will send you an email once your account is fully activated.')); ?>

                        </p>
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="termsAgree" name="termsAgree" required value="1" />
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text"><?php echo e(__('I have read and agreed to')); ?> <a
                                href="<?php echo e(route('terms')); ?>" target="_blank"
                                class="terms-link"><?php echo e(__('Terms of Service')); ?></a> <?php echo e(__('and')); ?> <a
                                href="javascript:void(0)" @click.prevent="$dispatch('open-modal', 'privacyModal')"
                                class="terms-link"><?php echo e(__('Privacy Policy')); ?></a> <span
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
                    <span><?php echo e(__('Previous')); ?></span>
                </button>
                <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                    <span><?php echo e(__('Next')); ?></span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </button>
                <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="display: none">
                    <span><?php echo e(__('Submit Request')); ?></span>
                    <span class="btn-loader"></span>
                </button>
            </div>
            </form>
        </div>
        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/scripts/pages/registration-organization.js')); ?>"></script>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\register-organization.blade.php ENDPATH**/ ?>