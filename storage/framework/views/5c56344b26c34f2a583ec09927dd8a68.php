<?php
    $settings = app(\App\Settings\GeneralSettings::class);
    $logo = $settings->site_logo
        ? \Illuminate\Support\Facades\Storage::url($settings->site_logo)
        : asset('assets/images/logo.png');
?>

<div class="flex items-center gap-3 whitespace-nowrap" style="display: flex; align-items: center; flex-wrap: nowrap;">
    <img src="<?php echo e($logo); ?>" alt="<?php echo e($settings->getTranslation('site_name')); ?>"
        style="height: <?php echo e($height ?? '3.5rem'); ?>; width: auto; object-fit: contain;" class="fi-logo">
    <span class="text-xl font-bold text-gray-900 dark:text-white"
        style="line-height: <?php echo e($height ?? '3.5rem'); ?>;"><?php echo e($settings->getTranslation('site_name')); ?></span>
</div>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\logo.blade.php ENDPATH**/ ?>