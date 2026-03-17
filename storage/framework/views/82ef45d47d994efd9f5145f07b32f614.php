
<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(1, minmax(0, 1fr));" class="lg:grid-cols-3">
        
        <div style="grid-column: span 2 / span 2;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($foundResponse && $foundResponse->donor): ?>
                
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                     <?php $__env->slot('heading', null, []); ?> 
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary-600, #2563EB);" class="dark:text-primary-400">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-identification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width: 1.5rem; height: 1.5rem;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                            <?php echo e(__('Donor Information')); ?>

                        </div>
                     <?php $__env->endSlot(); ?>

                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1.5rem; padding: 1rem;">
                        
                        <div style="height: 8rem; width: 8rem; overflow: hidden; border-radius: 9999px; border-width: 4px; border-color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); background-color: #f3f4f6;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($foundResponse->donor->user->name)); ?>&color=7F9CF5&background=random"
                                alt="<?php echo e($foundResponse->donor->user->name); ?>" style="height: 100%; width: 100%; object-fit: cover;">
                        </div>

                        
                        <div style="text-align: center;">
                            <h2 style="font-size: 1.875rem; font-weight: 700;">
                                <?php echo e($foundResponse->donor->user->name); ?>

                            </h2>
                            <div style="margin-top: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <span style="border-radius: 9999px; background-color: #FEE2E2; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #B91C1C;" class="dark:bg-red-900/30 dark:text-red-400">
                                    <?php echo e($foundResponse->donor->blood_type?->label() ?? __('Not Specified')); ?>

                                </span>
                                <span style="color: #6B7280;">|</span>
                                <span style="font-size: 0.875rem; color: #4B5563;" class="dark:text-gray-300">
                                    <?php echo e($foundResponse->donor->user->email); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div style="display: grid; width: 100%; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; padding-top: 1rem;">
                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['size' => 'xl','color' => 'success','wire:click' => 'confirmAdmission','style' => 'height: 3.5rem; font-size: 1.125rem;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xl','color' => 'success','wire:click' => 'confirmAdmission','style' => 'height: 3.5rem; font-size: 1.125rem;']); ?>
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-check-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'margin-inline-start: 0.5rem; height: 1.5rem; width: 1.5rem; display: inline-block;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                <?php echo e(__('Confirm Admission')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>

                            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['size' => 'xl','color' => 'gray','wire:click' => 'cancelAdmission','style' => 'height: 3.5rem; font-size: 1.125rem;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xl','color' => 'gray','wire:click' => 'cancelAdmission','style' => 'height: 3.5rem; font-size: 1.125rem;']); ?>
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-m-x-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'margin-inline-start: 0.5rem; height: 1.5rem; width: 1.5rem; display: inline-block;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                <?php echo e(__('Cancel')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
                        </div>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="<?php echo e($foundResponse ? 'hidden' : ''); ?>">
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                     <?php $__env->slot('heading', null, []); ?> 
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-camera'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width: 1.25rem; height: 1.25rem; color: #9CA3AF;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                            <?php echo e(__('QR Scanner')); ?>

                        </div>
                     <?php $__env->endSlot(); ?>

                    
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                        
                        <div id="scan-status-badge" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 9999px; padding: 0.5rem 1.5rem; font-size: 1rem; font-weight: 600; transition: all 300ms; background-color: #FEF3C7; color: #D97706;">
                            <span id="scan-status-text"><?php echo e(__('Loading Camera')); ?>...</span>
                        </div>

                        <div class="qr-scanner-container" style="width: 100%; display: flex; justify-content: center;">
                            <div id="reader" style="border-radius: 0.75rem; overflow: hidden; background-color: black; width: 100%; max-width: 500px;" wire:ignore></div>
                        </div>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            </div>

            
            <div style="margin-top: 1.5rem;">
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
                        <div style="border-radius: 0.5rem; padding: 1rem; text-align: center;" class="bg-gray-50 dark:bg-gray-800/50">
                            <div style="font-size: 0.75rem; font-weight: 500; color: #6B7280;" class="dark:text-gray-400"><?php echo e(__('Total Scans Today')); ?></div>
                            <div id="stat-total" style="margin-top: 0.25rem; font-size: 1.5rem; font-weight: 700;">0</div>
                        </div>
                        <div style="border-radius: 0.5rem; padding: 1rem; text-align: center;" class="bg-green-50 dark:bg-green-900/20">
                            <div style="font-size: 0.75rem; font-weight: 500; color: #16A34A;" class="dark:text-green-400"><?php echo e(__('Successful Verifications')); ?></div>
                            <div id="stat-success" style="margin-top: 0.25rem; font-size: 1.5rem; font-weight: 700; color: #15803D;" class="dark:text-green-400">0</div>
                        </div>
                        <div style="border-radius: 0.5rem; padding: 1rem; text-align: center;" class="bg-red-50 dark:bg-red-900/20">
                            <div style="font-size: 0.75rem; font-weight: 500; color: #DC2626;" class="dark:text-red-400"><?php echo e(__('Failed Verifications')); ?></div>
                            <div id="stat-failure" style="margin-top: 0.25rem; font-size: 1.5rem; font-weight: 700; color: #B91C1C;" class="dark:text-red-400">0</div>
                        </div>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            </div>
        </div>

        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                 <?php $__env->slot('heading', null, []); ?> 
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-light-bulb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width: 1.25rem; height: 1.25rem; color: var(--primary-600, #2563EB);','class' => 'dark:text-primary-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <span><?php echo e(__('Instructions for Use')); ?></span>
                    </div>
                 <?php $__env->endSlot(); ?>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        '1' => __('Allow the browser to access the camera when prompted'),
                        '2' => __('Point the camera at the QR code on the donor card'),
                        '3' => __('Wait for automatic verification - no need to click any button'),
                        '4' => __('Ensure there is sufficient lighting for best results')
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display: flex; gap: 0.75rem;">
                        <div style="display: flex; height: 2rem; width: 2rem; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 9999px; background-color: var(--primary-600, #2563EB); font-size: 0.875rem; font-weight: 700; color: white;" class="dark:bg-primary-500">
                            <?php echo e($step); ?>

                        </div>
                        <p style="font-size: 0.875rem; font-weight: 600; line-height: 1.625; margin: 0;">
                            <?php echo e($text); ?>

                        </p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let scanCount = 0;
                let successCount = 0;
                let failureCount = 0;
                let isProcessing = false;

                // Update status badge
                function updateStatus(message, type = 'loading') {
                    const badge = document.getElementById('scan-status-badge');
                    const text = document.getElementById('scan-status-text');
                    text.textContent = message;
                    
                    // Robust styling with explicit colors
                    badge.style.color = '#ffffff'; // Always white text for better contrast
                    
                    if(type === 'success') {
                        badge.style.backgroundColor = '#10B981'; // Green 500
                    } else if(type === 'error') {
                        badge.style.backgroundColor = '#EF4444'; // Red 500
                    } else if(type === 'ready') {
                        badge.style.backgroundColor = '#3B82F6'; // Blue 500
                    } else {
                        // loading
                        badge.style.backgroundColor = '#F59E0B'; // Amber 500
                    }
                }

                // Update statistics
                function updateStats() {
                    document.getElementById('stat-total').textContent = scanCount;
                    document.getElementById('stat-success').textContent = successCount;
                    document.getElementById('stat-failure').textContent = failureCount;
                }

                let html5QrCode = null;

                async function startScanning() {
                    try {
                        const devices = await Html5Qrcode.getCameras();
                        if (!devices || devices.length === 0) {
                            updateStatus('❌ <?php echo e(__('No camera found')); ?>', 'error');
                            return;
                        }

                        const cameraId = selectBestCamera(devices);
                        html5QrCode = new Html5Qrcode("reader");

                        await html5QrCode.start(
                            cameraId, {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        },
                            onScanSuccess,
                            onScanFailure
                        );

                        updateStatus('📷 <?php echo e(__('Camera ready - start scanning')); ?>', 'ready');

                    } catch (err) {
                        console.error('Error starting scanner:', err);
                        updateStatus('❌ <?php echo e(__('Failed to start camera:')); ?> ' + err, 'error');
                    }
                }

                function selectBestCamera(devices) {
                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('environment')
                    );
                    return backCamera ? backCamera.id : devices[0].id;
                }

                function onScanSuccess(decodedText, decodedResult) {
                    if (isProcessing) return;
                    isProcessing = true;
                    scanCount++;
                    updateStatus('🔄 <?php echo e(__('Verifying code...')); ?>', 'loading');
                    updateStats();

                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').verifyQRCode(decodedText).then((result) => {
                        if (result === true) {
                            successCount++;
                            updateStatus('✅ <?php echo e(__('Verified successfully!')); ?>', 'success');
                            updateStats();
                            try { new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGa78OScTgwOUKXi8LZjHAU7k9r0y3krBSl+zPLaizsKElyx6OyrWBELTKXh8bllHgU1ivDu0oU2Bx5ruvDmnU0ME1Cl4/C2YhwGPJHX8sx5KwUmecnw3I8+ChVbsOnnqVUSC0yl4fC5ZB4FNYzx79KFNgcdarvv5Z5ODhNPpePwtmIcBT2Q2PTLeSkEKn3N8di8OwoVWbDp56lUEgxLpeDwuWQeBDWM8u/ShTYHHWq77+WeTg4TT6Xj8LZiHAU9kNj0y3kpBCp9zfHYvDsKFVmw6eepVBIMS6Xg8LlkHgQ1jPLv0oU2Bx1qu+/lnk4OE0+l4/C2YhwFPZDY9Mt5KQQqfc3x2Lw7ChVZsOnnqVQSDEul4PC5ZB4ENYzy79KFNgcdarvv5Z5ODhNPpePwtmIcBT2Q2PTLeSkEKn3N8di8OwoVWbDp56lUEgxLpeDwuWQeBDWM8u/Sh').play(); } catch(e){}
                            setTimeout(() => { updateStatus('📷 <?php echo e(__('Ready for next scan')); ?>', 'ready'); isProcessing = false; }, 2000);
                        } else {
                            failureCount++;
                            updateStatus('❌ <?php echo e(__('Invalid or already used code')); ?>', 'error');
                            updateStats();
                            setTimeout(() => { updateStatus('📷 <?php echo e(__('Ready for next scan')); ?>', 'ready'); isProcessing = false; }, 4000);
                        }
                    }).catch((error) => {
                        console.error(error);
                        isProcessing = false;
                        updateStatus('❌ <?php echo e(__('Connection error')); ?>', 'error');
                    });
                }

                function onScanFailure(error) {}

                startScanning();

                window.addEventListener('beforeunload', () => {
                    if (html5QrCode) html5QrCode.stop().catch(err => console.log('Stop failed', err));
                });
            });
        </script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
             /* Force Grid Layout for Large Screens */
            @media (min-width: 1024px) {
                .lg\:grid-cols-3 {
                    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                }
            }

            /* Container Constraints */
            #reader {
                width: 100% !important;
                max-width: 500px !important;
                margin: 0 auto !important;
                border-radius: 0.5rem;
                overflow: hidden;
            }
            
            #reader video {
                width: 100% !important;
                height: auto !important;
                object-fit: cover !important;
                border-radius: 0.5rem;
            }

            /* Fix Unstyled SVGs */
            #reader svg:not([class]) {
                display: none !important; /* Hide html5-qrcode's ugly icons */
            }
        </style>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\filament\organization\pages\scan-donor-qr.blade.php ENDPATH**/ ?>