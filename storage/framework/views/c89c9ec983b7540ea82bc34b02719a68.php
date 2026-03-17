<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Forgot Password')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/forgot-password.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="fp-section">

        
        <div class="fp-background">
            <div class="fp-blob fp-blob-1"></div>
            <div class="fp-blob fp-blob-2"></div>
            <div class="fp-blob fp-blob-3"></div>
        </div>

        <div class="fp-container">

            
            <div class="fp-card">

                
                <div class="fp-header">
                    <div class="fp-icon-wrap">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <h1><?php echo e(__('Forgot Your Password?')); ?></h1>
                    <p><?php echo e(__('No worries! Enter your email and we\'ll send you a reset link.')); ?></p>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                    <div class="fp-status-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?php echo e(session('status')); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <form class="fp-form" method="POST" action="<?php echo e(route('password.email')); ?>" id="forgotPasswordForm">
                    <?php echo csrf_field(); ?>

                    <div class="fp-form-group">
                        <label for="email"><?php echo e(__('Email Address')); ?></label>
                        <div class="fp-input-wrapper">
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                                placeholder="you@example.com" required autofocus autocomplete="email"
                                class="<?php echo e($errors->has('email') ? 'is-invalid' : ''); ?>" />
                            <i class="fa-solid fa-envelope fp-input-icon"></i>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="fp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo e($message); ?>

                            </p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="submit" class="fp-submit-btn" id="submitBtn">
                        <i class="fa-solid fa-paper-plane btn-text"></i>
                        <span class="btn-text"><?php echo e(__('Send Reset Link')); ?></span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                
                <div class="fp-back-link">
                    <p>
                        <?php echo e(__('Remembered your password?')); ?>

                        <a href="<?php echo e(route('login')); ?>">
                            <i class="fa-solid fa-arrow-right"></i>
                            <?php echo e(__('Back to Login')); ?>

                        </a>
                    </p>
                </div>
            </div>

            
            <div class="fp-illustration">
                <div class="fp-illustration-content">

                    <span class="fp-illustration-icon">🔐</span>

                    <h2><?php echo e(__('Restore Access to Your Account')); ?></h2>
                    <p><?php echo e(__('Account security is our priority. Follow the simple steps to securely reset your password.')); ?>

                    </p>

                    <div class="fp-steps">
                        <div class="fp-step">
                            <span class="fp-step-num">1</span>
                            <span class="fp-step-text"><?php echo e(__('Enter your registered email address')); ?></span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">2</span>
                            <span class="fp-step-text"><?php echo e(__('Check your email for the reset link')); ?></span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">3</span>
                            <span class="fp-step-text"><?php echo e(__('Click the link and create a new password')); ?></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Loading state on submit
            const form = document.getElementById('forgotPasswordForm');
            const btn = document.getElementById('submitBtn');

            form.addEventListener('submit', () => {
                btn.classList.add('loading');
                btn.disabled = true;
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\forgot-password.blade.php ENDPATH**/ ?>