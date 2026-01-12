<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموقع تحت الصيانة - {{ $settings->site_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Cairo', sans-serif;
            color: #1a202c;
            overflow: hidden;
        }

        .maintenance-container {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 32px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            width: 90%;
            position: relative;
            z-index: 1;
        }

        .icon-box {
            width: 100px;
            height: 100px;
            background: #fef2f2;
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 2rem;
            color: #dc143c;
            font-size: 3rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #2d3748;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #718096;
            margin-bottom: 2rem;
        }

        .brand-name {
            font-weight: 700;
            color: #dc143c;
        }

        .background-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            background: #fee2e2;
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.6;
            animation: move 20s infinite alternate;
        }

        .blob-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            bottom: -150px;
            left: -150px;
            background: #fff1f2;
        }

        @keyframes move {
            from {
                transform: translate(0, 0);
            }

            to {
                transform: translate(40px, 40px);
            }
        }

        .logo-img {
            max-width: 150px;
            margin-bottom: 2rem;
        }

        .auth-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-family: 'Cairo', sans-serif;
            font-size: 0.95rem;
        }

        .btn-logout {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-logout:hover {
            background: #e2e8f0;
        }

        .btn-admin {
            background: #dc143c;
            color: white;
        }

        .btn-admin:hover {
            background: #b91133;
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.2);
        }
    </style>
</head>

<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="maintenance-container">
        @if($settings->site_logo)
            <img src="{{ Storage::url($settings->site_logo) }}" alt="{{ $settings->site_name }}" class="logo-img">
        @else
            <div class="icon-box">🛠️</div>
        @endif

        <h1>الموقع تحت الصيانة</h1>
        <p>
            {{ $settings->maintenance_message ?? 'نحن نقوم ببعض التحسينات حالياً. سنعود للعمل قريباً!' }}
        </p>

        <div style="font-weight: 600; color: #a0aec0; font-size: 0.9rem;">
            شكرًا لزيارة <span class="brand-name">{{ $settings->site_name }}</span>
        </div>
    </div>
</body>

</html>