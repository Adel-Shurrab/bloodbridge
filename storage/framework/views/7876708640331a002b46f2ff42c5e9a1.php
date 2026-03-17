<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Log In')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/login.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="login-section">
        <div class="login-background">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
        </div>

        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="welcome-icon">👋</div>
                    <h1><?php echo e($settings->getTranslation('login_title')); ?></h1>
                    <p><?php echo e($settings->getTranslation('login_subtitle')); ?></p>
                </div>

                <form class="login-form" id="loginForm" method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="email"><?php echo e(__('Email')); ?></label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                                placeholder="you@example.com" required autofocus autocomplete="username" />
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has('email') || $errors->has('credentials')): ?>
                            <span class="error-message error-message--visible" role="alert">
                                <?php echo e($errors->has('credentials') ? $errors->first('credentials') : $errors->first('email')); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label for="password"><?php echo e(__('Password')); ?></label>
                            <a href="<?php echo e(route('password.request')); ?>"
                                class="forgot-link"><?php echo e(__('Forgot your password?')); ?></a>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="current-password" />
                            <button type="button" class="toggle-password" id="togglePassword"
                                aria-label="<?php echo e(__('Toggle password visibility')); ?>" aria-pressed="false">
                                <span class="eye-icon" aria-hidden="true">👁️</span>
                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error-message error-message--visible" role="alert"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="rememberMe" name="remember" />
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text"><?php echo e(__('Remember me')); ?></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login" id="loginBtn">
                        <span class="btn-text"><?php echo e(__('Log In')); ?></span>
                        <span class="btn-loader" aria-hidden="true"></span>
                    </button>
                </form>

                <div class="divider">
                    <span><?php echo e(__('OR')); ?></span>
                </div>

                <button class="btn btn-social" aria-label="<?php echo e(__('Log in with Google')); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" role="img"
                        aria-hidden="true">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                    </svg>
                    <span><?php echo e(__('Continue with Google')); ?></span>
                </button>

                <div class="signup-prompt">
                    <p>
                        <?php echo e(__("Don't have an account?")); ?>

                        <a href="<?php echo e(route('register.selection')); ?>"><?php echo e(__('Create New Account')); ?></a>
                    </p>
                </div>
            </div>

                <div class="login-illustration"
                    style="<?php echo e($settings->login_image ? 'background-image: url(' . Storage::url($settings->login_image) . ');' : ''); ?>">
                    <div class="illustration-content">
                        <div class="illustration-icon">❤️</div>
                        <h2><?php echo e($settings->getTranslation('login_title')); ?></h2>
                        <p><?php echo e($settings->getTranslation('login_subtitle')); ?></p>
                    <div class="stats-mini">
                        <div class="stat-mini">
                            <span class="stat-mini-number">5000+</span>
                            <span class="stat-mini-label"><?php echo e(__('Donors')); ?></span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">120</span>
                            <span class="stat-mini-label"><?php echo e(__('Hospitals')); ?></span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">4500</span>
                            <span class="stat-mini-label"><?php echo e(__('Lives Saved')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/scripts/pages/login.js')); ?>"></script>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\auth\login.blade.php ENDPATH**/ ?>