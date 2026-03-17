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
    <div class="blood-demand-card">
        <h2 class="card-title"><?php echo e(__('Most Needed Blood Type')); ?></h2>

        <?php $data = $this->getDemandData(); ?>

        <div class="most-needed-section">
            <span class="blood-type-big"><?php echo e($data['most_needed']); ?></span>
            <span class="demand-label"><?php echo e(__('Most Requested')); ?></span>
        </div>

        <div class="demand-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data['breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="demand-item">
                    <span class="item-value"><?php echo e($item['value']); ?></span>
                    <span class="item-label"><?php echo e($item['label']); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <style>
        .blood-demand-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            height: 100%;
            text-align: center;

        }

        .dark .blood-demand-card {
            background: #111827;
            border: 1px solid #1f2937;
        }

        .most-needed-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 3rem 0;
        }

        .blood-type-big {
            font-size: 6rem;
            font-weight: 900;
            color: #ef4444;
            line-height: 1;
        }

        .demand-label {
            font-size: 1.25rem;
            color: #9ca3af;
            font-weight: 700;
            margin-top: 1rem;
        }

        .demand-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-top: auto;
        }

        .demand-item {
            background: #f9fafb;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .demand-item {
            background: rgba(255, 255, 255, 0.02);
        }

        .item-label {
            font-weight: 800;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .dark .item-label {
            color: #f3f4f6;
        }

        .item-value {
            color: #ef4444;
            font-weight: 800;
            font-size: 1.1rem;
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\widgets\blood-type-demand-widget.blade.php ENDPATH**/ ?>