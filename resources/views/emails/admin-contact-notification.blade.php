<!DOCTYPE html>
<html lang="ar" dir="rtl">

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

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .details th {
            text-align: right;
            padding-left: 10px;
            width: 120px;
            color: #6b7280;
        }

        .details td {
            font-weight: bold;
        }

        .message-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            white-space: pre-wrap;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #e11d48; margin: 0;">رسالة اتصال جديدة</h2>
        </div>
        <p>لقد تلقيت رسالة اتصال جديدة من خلال الموقع.</p>

        <table class="details">
            <tr>
                <th>الاسم:</th>
                <td>{{ $contactMessage->name }}</td>
            </tr>
            <tr>
                <th>البريد الإلكتروني:</th>
                <td>{{ $contactMessage->email }}</td>
            </tr>
            <tr>
                <th>الموضوع:</th>
                <td>{{ $contactMessage->subject }}</td>
            </tr>
            <tr>
                <th>عنوان الـ IP:</th>
                <td>{{ $contactMessage->ip_address }}</td>
            </tr>
        </table>

        <div style="margin-top: 20px;">
            <strong>نص الرسالة:</strong>
            <div class="message-box">{{ $contactMessage->message }}</div>
        </div>
    </div>
</body>

</html>
