<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Home')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/index.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1><?php echo e($settings->getTranslation('home_hero_title')); ?></h1>
                <p><?php echo e($settings->getTranslation('home_hero_subtitle')); ?></p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap">
                    <a href="<?php echo e(route('register.donor')); ?>" class="btn btn-primary"
                        style="font-size: 1.1rem; padding: 1rem 2.5rem"><?php echo e(__('Donate Now')); ?></a>
                    <a href="#how-it-works" class="btn btn-outline"><?php echo e(__('Read More')); ?></a>
                </div>
                <div class="hero-stats">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo e($stats['donations_count']); ?></span>
                        <span class="stat-label"><?php echo e(__('Completed Donations')); ?></span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number"><?php echo e($stats['orgs_count']); ?></span>
                        <span class="stat-label"><?php echo e(__('Partner Hospital')); ?></span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number"><?php echo e($stats['lives_saved']); ?></span>
                        <span class="stat-label"><?php echo e(__('Lives Saved')); ?></span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?php echo e($settings->home_hero_image ? Storage::url($settings->home_hero_image) : asset('assets/images/hero-image.jpg')); ?>"
                    alt="<?php echo e($settings->getTranslation('home_hero_title')); ?>" class="hero-image-img" />
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2><?php echo e($settings->getTranslation('home_features_title')); ?></h2>
            <p><?php echo e($settings->getTranslation('home_features_subtitle')); ?></p>
        </div>
        <div class="features-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('home_features') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="feature-card">
                    <div class="feature-icon"><?php echo e($feature['icon'] ?? '📁'); ?></div>
                    <h3><?php echo e($feature['title'] ?? ''); ?></h3>
                    <p><?php echo e($feature['text'] ?? ''); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <h2><?php echo e(__('How it Works?')); ?></h2>
        </div>
        <div class="tabs">
            <button class="tab-btn active" data-tab="donor"><?php echo e(__('As a Donor')); ?></button>
            <button class="tab-btn" data-tab="org"><?php echo e(__('As an Organization')); ?></button>
        </div>
        <div class="tab-content-wrapper">
            <div class="steps-grid active" id="donor-steps">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('home_how_it_works_donor') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="step-card">
                        <div class="step-number"><?php echo e($loop->iteration); ?></div>
                        <h3><?php echo e($step['title'] ?? ''); ?></h3>
                        <p><?php echo e($step['text'] ?? ''); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="steps-grid" id="org-steps">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('home_how_it_works_org') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="step-card">
                        <div class="step-number"><?php echo e($loop->iteration); ?></div>
                        <h3><?php echo e($step['title'] ?? ''); ?></h3>
                        <p><?php echo e($step['text'] ?? ''); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2><?php echo e($settings->getTranslation('home_cta_title')); ?></h2>
        <p><?php echo e($settings->getTranslation('home_cta_subtitle')); ?></p>
        <a href="<?php echo e(route('register.selection')); ?>" class="btn"><?php echo e(__('Register Now')); ?></a>
    </section>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\pages\home.blade.php ENDPATH**/ ?>