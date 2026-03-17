<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve(['title' => ''.e(__('Terms of Service')).' - '.e($settings->getTranslation('site_name')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .terms-hero {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 60%, #1a202c 100%);
            padding: 6rem 2rem 5rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .terms-hero::before {
            content: '';
            position: absolute;
            top: -80px;
            left: -80px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(211, 47, 47, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .terms-hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .terms-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(211, 47, 47, 0.2);
            border: 1px solid rgba(211, 47, 47, 0.4);
            color: #fc8181;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
        }

        .terms-hero-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .terms-hero h1 {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
            line-height: 1.2;
        }

        .terms-hero p {
            font-size: 1.1rem;
            opacity: 0.8;
            line-height: 1.9;
            max-width: 650px;
            margin: 0 auto;
        }

        .terms-hero-meta {
            margin-top: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.85rem;
            opacity: 0.7;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Layout */
        .terms-layout {
            max-width: 1050px;
            margin: 0 auto;
            padding: 4rem 1.5rem;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 3rem;

        }

        /* Sidebar */
        .terms-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .terms-sidebar-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .terms-sidebar-card h3 {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a0aec0;
            margin-bottom: 1.25rem;
        }

        .terms-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            text-decoration: none;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .terms-nav-item:hover {
            background: #f7fafc;
            color: #1a202c;
        }

        .terms-nav-num {
            width: 22px;
            height: 22px;
            background: #edf2f7;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            flex-shrink: 0;
        }

        /* Content */
        .terms-content {
            min-width: 0;
        }

        .terms-section {
            margin-bottom: 3.5rem;
            scroll-margin-top: 100px;
        }

        .terms-section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .terms-section-num {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 900;
            flex-shrink: 0;
        }

        .terms-section-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1a202c;
            margin: 0;
        }

        .terms-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        /* Danger card for disclaimer */
        .terms-card-danger {
            background: linear-gradient(135deg, #fff5f5 0%, #fffafa 100%);
            border-color: #fed7d7;
            border-inline-start: 4px solid #e53e3e;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.9rem;
        }

        .terms-list-item {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            color: #4a5568;
            font-size: 0.97rem;
            line-height: 1.8;
        }

        .terms-list-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .terms-list-icon.green {
            background: #f0fff4;
            color: #38a169;
        }

        .terms-list-icon.red {
            background: #fff5f5;
            color: #e53e3e;
        }

        .terms-list-icon.blue {
            background: #ebf8ff;
            color: #3182ce;
        }

        .terms-list-icon.gray {
            background: #f7fafc;
            color: #718096;
        }

        .terms-inline-link {
            color: #d32f2f;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px dashed #d32f2f;
            transition: all 0.2s;
        }

        .terms-inline-link:hover {
            color: #9b2c2c;
            border-bottom-style: solid;
        }

        .terms-info-box {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            color: #2c5282;
            font-size: 0.9rem;
            line-height: 1.7;
            margin-top: 1rem;
        }

        .terms-cta {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            color: white;
            margin-top: 1rem;
            box-shadow: 0 20px 40px rgba(26, 32, 44, 0.2);
        }

        .terms-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #d32f2f;
            color: white;
            text-decoration: none;
            padding: 0.85rem 2.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 1.5rem;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.35);
        }

        .terms-cta-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
        }

        @media (max-width: 768px) {
            .terms-layout {
                grid-template-columns: 1fr;
            }

            .terms-sidebar {
                position: static;
                display: none;
            }

            .terms-hero h1 {
                font-size: 1.9rem;
            }

            .terms-hero {
                padding: 4rem 1.5rem 3.5rem;
            }

            .terms-card {
                padding: 1.25rem 1.25rem;
            }
        }
    </style>

    <!-- Hero -->
    <div class="terms-hero">
        <div style="position: relative; z-index: 1;">
            <div class="terms-hero-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1><?php echo e(__('Terms of Service and Use')); ?></h1>
            <p><?php echo e(__('Please read these terms carefully before using the :site_name platform. By using the platform, you agree to fully abide by these terms and conditions.', ['site_name' => $settings->getTranslation('site_name')])); ?>

            </p>
            <div class="terms-hero-meta">
                <i class="fas fa-calendar-alt"></i>
                <?php echo e(__('Last Updated')); ?>: <?php echo e(date('d/m/Y')); ?>

            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div style="background: #f7fafc; padding-bottom: 4rem;">
        <div class="terms-layout">

            <!-- Sidebar -->
            <aside class="terms-sidebar">
                <div class="terms-sidebar-card">
                    <h3><?php echo e(__('Table of Contents')); ?></h3>
                    <nav>
                        <a href="#disclaimer" class="terms-nav-item">
                            <span class="terms-nav-num"
                                style="background:#fff5f5; color:#e53e3e;"><?php echo e(__('1')); ?></span>
                            <?php echo e(__('Medical Disclaimer')); ?>

                        </a>
                        <a href="#service-nature" class="terms-nav-item">
                            <span class="terms-nav-num"><?php echo e(__('2')); ?></span>
                            <?php echo e(__('Nature of Service')); ?>

                        </a>
                        <a href="#donor-duties" class="terms-nav-item">
                            <span class="terms-nav-num"><?php echo e(__('3')); ?></span>
                            <?php echo e(__('Donor Obligations')); ?>

                        </a>
                        <a href="#org-duties" class="terms-nav-item">
                            <span class="terms-nav-num"><?php echo e(__('4')); ?></span>
                            <?php echo e(__('Health Institutions Obligations')); ?>

                        </a>
                        <a href="#privacy" class="terms-nav-item">
                            <span class="terms-nav-num"
                                style="background:#ebf8ff; color:#3182ce;"><?php echo e(__('5')); ?></span>
                            <?php echo e(__('Privacy and Data')); ?>

                        </a>
                        <a href="#account" class="terms-nav-item">
                            <span class="terms-nav-num"><?php echo e(__('6')); ?></span>
                            <?php echo e(__('Account Suspension or Termination')); ?>

                        </a>
                        <a href="#updates" class="terms-nav-item">
                            <span class="terms-nav-num"><?php echo e(__('7')); ?></span>
                            <?php echo e(__('Modifications to Terms')); ?>

                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Content -->
            <main class="terms-content">

                
                <section id="disclaimer" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fff5f5; color:#e53e3e;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 class="terms-section-title"><?php echo e(__('Medical Disclaimer')); ?></h2>
                    </div>
                    <div class="terms-card terms-card-danger">
                        <p
                            style="font-size:0.9rem; font-weight:700; color:#c53030; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo e(__('This clause is the most important — please read it carefully')); ?>

                        </p>
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span><?php echo e(__('The :site_name platform is solely a technical intermediary aiming to facilitate communication between donors and hospitals or blood banks. The platform does not provide any medical consultations in any form.', ['site_name' => $settings->getTranslation('site_name')])); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span><?php echo e(__('The receiving hospital or blood bank is the sole and direct responsible party for the medical examination of the donor, assessing their final eligibility, and conducting complete safety tests on the blood.')); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span><?php echo e(__('The platform and its team are not responsible for any health complications that occur to the donor during or after the process, or for any negative medical outcomes for the recipient patient.')); ?></span>
                            </li>
                        </ul>
                    </div>
                </section>

                
                <section id="service-nature" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#f0fff4; color:#38a169;">٢</div>
                        <h2 class="terms-section-title"><?php echo e(__('Nature of Service')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            <?php echo e(__('The :site_name platform provides a technical service to create a live and active database of blood donors, allowing authorized health institutions to send targeted donation requests during emergencies. The core services of the platform are completely free for individual donors.', ['site_name' => $settings->getTranslation('site_name')])); ?>

                        </p>
                        <div class="terms-info-box">
                            <i class="fas fa-info-circle" style="margin-top:3px; flex-shrink:0;"></i>
                            <span><?php echo e(__('The platform does not guarantee the availability of donors at any given time, nor does it bear responsibility for any delay in responding to donation requests in emergency situations.')); ?></span>
                        </div>
                    </div>
                </section>

                
                <section id="donor-duties" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#ebf8ff; color:#3182ce;">٣</div>
                        <h2 class="terms-section-title"><?php echo e(__('Donor Obligations')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong><?php echo e(__('Information Accuracy')); ?>:</strong>
                                    <?php echo e(__('You acknowledge that all health and personal data you have entered (such as blood type, diseases, and age) is accurate, up-to-date, and does not contain misleading information.')); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong><?php echo e(__('Consent for Notifications')); ?>:</strong>
                                    <?php echo e(__('By registering, you agree to receive notifications via email or text messages when an urgent donation request matches your blood type.')); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong><?php echo e(__('No Obligation')); ?>:</strong>
                                    <?php echo e(__('Receiving a donation request does not legally obligate you to donate; you have complete freedom to accept or decline based on your health and personal circumstances at that moment.')); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong><?php echo e(__('Total Medical Honesty')); ?>:</strong>
                                    <?php echo e(__('You are committed to fully disclosing any donation deferrals or medical history to the medical staff at the hospital :before the actual donation, regardless of what is registered in the system.', ['before' => 'before'])); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon blue"><i class="fas fa-info"></i></span>
                                <span><?php echo e(__('Eligible registration age: between :min_age and :max_age years old. Minimum weight: :min_weight kg.', ['min_age' => $settings->min_donor_age, 'max_age' => $settings->max_donor_age, 'min_weight' => $settings->min_donor_weight])); ?></span>
                            </li>
                        </ul>
                    </div>
                </section>

                
                <section id="org-duties" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fefcbf; color:#d69e2e;">٤</div>
                        <h2 class="terms-section-title"><?php echo e(__('Health Institutions Obligations')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span><?php echo e(__('The use of the system is restricted to searching for donors for genuine and emergent medical cases, and exploiting donor data for any commercial or advertising purpose is strictly prohibited.')); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span><?php echo e(__('The system limits the number of daily requests (a maximum of :max requests per day). Random broadcasting is prohibited to avoid disturbing donors.', ['max' => $settings->org_max_requests_per_day])); ?></span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span><?php echo e(__('Each institution bears the full and individual responsibility for applying safety standards and conducting laboratory and viral testing on any blood collected via the platform.')); ?></span>
                            </li>
                        </ul>
                    </div>
                </section>

                
                <section id="privacy" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#ebf8ff; color:#3182ce;">٥</div>
                        <h2 class="terms-section-title"><?php echo e(__('Privacy and Data')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            <?php echo e(__('We share the minimum necessary contact data (such as phone number and blood type) with the hospital only when a donation request matches, and with your explicit consent upon registration. For more details, consult the')); ?>

                            <a href="javascript:void(0)" @click.prevent="$dispatch('open-modal', 'privacyModal')"
                                class="terms-inline-link"><?php echo e(__('Full Privacy Policy')); ?></a>.
                        </p>
                    </div>
                </section>

                
                <section id="account" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fff5f5; color:#e53e3e;">٦</div>
                        <h2 class="terms-section-title"><?php echo e(__('Account Suspension or Termination')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            <?php echo e(__('We reserve the right to suspend or delete any account proven to misuse the platform, including: entering false data, repeatedly failing to donate after confirmation, or any violation of privacy conditions. Account owners will be notified prior to any action where possible.')); ?>

                        </p>
                    </div>
                </section>

                
                <section id="updates" class="terms-section" style="margin-bottom: 0;">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#f7fafc; color:#718096;">٧</div>
                        <h2 class="terms-section-title"><?php echo e(__('Modifications to Terms')); ?></h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            <?php echo e(__('The platform reserves the right to update these terms at any time. Users will be notified of any substantial changes via the platform or email. Your continued use of the site following any modification constitutes explicit acceptance of the changes.')); ?>

                        </p>
                    </div>
                </section>

                <!-- CTA Footer -->
                <div class="terms-cta" style="margin-top: 3.5rem;">
                    <div style="font-size:2rem; margin-bottom:1rem;">💬</div>
                    <h3 style="font-size:1.35rem; font-weight:800; margin-bottom:0.5rem;">
                        <?php echo e(__('Do you have a legal inquiry?')); ?></h3>
                    <p style="opacity:0.75; font-size:0.95rem;">
                        <?php echo e(__('The :site_name team is ready to answer any questions regarding these terms.', ['site_name' => $settings->getTranslation('site_name')])); ?>

                    </p>
                    <a href="<?php echo e(route('contact')); ?>" class="terms-cta-btn">
                        <i class="fas fa-envelope"></i>
                        <?php echo e(__('Contact Administration')); ?>

                    </a>
                </div>

            </main>
        </div>
    </div>

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
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\pages\terms.blade.php ENDPATH**/ ?>