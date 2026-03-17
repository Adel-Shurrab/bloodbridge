<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Contact Us')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/styles/pages/contact.css')); ?>" />
        <script>
            window.translations = {
                'Name min 3 chars': '<?php echo e(__('Name must be at least 3 characters')); ?>',
                'Invalid email': '<?php echo e(__('Please enter a valid email address')); ?>',
                'Subject min 5 chars': '<?php echo e(__('Subject must be at least 5 characters')); ?>',
                'Message min 10 chars': '<?php echo e(__('Message must be at least 10 characters')); ?>',
                'Privacy agreement required': '<?php echo e(__('You must agree to the privacy policy')); ?>',
                'Success message': '<?php echo e(__('Your message has been sent successfully! We will get back to you soon.')); ?>',
                'Error sending message': '<?php echo e(__('An error occurred while sending your message. Please try again later.')); ?>'
            };

            function __(key, replace = {}) {
                let translation = window.translations[key] || key;
                for (let placeholder in replace) {
                    translation = translation.replace(':' + placeholder, replace[placeholder]);
                }
                return translation;
            }
        </script>
    <?php $__env->stopPush(); ?>

    <main class="contact-page">
        <section class="contact-header">
            <div class="section-header" style="text-align: center">
                <h2><?php echo e($settings->getTranslation('contact_hero_title')); ?></h2>
                <p><?php echo e($settings->getTranslation('contact_hero_subtitle')); ?></p>
            </div>
        </section>

        <section class="contact-content">
            <div class="contact-grid">
                <div class="form-card">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->enable_contact_messages): ?>
                        <h3><?php echo e(__('Send us a Message')); ?></h3>
                        <form id="contactForm" method="POST" action="<?php echo e(route('contact.submit')); ?>"
                            class="contact-form">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="name"><?php echo e(__('Your Name')); ?> <span class="required">*</span></label>
                                <input type="text" id="name" name="name"
                                    placeholder="<?php echo e(__('Enter your name')); ?>" required aria-required="true"
                                    aria-describedby="nameError" />
                                <span class="error-message" id="nameError"></span>
                            </div>
                            <div class="form-group">
                                <label for="email"><?php echo e(__('Your Email')); ?> <span class="required">*</span></label>
                                <input type="email" id="email" name="email" placeholder="name@example.com"
                                    required aria-required="true" aria-describedby="emailError" />
                                <span class="error-message" id="emailError"></span>
                            </div>
                            <div class="form-group">
                                <label for="subject"><?php echo e(__('Subject')); ?> <span class="required">*</span></label>
                                <input type="text" id="subject" name="subject"
                                    placeholder="<?php echo e(__('How can we help you?')); ?>" required aria-required="true"
                                    aria-describedby="subjectError" />
                                <span class="error-message" id="subjectError"></span>
                            </div>
                            <div class="form-group">
                                <label for="message"><?php echo e(__('Message')); ?> <span class="required">*</span></label>
                                <textarea id="message" name="message" rows="5" placeholder="<?php echo e(__('Write your message here...')); ?>" required
                                    aria-required="true" aria-describedby="messageError"></textarea>
                                <span class="error-message" id="messageError"></span>
                            </div>
                            <div class="form-group checkbox">
                                <input type="checkbox" id="privacy" name="privacy" required aria-required="true" />
                                <label for="privacy"><?php echo e(__('I agree to the')); ?> <a href="javascript:void(0)"
                                        @click.prevent="$dispatch('open-modal', 'privacyModal')"><?php echo e(__('Privacy Policy')); ?></a>
                                    <span class="required">*</span></label>
                            </div>
                            <button type="submit" class="btn btn-primary full-width" id="submitBtn">
                                <span class="btn-text"><?php echo e(__('Send Message')); ?></span>
                                <span class="btn-loader" style="display: none;"><?php echo e(__('Sending...')); ?></span>
                            </button>
                            <div class="form-message success-message" id="successMessage" style="display: none;"></div>
                            <div class="form-message error-message" id="errorMessage" style="display: none;"></div>
                        </form>
                    <?php else: ?>
                        <div class="text-center" style="padding: 40px 20px;">
                            <i class="fa-solid fa-envelope-circle-xmark"
                                style="font-size: 4rem; color: #9ca3af; margin-bottom: 20px;"></i>
                            <h3><?php echo e(__('Sorry, receiving messages is currently disabled')); ?></h3>
                            <p style="color: #6b7280; margin-top: 10px;">
                                <?php echo e(__('You can contact us via the other contact channels shown below.')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="info-card">
                    <div class="info-section">
                        <h3><?php echo e(__('Contact Information')); ?></h3>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <h4><?php echo e(__('Phone')); ?></h4>
                                <a
                                    href="tel:<?php echo e(str_replace(['-', ' ', '(', ')'], '', $settings->support_phone)); ?>"><?php echo e($settings->support_phone); ?></a>
                                <p><?php echo e($settings->getTranslation('working_days')); ?>، <?php echo e($settings->getTranslation('working_hours')); ?></p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="info-text">
                                <h4><?php echo e(__('Email')); ?></h4>
                                <a href="mailto:<?php echo e($settings->support_email); ?>"><?php echo e($settings->support_email); ?></a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="info-text">
                                <h4><?php echo e(__('Address')); ?></h4>
                                <p><?php echo e($settings->getTranslation('address')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-section" id="faq" style="scroll-margin-top: 100px;">
                        <h3><?php echo e(__('Frequently Asked Questions')); ?></h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $settings->getTranslation('contact_faqs') ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="faq-item">
                                <button class="faq-question">
                                    <span><?php echo e($faq['question'] ?? ''); ?></span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="faq-answer">
                                    <p><?php echo e($faq['answer'] ?? ''); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/scripts/pages/contact.js')); ?>"></script>
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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\pages\contact.blade.php ENDPATH**/ ?>