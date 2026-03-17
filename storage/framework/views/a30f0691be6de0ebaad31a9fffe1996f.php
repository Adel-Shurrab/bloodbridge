<nav class="navbar" id="navbar" role="navigation" aria-label="<?php echo e(__('Main Navigation')); ?>">
    <div class="nav-container">
        <a href="<?php echo e(route('home')); ?>" class="logo"
            aria-label="<?php echo e($settings->getTranslation('site_name')); ?> - <?php echo e(__('Homepage')); ?>">
            <img src="<?php echo e($settings->site_logo ? Storage::url($settings->site_logo) : asset('assets/images/logo.png')); ?>"
                alt="<?php echo e($settings->getTranslation('site_name')); ?> Logo" class="logo-icon" />
            <span><?php echo e($settings->getTranslation('site_name')); ?></span>
        </a>
        <ul class="nav-links">
            <li><a href="<?php echo e(route('home')); ?>" aria-current="page"><?php echo e(__('Home')); ?></a></li>
            <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(__('Features')); ?></a></li>
            <li><a href="<?php echo e(route('home')); ?>#how-it-works"><?php echo e(__('How it Works')); ?></a></li>
            <li><a href="<?php echo e(route('about')); ?>"><?php echo e(__('About Us')); ?></a></li>
            <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(__('Contact Us')); ?></a></li>
        </ul>
        <div class="nav-buttons">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(auth()->user()->getDashboardUrl()); ?>" class="btn btn-primary"><?php echo e(__('Dashboard')); ?></a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline"><?php echo e(__('Log Out')); ?></button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline"><?php echo e(__('Log In')); ?></a>
                <a href="<?php echo e(route('register.selection')); ?>" class="btn btn-primary"><?php echo e(__('Register')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="lang-switcher">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
                    <a href="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true)); ?>"
                        class="lang-link">
                        <span class="flag-icon">🇺🇸</span>
                        <span>English</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true)); ?>"
                        class="lang-link">
                        <span class="flag-icon">🇸🇦</span>
                        <span>العربية</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="<?php echo e(__('Open Menu')); ?>" aria-expanded="false"
            aria-controls="mobile-nav">☰</button>
    </div>
</nav>

<div class="mobile-nav" id="mobile-nav" aria-hidden="true">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="<?php echo e(__('Close Menu')); ?>">×</button>
    <ul class="mobile-nav-links">
        <li><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
        <li><a href="<?php echo e(route('home')); ?>#features"><?php echo e(__('Features')); ?></a></li>
        <li><a href="<?php echo e(route('home')); ?>#how-it-works"><?php echo e(__('How it Works')); ?></a></li>
        <li><a href="<?php echo e(route('about')); ?>"><?php echo e(__('About Us')); ?></a></li>
        <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(__('Contact Us')); ?></a></li>
    </ul>
    <div class="mobile-nav-buttons">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(auth()->user()->getDashboardUrl()); ?>" class="btn btn-primary"><?php echo e(__('Dashboard')); ?></a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: block; width: 100%;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline" style="width: 100%;"><?php echo e(__('Log Out')); ?></button>
            </form>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-outline"><?php echo e(__('Log In')); ?></a>
            <a href="<?php echo e(route('register.selection')); ?>" class="btn btn-primary"><?php echo e(__('Register')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mobile-lang-switcher" style="margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
                <a href="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true)); ?>"
                    class="lang-link"
                    style="display: flex; align-items: center; gap: 0.5rem; justify-content: center; color: #4b5563;">
                    <span class="flag-icon">🇺🇸</span>
                    <span>English</span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true)); ?>"
                    class="lang-link"
                    style="display: flex; align-items: center; gap: 0.5rem; justify-content: center; color: #4b5563;">
                    <span class="flag-icon">🇸🇦</span>
                    <span>العربية</span>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\components\navbar.blade.php ENDPATH**/ ?>