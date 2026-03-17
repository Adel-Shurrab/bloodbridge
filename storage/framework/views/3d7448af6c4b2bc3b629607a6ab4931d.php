<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <h4><?php echo e(__('About :site', ['site' => $settings->getTranslation('site_name')])); ?></h4>
                <p><?php echo e($settings->getTranslation('seo_description')); ?></p>
                <div class="social-links">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->facebook_url): ?>
                        <a href="<?php echo e($settings->facebook_url); ?>" aria-label="<?php echo e(__('Facebook')); ?>"
                            title="<?php echo e(__('Follow us on Facebook')); ?>"><i class="fab fa-facebook"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->twitter_url): ?>
                        <a href="<?php echo e($settings->twitter_url); ?>" aria-label="<?php echo e(__('Twitter')); ?>"
                            title="<?php echo e(__('Follow us on Twitter')); ?>"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->instagram_url): ?>
                        <a href="<?php echo e($settings->instagram_url); ?>" aria-label="<?php echo e(__('Instagram')); ?>"
                            title="<?php echo e(__('Follow us on Instagram')); ?>"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings->linkedin_url): ?>
                        <a href="<?php echo e($settings->linkedin_url); ?>" aria-label="<?php echo e(__('LinkedIn')); ?>"
                            title="<?php echo e(__('Follow us on LinkedIn')); ?>"><i class="fab fa-linkedin"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="footer-section">
                <h4><?php echo e(__('Quick Links')); ?></h4>
                <ul>
                    <li><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
                    <li><a href="<?php echo e(route('about')); ?>"><?php echo e(__('About Us')); ?></a></li>
                    <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(__('Contact Us')); ?></a></li>
                    <li><a href="javascript:void(0)"
                            @click.prevent="$dispatch('open-modal', 'privacyModal')"><?php echo e(__('Privacy Policy')); ?></a>
                    </li>
                    <li><a href="<?php echo e(route('terms')); ?>"><?php echo e(__('Terms of Service')); ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4><?php echo e(__('For Donors')); ?></h4>
                <ul>
                    <li><a href="<?php echo e(route('register.donor')); ?>"><?php echo e(__('Register as a Donor')); ?></a></li>
                    <li><a href="javascript:void(0)"
                            @click.prevent="$dispatch('open-modal', 'eligibilityModal')"><?php echo e(__('Eligibility Requirements')); ?></a>
                    </li>
                    <li><a href="<?php echo e(route('contact')); ?>#faq"><?php echo e(__('Frequently Asked Questions')); ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4><?php echo e(__('For Organizations')); ?></h4>
                <ul>
                    <li><a href="<?php echo e(route('register.organization')); ?>"><?php echo e(__('Register as an Organization')); ?></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($settings->getTranslation('site_name')); ?>.
                <?php echo e(__('All rights reserved.')); ?></p>
            <p><?php echo e($settings->getTranslation('site_slogan')); ?> 🩸</p>
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\components\footer.blade.php ENDPATH**/ ?>