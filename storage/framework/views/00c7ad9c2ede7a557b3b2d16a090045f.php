<?php if (isset($component)) { $__componentOriginalb525200bfa976483b4eaa0b7685c6e24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="custom-dashboard-header">
        <div class="header-content">
            
            <div class="header-text">
                <h1 class="dashboard-title"><?php echo e(__('filament.pages.dashboard.title')); ?></h1>
                <p class="welcome-text">
                    <?php echo e(__('Welcome,')); ?> <span class="brand-text"><?php echo e(auth()->user()->name); ?></span>. <?php echo e(__('Thank you for your contribution to saving lives.')); ?>

                </p>
            </div>

            
            <div class="header-profile">
                <div class="profile-box">
                    <div class="profile-text">
                        <span class="profile-name"><?php echo e(auth()->user()->name); ?></span>
                        <span class="profile-role"><?php echo e(__('Donor')); ?></span>
                    </div>
                    <div class="profile-avatar">
                        <img src="<?php echo e(filament()->getUserAvatarUrl(auth()->user())); ?>" alt="<?php echo e(__('Profile')); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-dashboard-header {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            width: 100%;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .dark .custom-dashboard-header {
            background: #111827;
            border: 1px solid #1f2937;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .header-text {
            text-align: start;
            flex: 1;
        }

        .dashboard-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
            line-height: 1.2;
        }

        .dark .dashboard-title {
            color: #f9fafb;
        }

        .welcome-text {
            color: #9ca3af;
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        .dark .welcome-text {
            color: #6b7280;
        }

        .brand-text {
            color: #ef4444;
            font-weight: 700;
        }

        .header-profile {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .profile-box {
            background: #fff5f5;
            padding: 0.5rem 1rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid #fee2e2;
        }

        .dark .profile-box {
            background: #1f1212;
            border-color: #450a0a;
        }

        .profile-text {
            display: flex;
            flex-direction: column;
            text-align: start;
            line-height: 1.2;
        }

        .profile-name {
            font-weight: 700;
            color: #1f2937;
            font-size: 0.95rem;
            display: block;
        }

        .dark .profile-name {
            color: #f3f4f6;
        }

        .profile-role {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            display: block;
        }

        .dark .profile-role {
            color: #9ca3af;
        }

        .profile-avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .dark .profile-avatar {
            border-color: #1f2937;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 1024px) {
            .dashboard-title {
                font-size: 1.5rem;
            }
            .welcome-text {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 1.25rem;
                padding: 0.5rem 0;
            }

            .header-text {
                text-align: center;
            }

            .header-profile {
                justify-content: center;
            }
        }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $attributes = $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $component = $__componentOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?><?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\donor\widgets\donor-header-widget.blade.php ENDPATH**/ ?>