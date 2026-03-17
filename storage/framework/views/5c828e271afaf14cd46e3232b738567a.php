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
    <?php ($data = $this->getEligibilityData()); ?>

    <div class="eligibility-timer-banner">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$data['eligible_now']): ?>
            <p class="eligibility-timer-label">
                <?php echo e(__('Next donation eligibility in:')); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="eligibility-timer-display">
            <i class="fa-regular fa-hourglass-half"></i>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['eligible_now']): ?>
                <h2><?php echo e($data['message']); ?></h2>
            <?php elseif($data['timestamp']): ?>
                
                <h2 x-data="{
                                target: <?php echo e($data['timestamp']); ?> * 1000,
                                now: Date.now(),
                                tick() { this.now = Date.now() },
                                get diff() { return Math.max(0, this.target - this.now) },
                                get d() { return Math.floor(this.diff / 86400000) },
                                get h() { return Math.floor((this.diff % 86400000) / 3600000) },
                                get m() { return Math.floor((this.diff % 3600000) / 60000) },
                                get s() { return Math.floor((this.diff % 60000) / 1000) }
                            }" x-init="setInterval(() => tick(), 1000)">
                    <span><span x-text="d"></span> <?php echo e(__('d')); ?></span>
                    <span><span x-text="h"></span> <?php echo e(__('h')); ?></span>
                    <span><span x-text="m"></span> <?php echo e(__('m')); ?></span>
                    <span><span x-text="s"></span> <?php echo e(__('s')); ?></span>
                </h2>
            <?php else: ?>
                <h2><?php echo e($data['message']); ?></h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <style>
        .eligibility-timer-banner {
            background-color: #ffffff;
            border: 1px solid rgba(220, 38, 38, 0.4);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #fffafa, #ffffff);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        html.dark .eligibility-timer-banner {
            background-color: #18181b;
            /* zinc-900 */
            border: 1px solid rgba(239, 68, 68, 0.3);
            /* red-500 with opacity */
            background: linear-gradient(to right, #18181b, #271c1c, #18181b);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.5);
        }

        .eligibility-timer-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            /* gray-500 */
            margin-bottom: 0.5rem;
        }

        html.dark .eligibility-timer-label {
            color: #9ca3af;
            /* gray-400 */
        }

        .eligibility-timer-display {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            color: #dc2626;
            /* red-600 */
        }

        html.dark .eligibility-timer-display {
            color: #ef4444;
            /* red-500 */
        }

        .eligibility-timer-display i {
            font-size: 1.5rem;
        }

        .eligibility-timer-display h2 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin: 0;
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
<?php endif; ?><?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\donor\widgets\eligibility-countdown.blade.php ENDPATH**/ ?>