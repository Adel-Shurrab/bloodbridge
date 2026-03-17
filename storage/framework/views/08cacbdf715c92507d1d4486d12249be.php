<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>"
    dir="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocaleDirection()); ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description"
        content="<?php echo e($description ?? ($settings->getTranslation('seo_description') ?? __(':site_name - A smart system connecting donors with those in need. Donate blood and save lives today.', ['site_name' => $settings->getTranslation('site_name')]))); ?>" />
    <meta name="keywords"
        content="<?php echo e($settings->getTranslation('seo_keywords') ?? __('blood donation, blood bridge, blood transfusion, donors, hospitals')); ?>" />
    <meta name="author" content="<?php echo e($settings->getTranslation('site_name')); ?>" />
    <meta name="theme-color" content="#DC143C" />
    <meta property="og:title"
        content="<?php echo e($title ?? ($settings->getTranslation('seo_title') ?? __(':site_name - Blood Donation System', ['site_name' => $settings->getTranslation('site_name')]))); ?>" />
    <meta property="og:description"
        content="<?php echo e($description ?? ($settings->getTranslation('seo_description') ?? __('A smart system connecting donors with those in need'))); ?>" />
    <meta property="og:type" content="website" />
    <script>
        window.appConfig = {
            locale: "<?php echo e(str_replace('_', '-', app()->getLocale())); ?>",
            dir: "<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>"
        };
    </script>

    <title>
        <?php echo e($title ?? ($settings->getTranslation('seo_title') ?? __(':site_name - Saving lives drop by drop', ['site_name' => $settings->getTranslation('site_name')]))); ?>

    </title>

    <link rel="icon"
        href="<?php echo e($settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png')); ?>" />
    <link rel="shortcut icon"
        href="<?php echo e($settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png')); ?>" />

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/main.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/layout/navbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/layout/footer.css')); ?>" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body x-data>
    <div class="overlay" id="overlay"></div>

    <?php if (isset($component)) { $__componentOriginala591787d01fe92c5706972626cdf7231 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala591787d01fe92c5706972626cdf7231 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navbar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $attributes = $__attributesOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__attributesOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $component = $__componentOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__componentOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>

    <main>
        <?php echo e($slot); ?>

    </main>

    <?php if (isset($component)) { $__componentOriginal8a8716efb3c62a45938aca52e78e0322 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a8716efb3c62a45938aca52e78e0322 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $attributes = $__attributesOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__attributesOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a8716efb3c62a45938aca52e78e0322)): ?>
<?php $component = $__componentOriginal8a8716efb3c62a45938aca52e78e0322; ?>
<?php unset($__componentOriginal8a8716efb3c62a45938aca52e78e0322); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal338aa338eb284528f446e4fba0beac51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal338aa338eb284528f446e4fba0beac51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.privacy-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('privacy-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal338aa338eb284528f446e4fba0beac51)): ?>
<?php $attributes = $__attributesOriginal338aa338eb284528f446e4fba0beac51; ?>
<?php unset($__attributesOriginal338aa338eb284528f446e4fba0beac51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal338aa338eb284528f446e4fba0beac51)): ?>
<?php $component = $__componentOriginal338aa338eb284528f446e4fba0beac51; ?>
<?php unset($__componentOriginal338aa338eb284528f446e4fba0beac51); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal5cc710949293a1af4bf787fa437b3c3d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5cc710949293a1af4bf787fa437b3c3d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.eligibility-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eligibility-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5cc710949293a1af4bf787fa437b3c3d)): ?>
<?php $attributes = $__attributesOriginal5cc710949293a1af4bf787fa437b3c3d; ?>
<?php unset($__attributesOriginal5cc710949293a1af4bf787fa437b3c3d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5cc710949293a1af4bf787fa437b3c3d)): ?>
<?php $component = $__componentOriginal5cc710949293a1af4bf787fa437b3c3d; ?>
<?php unset($__componentOriginal5cc710949293a1af4bf787fa437b3c3d); ?>
<?php endif; ?>

    <script src="<?php echo e(asset('assets/scripts/pages/index.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\layouts\public.blade.php ENDPATH**/ ?>