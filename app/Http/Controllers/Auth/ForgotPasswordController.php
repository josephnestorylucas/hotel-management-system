<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly SmsService $smsService)
    {
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30', 'required_without:email'],
        ]);

        $safeMessage = __('auth.reset.safe_notice');

        if (!empty($validated['email'])) {
            Password::sendResetLink(['email' => $validated['email']]);
            Log::info('Password reset link requested via email.', [
                'email_hash' => hash('sha256', $validated['email']),
                'ip' => $request->ip(),
            ]);

            return back()->with('success', $safeMessage);
        }

        $normalizedPhone = PhoneNumber::normalize($validated['phone'] ?? null);

        if (!PhoneNumber::isValid($normalizedPhone)) {
            return back()
                ->withErrors(['phone' => __('auth.reset.invalid_phone')])
                ->withInput();
        }

        $ip = $request->ip() ?? 'unknown';
        $phoneHash = hash('sha256', $normalizedPhone);
        $cooldownKey = "password-reset:phone-cooldown:{$ip}:{$phoneHash}";
        $phoneLockKey = "password-reset:phone-lock:{$phoneHash}";
        $ipLockKey = "password-reset:ip-lock:{$ip}";

        if (
            RateLimiter::tooManyAttempts($cooldownKey, 1) ||
            RateLimiter::tooManyAttempts($phoneLockKey, 5) ||
            RateLimiter::tooManyAttempts($ipLockKey, 10)
        ) {
            Log::warning('Phone password reset throttled.', [
                'ip' => $ip,
                'cooldown_remaining' => RateLimiter::availableIn($cooldownKey),
                'phone_lock_remaining' => RateLimiter::availableIn($phoneLockKey),
                'ip_lock_remaining' => RateLimiter::availableIn($ipLockKey),
            ]);

            return back()->with('success', $safeMessage);
        }

        RateLimiter::hit($cooldownKey, 60);
        RateLimiter::hit($phoneLockKey, 900);
        RateLimiter::hit($ipLockKey, 900);

        $user = User::where('phone', $normalizedPhone)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            Log::info('Phone password reset requested for unknown or inactive user.', [
                'ip' => $ip,
            ]);

            return back()->with('success', $safeMessage);
        }

        $temporaryPassword = $this->generateTemporaryPassword();
        $requestedAt = now();

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'password_reset_requested_at' => $requestedAt,
            'password_reset_completed_at' => now(),
            'password_reset_phone' => $normalizedPhone,
            'must_change_password' => true,
        ])->save();

        Log::info('Phone password reset completed (self-service).', [
            'user_id' => $user->id,
            'actor' => 'self-service',
            'requested_at' => $requestedAt->toIso8601String(),
            'completed_at' => now()->toIso8601String(),
            'ip' => $ip,
        ]);

        $message = __('auth.reset.sms_message', [
            'name' => $user->name ?: __('auth.reset.user_fallback'),
            'temp' => $temporaryPassword,
        ]);

        try {
            $sent = $this->smsService->send($normalizedPhone, $message);

            if ($sent) {
                Log::info('Temporary password SMS sent.', [
                    'user_id' => $user->id,
                    'ip' => $ip,
                ]);
            } else {
                Log::error('Temporary password SMS failed to send.', [
                    'user_id' => $user->id,
                    'ip' => $ip,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Temporary password SMS threw exception.', [
                'user_id' => $user->id,
                'ip' => $ip,
                'error' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', $safeMessage);
    }

    private function generateTemporaryPassword(): string
    {
        return Str::password(16);
    }
}

