<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Verify Email')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/verify-email.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="ve-section">

        
        <div class="ve-background">
            <div class="ve-blob ve-blob-1"></div>
            <div class="ve-blob ve-blob-2"></div>
        </div>

        <div class="ve-container">

            
            <div class="ve-card">

                
                <div class="ve-header">
                    <div class="ve-icon-wrap">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h1><?php echo e(__('Verify Email Address')); ?></h1>
                    <p>
                        <?php echo e(__('Thanks for joining us! Before getting started, please verify your email address by clicking on the link we just sent you. If you didn\'t receive the email, we will gladly send you another one.')); ?>

                    </p>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') == 'verification-link-sent'): ?>
                    <div class="ve-status-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?php echo e(__('A new verification link has been sent to the email address you provided during registration.')); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="ve-actions">
                    <form method="POST" action="<?php echo e(route('verification.send')); ?>" id="resendVerificationForm">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ve-submit-btn" id="submitBtn">
                            <i class="fa-solid fa-paper-plane btn-text"></i>
                            <span class="btn-text"><?php echo e(__('Resend Verification Link')); ?></span>
                            <span class="btn-loader"></span>
                        </button>
                    </form>

                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="ve-logout-form">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ve-logout-btn">
                            <?php echo e(__('Log in with another account?')); ?>

                        </button>
                    </form>
                </div>

            </div>

            
            <div class="ve-illustration">
                <div class="ve-illustration-content">

                    <span class="ve-illustration-icon">🛡️</span>

                    <h2><?php echo e(__('Account Activation and Security')); ?></h2>
                    <p><?php echo e(__('To protect your data and ensure effective communication, we request confirmation of your email ownership.')); ?>

                    </p>

                    <div class="ve-steps">
                        <div class="ve-step">
                            <span class="ve-step-num">1</span>
                            <span class="ve-step-text"><?php echo e(__('Open your email inbox')); ?></span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">2</span>
                            <span
                                class="ve-step-text"><?php echo e(__('Look for the activation message from our platform')); ?></span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">3</span>
                            <span
                                class="ve-step-text"><?php echo e(__('Click on the verification button to get started')); ?></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Loading state on submit
            const form = document.getElementById('resendVerificationForm');
            const btn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', () => {
                    btn.classList.add('loading');
                    btn.disabled = true;
                });
            }
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\verify-email.blade.php ENDPATH**/ ?>