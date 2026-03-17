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
    <div class="top-bar">
        <div class="top-bar-title">
            <h1><?php echo e(__('Dashboard')); ?></h1>
            <p class="top-bar-subtitle">
                <?php echo e(__('Welcome back, :name. Here is what is happening with', ['name' => $this->getOrganization()->responsible_person_name])); ?> <span
                    class="brand-text"><?php echo e($this->getOrganization()->org_name); ?></span>.
            </p>
        </div>
        <div class="top-bar-actions">

            
            <div class="profile-section">
                <div class="profile-info">
                    <span class="profile-name"><?php echo e($this->getOrganization()->responsible_person_name); ?></span>
                    <span class="profile-role">
                        <?php echo e($this->getOrganization()->org_name); ?>

                    </span>
                </div>
                <img src="<?php echo e(filament()->getUserAvatarUrl(auth()->user())); ?>" alt="Profile" class="profile-img">
            </div>
        </div>
    </div>

    <style>
        .top-bar {
            background: #ffffff;
            padding: 1.5rem 2rem;
            border-radius: 24px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(211, 47, 47, 0.05);

            transition: all 0.3s;
        }

        .dark .top-bar {
            background: #111827;
            border-color: #1f2937;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
        }

        .top-bar:hover {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
        }

        .top-bar-title {
            text-align: start;
        }

        .top-bar-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .dark .top-bar-title h1 {
            color: #f9fafb;
        }

        .top-bar-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        .brand-text {
            color: #d32f2f;
            font-weight: 700;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-radius: 16px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .dark .profile-section {
            background: linear-gradient(135deg, #1f1212, #450a0a);
        }

        .profile-section:hover {
            transform: translateX(-5px);
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.2;
        }

        .profile-name {
            font-weight: 700;
            color: #2d3748;
            font-size: 0.95rem;
        }

        .dark .profile-name {
            color: #f3f4f6;
        }

        .profile-role {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 600;
        }

        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #d32f2f;
            box-shadow: 0 4px 10px rgba(211, 47, 47, 0.2);
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem;
            }

            .top-bar-title {
                text-align: center;
            }

            .profile-info {
                align-items: center;
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
<?php endif; ?>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\organization\widgets\organization-header-widget.blade.php ENDPATH**/ ?>