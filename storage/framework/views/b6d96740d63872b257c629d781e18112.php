<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name' => 'privacyModal']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name' => 'privacyModal']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => $name,'maxWidth' => '2xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'maxWidth' => '2xl']); ?>
    <div class="privacy-modal-container"
        style="background: #ffffff; color: #2d3748; font-family: 'Cairo', sans-serif; position: relative; overflow: hidden; width: 100%;">

        <!-- Decorative Background Element -->
        <div
            style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: rgba(211, 47, 47, 0.03); border-radius: 50%; z-index: 0;">
        </div>

        <style>
            .privacy-modal-container {
                padding: 1.5rem;

                text-align: start;
            }

            .privacy-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #f7fafc;
                position: relative;
                z-index: 1;
            }

            .privacy-modal-title-wrapper {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .privacy-modal-title {
                font-size: 1.5rem;
                font-weight: 800;
                color: #d32f2f;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin: 0;
            }

            .privacy-last-updated {
                font-size: 0.7rem;
                color: #a0aec0;
                font-weight: 600;
                margin-inline-start: 2.5rem;
            }

            .privacy-modal-close {
                background: #f7fafc;
                border: none;
                color: #718096;
                cursor: pointer;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .privacy-modal-close:hover {
                background: #edf2f7;
                color: #2d3748;
                transform: rotate(90deg);
            }

            .privacy-modal-content {
                max-height: 60vh;
                overflow-y: auto;
                padding-inline-end: 1rem;
                padding-inline-start: 0.25rem;
                position: relative;
                z-index: 1;
            }

            /* Custom Premium Scrollbar */
            .privacy-modal-content::-webkit-scrollbar {
                width: 6px;
            }

            .privacy-modal-content::-webkit-scrollbar-track {
                background: #f8fafc;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb:hover {
                background: #d32f2f;
            }

            .privacy-section {
                margin-bottom: 2rem;
                position: relative;
            }

            .privacy-section-header {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                margin-bottom: 0.75rem;
            }

            .privacy-section-icon {
                width: 28px;
                height: 28px;
                background: #fff5f5;
                color: #d32f2f;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.8rem;
            }

            .privacy-section h3 {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1a202c;
                margin: 0;
            }

            .privacy-section p {
                font-size: 0.95rem;
                line-height: 1.7;
                color: #4a5568;
                margin: 0;
            }

            .privacy-list {
                list-style: none;
                padding: 0;
                margin: 0.75rem 0 0 0;
                display: grid;
                gap: 0.5rem;
            }

            .privacy-list li {
                position: relative;
                padding-inline-start: 1.5rem;
                font-size: 0.9rem;
                color: #4a5568;
                line-height: 1.5;
            }

            .privacy-list li::before {
                content: '\f00c';
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                color: #38a169;
                position: absolute;
                right: 0;
                top: 2px;
                font-size: 0.75rem;
            }

            .privacy-highlight {
                background: linear-gradient(to left, #fff5f5, #ffffff);
                border-inline-start: 3px solid #d32f2f;
                padding: 1rem;
                border-radius: 0 6px 6px 0;
                font-weight: 600;
                color: #2d3748;
                font-size: 0.9rem;
                line-height: 1.6;
            }

            .privacy-modal-footer {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 2px solid #f7fafc;
                display: flex;
                justify-content: flex-end;
                z-index: 1;
                position: relative;
            }

            .close-btn {
                background: #d32f2f;
                color: white;
                border: none;
                padding: 0.6rem 2rem;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
                font-size: 0.95rem;
            }

            .close-btn:hover {
                background: #b71c1c;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
            }

            @media (max-width: 640px) {
                .privacy-modal-container {
                    padding: 1.25rem;
                }

                .privacy-modal-title {
                    font-size: 1.25rem;
                }

                .close-btn {
                    width: 100%;
                }
            }
        </style>

        <div class="privacy-modal-header">
            <div class="privacy-modal-title-wrapper">
                <h2 class="privacy-modal-title">
                    <i class="fas fa-shield-heart"></i>
                    <?php echo e(__('Privacy Policy')); ?>

                </h2>
                <span class="privacy-last-updated"><?php echo e(__('Last Updated: March 2026')); ?></span>
            </div>
            <button @click="$dispatch('close-modal', '<?php echo e($name); ?>')" class="privacy-modal-close"
                aria-label="<?php echo e(__('Close')); ?>">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="privacy-modal-content">
            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-info-circle"></i></div>
                    <h3><?php echo e(__('1. Introduction')); ?></h3>
                </div>
                <p>
                    <?php echo e(__('Welcome to :site. We place the highest importance on your privacy and the security of your health data, and we work according to the highest protection standards to ensure the confidentiality of your information and your trust in us as a bridge to save lives.', ['site' => app(\App\Settings\GeneralSettings::class)->getTranslation('site_name')])); ?>

                </p>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-id-card"></i></div>
                    <h3><?php echo e(__('2. Information We Collect')); ?></h3>
                </div>
                <ul class="privacy-list">
                    <li><strong><?php echo e(__('Personal Information:')); ?></strong>
                        <?php echo e(__('Name, mobile number, and ID number.')); ?></li>
                    <li><strong><?php echo e(__('Health Information:')); ?></strong>
                        <?php echo e(__('Blood type, medical conditions, and donation history.')); ?></li>
                    <li><strong><?php echo e(__('Location:')); ?></strong> <?php echo e(__('City and region to facilitate donation.')); ?>

                    </li>
                    <li><strong><?php echo e(__('Technical Data:')); ?></strong>
                        <?php echo e(__('IP address and browser type for security.')); ?></li>
                </ul>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <h3><?php echo e(__('3. How We Use Your Information')); ?></h3>
                </div>
                <ul class="privacy-list">
                    <li><?php echo e(__('Coordinating blood donation processes.')); ?></li>
                    <li><?php echo e(__('Sending emergency notifications.')); ?></li>
                    <li><?php echo e(__('Automatic verification of medical eligibility.')); ?></li>
                </ul>
            </div>

            <div class="privacy-section">
                <div class="privacy-highlight">
                    <i class="fas fa-user-lock" style="margin-inline-end: 0.5rem; color: #d32f2f;"></i>
                    <strong><?php echo e(__('Your Safety First:')); ?></strong>
                    <?php echo e(__('We do not sell your data. Your information is only shared with the concerned hospital when you accept a donation request.')); ?>

                </div>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-lock"></i></div>
                    <h3><?php echo e(__('4. Information Security and Protection')); ?></h3>
                </div>
                <ul class="privacy-list">
                    <li><?php echo e(__('Using advanced encryption protocols (SSL/TLS) to protect data during transmission.')); ?>

                    </li>
                    <li><?php echo e(__('Storing sensitive data using advanced AES-256 encryption algorithms.')); ?></li>
                    <li><?php echo e(__('24/7 monitoring systems to detect any unauthorized access attempts.')); ?></li>
                </ul>
            </div>

            <div class="privacy-section" style="margin-bottom: 0;">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-user-check"></i></div>
                    <h3><?php echo e(__('5. Your Rights')); ?></h3>
                </div>
                <p>
                    <?php echo e(__('You have the full right to access your data, correct it, or request to delete your account permanently via the contact page.')); ?>

                </p>
            </div>
        </div>

        <div class="privacy-modal-footer">
            <button @click="$dispatch('close-modal', '<?php echo e($name); ?>')" class="close-btn">
                <?php echo e(__('Close')); ?>

            </button>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\components\privacy-modal.blade.php ENDPATH**/ ?>