<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\UserContactConfirmationMail;
use App\Mail\AdminContactNotificationMail;
use Illuminate\Support\Facades\Log;
use App\Settings\GeneralSettings;

class ContactController extends Controller
{
    public function submit(Request $request, GeneralSettings $settings)
    {
        $settings = app(GeneralSettings::class);
        if (!$settings->enable_contact_messages) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'استقبال الرسائل معطل حالياً من قبل الإدارة.'
                ], 403);
            }
            return back()->with('error', 'استقبال الرسائل معطل حالياً من قبل الإدارة.');
        }

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['required', 'string', 'min:5', 'max:255'],
                'message' => ['required', 'string', 'min:10'],
                'privacy' => ['accepted'],
            ]);

            $contactMessage = ContactMessage::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'ip_address' => $request->ip(),
            ]);

            // Try sending emails
            try {
                // To Admin: We will assume support_email from settings or a default
                $supportEmail = $settings->support_email ?? 'admin@bloodbridge.com';
                if ($supportEmail) {
                    Mail::to($supportEmail)->send(new AdminContactNotificationMail($contactMessage));
                }

                // To User
                $siteName = $settings->site_name ?? config('app.name');
                Mail::to($contactMessage->email)->send(new UserContactConfirmationMail($contactMessage, $siteName));
            } catch (\Exception $e) {
                Log::error('Failed to send contact emails: ' . $e->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال رسالتك بنجاح! سنرد عليك قريباً.'
                ]);
            }

            return back()->with('success', 'تم إرسال رسالتك بنجاح! سنرد عليك قريباً.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة لاحقاً.'
                ], 500);
            }
            return back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة لاحقاً.');
        }
    }
}
