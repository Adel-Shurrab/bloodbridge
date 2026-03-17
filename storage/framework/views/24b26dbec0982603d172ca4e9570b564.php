<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocaleDirection()); ?>">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e11d48;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 0.9em;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #e11d48; margin: 0;"><?php echo e(__('Thank You for Contacting Us')); ?></h2>
        </div>

        <p><?php echo e(__('Hello')); ?> <strong><?php echo e($contactMessage->name); ?></strong>,</p>

        <p><?php echo e(__('We have received your message successfully. Our support team will review it and get back to you as soon as possible.')); ?></p>

        <div style="margin-top: 30px; background-color: #f9fafb; padding: 15px; border-radius: 6px;">
            <p style="margin-top: 0;"><strong><?php echo e(__('Your Message Details')); ?>:</strong></p>
            <p style="margin-bottom: 0;"><?php echo e(__('Subject')); ?>: <?php echo e($contactMessage->subject); ?></p>
        </div>

        <div class="footer">
            <p><?php echo e(__('This is an automated message, please do not reply directly to this email.')); ?></p>
            <p><?php echo e($siteName); ?></p>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\adels\Herd\bloodbridge\resources\views\emails\user-contact-confirmation.blade.php ENDPATH**/ ?>