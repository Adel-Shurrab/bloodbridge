<?php
    $settings = app(\App\Settings\GeneralSettings::class);
    $siteName = $settings->getTranslation('site_name') ?? config('app.name');
    $logo = $settings->site_logo
        ? \Illuminate\Support\Facades\Storage::url($settings->site_logo)
        : asset('assets/images/logo.png');
?>

<footer class="bb-footer">
    <div class="bb-footer-card">

        
        <div class="bb-footer-top">
            <div class="bb-footer-brand">
                <img src="<?php echo e($logo); ?>" alt="<?php echo e($siteName); ?>" class="bb-footer-logo">
                <span class="bb-footer-name"><?php echo e($siteName); ?></span>
            </div>

            <p class="bb-footer-tagline">
                <svg class="bb-footer-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                </svg>
                <?php echo e(__('Saving lives together')); ?>

            </p>
        </div>

        
        <div class="bb-footer-divider"></div>

        
        <p class="bb-footer-copy">
            &copy; <?php echo e(date('Y')); ?> <strong><?php echo e($siteName); ?></strong>. <?php echo e(__('All rights reserved.')); ?>

        </p>

    </div>
</footer>

<style>
    .bb-footer {
        width: 100%;
        padding: 1rem 1.5rem 1.75rem;

    }

    .bb-footer-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        border-radius: 16px;
        padding: 1.25rem 1.75rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s;
    }

    .dark .bb-footer-card {
        background: #111827;
        border-color: #1f2937;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    }

    /* Top Row */
    .bb-footer-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* Brand */
    .bb-footer-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .bb-footer-logo {
        height: 2rem;
        width: auto;
        object-fit: contain;
    }

    .dark .bb-footer-logo {
        filter: brightness(0) invert(1);
        opacity: 0.85;
    }

    .bb-footer-name {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }

    .dark .bb-footer-name {
        color: #f3f4f6;
    }

    /* Tagline */
    .bb-footer-tagline {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #6b7280;
        margin: 0;
    }

    .dark .bb-footer-tagline {
        color: #9ca3af;
    }

    .bb-footer-heart {
        width: 1rem;
        height: 1rem;
        color: #ef4444;
        flex-shrink: 0;
        animation: bb-heartbeat 1.8s ease-in-out infinite;
    }

    @keyframes bb-heartbeat {

        0%,
        100% {
            transform: scale(1);
        }

        14% {
            transform: scale(1.3);
        }

        28% {
            transform: scale(1);
        }

        42% {
            transform: scale(1.2);
        }

        56% {
            transform: scale(1);
        }
    }

    /* Divider */
    .bb-footer-divider {
        height: 1px;
        background: linear-gradient(to left, transparent, #e5e7eb, transparent);
        margin: 1rem 0;
    }

    .dark .bb-footer-divider {
        background: linear-gradient(to left, transparent, #374151, transparent);
    }

    /* Copyright */
    .bb-footer-copy {
        text-align: center;
        font-size: 0.78rem;
        color: #9ca3af;
        margin: 0;
    }

    .dark .bb-footer-copy {
        color: #6b7280;
    }

    .bb-footer-copy strong {
        color: #6b7280;
        font-weight: 600;
    }

    .dark .bb-footer-copy strong {
        color: #9ca3af;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .bb-footer-top {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }
</style>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\footer.blade.php ENDPATH**/ ?>