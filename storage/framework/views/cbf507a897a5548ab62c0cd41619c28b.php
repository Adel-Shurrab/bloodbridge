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
    <div class="recent-activity-card">
        <div class="card-header">
            <h2 class="card-title"><?php echo e(__('Recent Activities')); ?></h2>
        </div>

        <div class="timeline-container">
            <div class="timeline-line"></div>

            <div class="activities-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getActivities(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="activity-item">
                        <div class="activity-content">
                            <h3 class="activity-text"><?php echo e($activity['title']); ?></h3>
                            <span class="activity-time"><?php echo e($activity['time']); ?></span>
                        </div>
                        <div class="activity-icon-box <?php echo e($activity['color']); ?>">
                            <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $activity['icon'],'class' => 'activity-icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity['icon']),'class' => 'activity-icon']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

    </div>

    <style>
        .recent-activity-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            
        }

        .dark .recent-activity-card {
            background: #111827;
            border: 1px solid #1f2937;
        }

        .card-header {
            margin-bottom: 2rem;
            text-align: start;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
        }

        .dark .card-title {
            color: #f3f4f6;
        }

        .timeline-container {
            position: relative;
            flex-grow: 1;
            padding-inline-start: 1.5rem;
        }

        .timeline-line {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #ef4444, #fca5a5, transparent);
            border-radius: 3px;
        }

        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .activity-item {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1.5rem;
            position: relative;
        }

        .activity-content {
            text-align: start;
            flex-grow: 1;
        }

        .activity-text {
            font-size: 1rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .dark .activity-text {
            color: #d1d5db;
        }

        .activity-time {
            font-size: 0.85rem;
            color: #9ca3af;
            font-weight: 600;
        }

        .activity-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            z-index: 1;
        }

        .activity-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Colors */
        .activity-icon-box.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .activity-icon-box.emerald {
            background: #ecfdf5;
            color: #10b981;
        }

        .activity-icon-box.orange {
            background: #fff7ed;
            color: #f97316;
        }

        .activity-icon-box.red {
            background: #fef2f2;
            color: #ef4444;
        }

        .activity-icon-box.pink {
            background: #fdf2f8;
            color: #ec4899;
        }

        .dark .activity-icon-box {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .card-footer {
            margin-top: 2.5rem;
        }

        .view-all-btn {
            width: 100%;
            padding: 1rem;
            background: transparent;
            border: 2px solid #ef4444;
            color: #ef4444;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
        }

        .view-all-btn:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
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
<?php endif; ?><?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\widgets\recent-activity-widget.blade.php ENDPATH**/ ?>