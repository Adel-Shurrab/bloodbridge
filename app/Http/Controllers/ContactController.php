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
        if (!$settings->enable_contact_messages) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Contact messages are currently disabled by the administrator.')
                ], 403);
            }
            return back()->with('error', __('Contact messages are currently disabled by the administrator.'));
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
                $supportEmail = $settings->support_email ?? 'admin@bloodbridge.com';
                $siteName = $settings->site_name ?? config('app.name');

                if ($supportEmail) {
                    Mail::to($supportEmail)->send(new AdminContactNotificationMail($contactMessage));
                }

                Mail::to($contactMessage->email)->send(new UserContactConfirmationMail($contactMessage, $siteName));
            } catch (\Exception $e) {
                Log::error('Failed to send contact emails: ' . $e->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Your message has been sent successfully! We will reply to you soon.')
                ]);
            }

            return back()->with('success', __('Your message has been sent successfully! We will reply to you soon.'));
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
                    'message' => __('An error occurred while sending your message. Please try again later.')
                ], 500);
            }
            return back()->with('error', __('An error occurred while sending your message. Please try again later.'));
        }
    }
}
