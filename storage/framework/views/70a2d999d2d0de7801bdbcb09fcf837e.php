<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Reset Password')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/reset-password.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="rp-section">

        
        <div class="rp-background">
            <div class="rp-blob rp-blob-1"></div>
            <div class="rp-blob rp-blob-2"></div>
            <div class="rp-blob rp-blob-3"></div>
        </div>

        <div class="rp-container">

            
            <div class="rp-card">

                
                <div class="rp-header">
                    <div class="rp-icon-wrap">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h1><?php echo e(__('Reset Password')); ?></h1>
                    <p><?php echo e(__('Please enter your email and your new password.')); ?></p>
                </div>

                
                <form class="rp-form" method="POST" action="<?php echo e(route('password.store')); ?>" id="resetPasswordForm">
                    <?php echo csrf_field(); ?>

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="<?php echo e($request->route('token')); ?>">

                    <!-- Email Address -->
                    <div class="rp-form-group">
                        <label for="email"><?php echo e(__('Email Address')); ?></label>
                        <div class="rp-input-wrapper">
                            <input type="email" id="email" name="email"
                                value="<?php echo e(old('email', $request->email)); ?>" placeholder="you@example.com" required
                                autofocus autocomplete="username"
                                class="<?php echo e($errors->has('email') ? 'is-invalid' : ''); ?>" />
                            <i class="fa-solid fa-envelope rp-input-icon"></i>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="rp-form-group">
                        <label for="password"><?php echo e(__('New Password')); ?></label>
                        <div class="rp-input-wrapper">
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="new-password"
                                class="<?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>" />
                            <i class="fa-solid fa-lock rp-input-icon"></i>
                            <button type="button" class="rp-toggle-btn"
                                aria-label="<?php echo e(__('Toggle password visibility')); ?>">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>

                        <!-- Optional Password Strength Indicator -->
                        <div class="rp-strength" id="passwordStrength" style="display: none;">
                            <div class="rp-strength-bar">
                                <div class="rp-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="rp-strength-text" id="strengthText"><?php echo e(__('Weak')); ?></span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="rp-form-group">
                        <label for="password_confirmation"><?php echo e(__('Confirm New Password')); ?></label>
                        <div class="rp-input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••" required autocomplete="new-password"
                                class="<?php echo e($errors->has('password_confirmation') ? 'is-invalid' : ''); ?>" />
                            <i class="fa-solid fa-shield-halved rp-input-icon"></i>
                            <button type="button" class="rp-toggle-btn"
                                aria-label="<?php echo e(__('Toggle password visibility')); ?>">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="submit" class="rp-submit-btn" id="submitBtn">
                        <i class="fa-solid fa-floppy-disk rp-btn-text"></i>
                        <span class="rp-btn-text"><?php echo e(__('Save New Password')); ?></span>
                        <span class="rp-btn-loader"></span>
                    </button>
                </form>

                
                <div class="rp-back-link">
                    <p>
                        <?php echo e(__('Remembered your password?')); ?>

                        <a href="<?php echo e(route('login')); ?>">
                            <i class="fa-solid fa-arrow-right"></i>
                            <?php echo e(__('Back to Login')); ?>

                        </a>
                    </p>
                </div>
            </div>

            
            <div class="rp-illustration">
                <div class="rp-illustration-content">

                    <span class="rp-illustration-icon">🔐</span>

                    <h2><?php echo e(__('Secure Your Account')); ?></h2>
                    <p><?php echo e(__('Choose a strong password to protect your data. We recommend using a mix of letters, numbers, and symbols.')); ?>

                    </p>

                    <div class="rp-rules">
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?php echo e(__('At least 8 characters')); ?></span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?php echo e(__('Uppercase and lowercase letters')); ?></span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?php echo e(__('At least one number')); ?></span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?php echo e(__('One special symbol (!@#$)')); ?></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Toggle Password Visibility
                const toggleBtns = document.querySelectorAll('.rp-toggle-btn');
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const input = this.previousElementSibling
                            .previousElementSibling; // The input field
                        const icon = this.querySelector('i');

                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                            icon.style.color = '#d32f2f'; // Active color
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                            icon.style.color = ''; // Reset to CSS default
                        }
                    });
                });

                // Simple Password Strength Indicator
                const passwordInput = document.getElementById('password');
                const strengthContainer = document.getElementById('passwordStrength');
                const fillBar = document.getElementById('strengthFill');
                const textLabel = document.getElementById('strengthText');

                if (passwordInput && strengthContainer) {
                    passwordInput.addEventListener('input', function() {
                        const val = this.value;
                        if (val.length > 0) {
                            strengthContainer.style.display = 'block';

                            let strength = 0;
                            if (val.length >= 8) strength += 25;
                            if (val.match(/[A-Z]/) && val.match(/[a-z]/)) strength += 25;
                            if (val.match(/[0-9]/)) strength += 25;
                            if (val.match(/[^a-zA-Z0-9]/)) strength += 25;

                            fillBar.style.width = strength + '%';

                            if (strength <= 25) {
                                fillBar.style.background = '#ef4444'; // Red
                                textLabel.textContent = "<?php echo e(__('Very Weak')); ?>";
                                textLabel.style.color = '#ef4444';
                            } else if (strength <= 50) {
                                fillBar.style.background = '#f59e0b'; // Orange
                                textLabel.textContent = "<?php echo e(__('Weak')); ?>";
                                textLabel.style.color = '#f59e0b';
                            } else if (strength <= 75) {
                                fillBar.style.background = '#10b981'; // Green
                                textLabel.textContent = "<?php echo e(__('Good')); ?>";
                                textLabel.style.color = '#10b981';
                            } else {
                                fillBar.style.background = '#059669'; // Dark Green
                                textLabel.textContent = "<?php echo e(__('Strong')); ?>";
                                textLabel.style.color = '#059669';
                            }
                        } else {
                            strengthContainer.style.display = 'none';
                            fillBar.style.width = '0%';
                        }
                    });
                }

                // Loading state on submit
                const form = document.getElementById('resetPasswordForm');
                const btn = document.getElementById('submitBtn');

                if (form && btn) {
                    form.addEventListener('submit', () => {
                        // Only show loading if form is valid enough to submit natively
                        btn.classList.add('loading');
                        btn.disabled = true;
                    });
                }
            });
        </script>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\reset-password.blade.php ENDPATH**/ ?>