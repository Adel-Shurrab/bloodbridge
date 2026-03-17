<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('About Us')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/about.css')); ?>" />
    <?php $__env->stopPush(); ?>

    <section class="about-hero">
        <div class="about-hero-container">
            <div class="hero-badge"><?php echo e(__('About Us')); ?></div>
            <h1><?php echo e($settings->getTranslation('about_hero_title')); ?></h1>
            <p><?php echo e($settings->getTranslation('about_hero_subtitle')); ?></p>
        </div>
    </section>

    <section class="mission-vision">
        <div class="container-content">
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="card-icon mission-icon">🎯</div>
                    <h2><?php echo e($settings->getTranslation('about_mission_title1')); ?></h2>
                    <p><?php echo e($settings->getTranslation('about_mission_text1')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->about_mission_image1): ?>
                        <img src="<?php echo e(Storage::disk('public')->url($settings->about_mission_image1)); ?>"
                            alt="<?php echo e($settings->getTranslation('about_mission_title1')); ?>"
                            style="max-width: 100%; border-radius: 10px; margin-top: 1rem;">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="mission-card">
                    <div class="card-icon vision-icon">👁️</div>
                    <h2><?php echo e($settings->getTranslation('about_mission_title2')); ?></h2>
                    <p><?php echo e($settings->getTranslation('about_mission_text2')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->about_mission_image2): ?>
                        <img src="<?php echo e(Storage::disk('public')->url($settings->about_mission_image2)); ?>"
                            alt="<?php echo e($settings->getTranslation('about_mission_title2')); ?>"
                            style="max-width: 100%; border-radius: 10px; margin-top: 1rem;">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container-content">
            <div class="section-header">
                <h2><?php echo e($settings->getTranslation('about_values_title')); ?></h2>
                <p><?php echo e($settings->getTranslation('about_values_subtitle')); ?></p>
            </div>
            <div class="values-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('about_values') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="value-card">
                        <div class="value-icon">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value['image'] ?? null): ?>
                                <img src="<?php echo e(Storage::disk('public')->url($value['image'])); ?>"
                                    alt="<?php echo e($value['title']); ?>"
                                    style="width: 40px; height: 40px; object-fit: contain;">
                            <?php elseif($value['icon'] ?? null): ?>
                                <?php echo $value['icon']; ?>

                            <?php else: ?>
                                💎
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <h3><?php echo e($value['title'] ?? ''); ?></h3>
                        <p><?php echo e($value['text'] ?? ''); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container-content">
            <div class="section-header">
                <h2><?php echo e($settings->getTranslation('about_team_title')); ?></h2>
                <p><?php echo e($settings->getTranslation('about_team_subtitle')); ?></p>
            </div>

            <div class="team-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('about_team_members') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="team-card">
                        <div class="team-image">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['image'] ?? null): ?>
                                <img src="<?php echo e(Storage::disk('public')->url($member['image'])); ?>"
                                    alt="<?php echo e($member['name']); ?>"
                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <div class="team-image-placeholder">👤</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <h3><?php echo e($member['name'] ?? ''); ?></h3>
                        <p class="team-role"><?php echo e($member['role'] ?? ''); ?></p>
                        <p class="team-desc"><?php echo e($member['bio'] ?? ''); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="impact-section">
        <div class="container-content">
            <div class="section-header">
                <h2><?php echo e($settings->getTranslation('about_impact_title')); ?></h2>
                <p><?php echo e($settings->getTranslation('about_impact_text')); ?></p>
            </div>
            <div class="impact-stats">
                <div class="impact-card">
                    <div class="impact-number" data-target="<?php echo e($stats['donations_count']); ?>">0</div>
                    <p><?php echo e(__('Completed Donations')); ?></p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="<?php echo e($stats['orgs_count']); ?>">0</div>
                    <p><?php echo e(__('Partner Hospital')); ?></p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="<?php echo e($stats['lives_saved']); ?>">0</div>
                    <p><?php echo e(__('Lives Saved')); ?></p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="<?php echo e($stats['donors_count']); ?>">0</div>
                    <p><?php echo e(__('Active Donor')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2><?php echo e($settings->getTranslation('about_join_title')); ?></h2>
        <p><?php echo e($settings->getTranslation('about_join_subtitle')); ?></p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo e(route('contact')); ?>" class="btn"><?php echo e(__('Contact Us')); ?></a>
            <a href="<?php echo e(route('register.selection')); ?>" class="btn btn-outline-white"><?php echo e(__('Register Now')); ?></a>
        </div>
    </section>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/scripts/pages/about.js')); ?>"></script>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\pages\about.blade.php ENDPATH**/ ?>