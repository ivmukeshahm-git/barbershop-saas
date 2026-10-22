<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showForgotForm(Request $request)
    {
        $this->clearResetSession($request);

        return view('admin.auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Please enter your registered email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::whereRaw('LOWER(email) = ?', [$email])
            ->where('active_status', 1)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'We could not find an active account with that email address.',
            ]);
        }

        try {
            $this->issueAndSendOtp($user, $email);
        } catch (\Throwable $e) {
            report($e);

            $message = 'Unable to send the verification email right now. Please try again in a few minutes.';
            if (config('app.debug')) {
                $message .= ' (' . $e->getMessage() . ')';
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        return redirect()
            ->route('admin.forgot-password.verify-otp')
            ->with('success', 'A verification code has been sent to your email.');
    }

    public function showVerifyOtp(Request $request)
    {
        if (!$request->session()->has('password_reset_user_id') || !$request->session()->has('password_reset_otp_id')) {
            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please enter your registered email address to continue.']);
        }

        $otpRecord = PasswordResetOtp::find($request->session()->get('password_reset_otp_id'));

        if (!$otpRecord || $otpRecord->isVerified()) {
            $this->clearResetSession($request);

            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please enter your registered email address to continue.']);
        }

        return view('admin.auth.verify-otp', [
            'maskedEmail' => $this->maskEmail($request->session()->get('password_reset_email')),
            'expiresAt' => optional($otpRecord->expires_at)->timestamp,
            'expireMinutes' => config('password_reset_otp.otp_expire_minutes', 10),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.digits' => 'The OTP must be exactly 6 digits.',
        ]);

        $otpRecord = $this->currentOtpRecord($request);

        if (!$otpRecord) {
            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please enter your registered email address to continue.']);
        }

        if ($otpRecord->isVerified()) {
            $this->clearResetSession($request);

            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'This OTP has already been used. Please request a new one.']);
        }

        $maxAttempts = (int) config('password_reset_otp.max_attempts', 5);

        if ($otpRecord->hasExceededAttempts($maxAttempts)) {
            throw ValidationException::withMessages([
                'otp' => 'Too many attempts. Please try again later.',
            ]);
        }

        if ($otpRecord->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => 'This OTP has expired. Please request a new OTP.',
            ]);
        }

        if (!Hash::check($request->input('otp'), $otpRecord->otp)) {
            $otpRecord->increment('attempts');

            if ($otpRecord->fresh()->hasExceededAttempts($maxAttempts)) {
                throw ValidationException::withMessages([
                    'otp' => 'Too many attempts. Please try again later.',
                ]);
            }

            throw ValidationException::withMessages([
                'otp' => 'The OTP you entered is incorrect.',
            ]);
        }

        $token = Str::random(64);
        $tokenExpireMinutes = (int) config('password_reset_otp.reset_token_expire_minutes', 15);

        $otpRecord->forceFill([
            'verified_at' => now(),
            'reset_token' => $token,
            'reset_token_expires_at' => now()->addMinutes($tokenExpireMinutes),
            'reset_token_used_at' => null,
        ])->save();

        $request->session()->put('password_reset_token', $token);
        $request->session()->forget('password_reset_otp_id');

        return redirect()
            ->route('admin.forgot-password.reset')
            ->with('success', 'OTP verified successfully. Please create a new password.');
    }

    public function resendOtp(Request $request)
    {
        $userId = $request->session()->get('password_reset_user_id');
        $email = $request->session()->get('password_reset_email');

        if (!$userId || !$email) {
            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please enter your registered email address to continue.']);
        }

        $user = User::where('id', $userId)->where('email', $email)->where('active_status', 1)->first();

        if (!$user) {
            $this->clearResetSession($request);

            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'We could not find an active account with that email address.']);
        }

        try {
            $this->issueAndSendOtp($user, $email);
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'otp' => 'Unable to resend the verification email right now. Please try again in a few minutes.',
            ]);
        }

        return redirect()
            ->route('admin.forgot-password.verify-otp')
            ->with('success', 'A new verification code has been sent to your email.');
    }

    public function showResetForm(Request $request)
    {
        if (!$this->hasValidResetToken($request)) {
            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please verify your email and OTP before resetting your password.']);
        }

        return view('admin.auth.reset-password', [
            'passwordMinLength' => (int) config('password_reset_otp.password_min_length', 6),
        ]);
    }

    public function resetPassword(Request $request)
    {
        if (!$this->hasValidResetToken($request)) {
            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Please verify your email and OTP before resetting your password.']);
        }

        $minLength = (int) config('password_reset_otp.password_min_length', 6);

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min($minLength)->letters()->numbers(),
            ],
        ], [
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'Confirm password does not match.',
            'password.min' => "Password must be at least {$minLength} characters.",
        ]);

        $userId = $request->session()->get('password_reset_user_id');
        $token = $request->session()->get('password_reset_token');
        $email = $request->session()->get('password_reset_email');

        $otpRecord = PasswordResetOtp::where('user_id', $userId)
            ->where('email', $email)
            ->where('reset_token', $token)
            ->whereNotNull('verified_at')
            ->whereNull('reset_token_used_at')
            ->latest('id')
            ->first();

        if (!$otpRecord || !$otpRecord->isResetTokenValid($token)) {
            $this->clearResetSession($request);

            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'Your password reset session has expired. Please try again.']);
        }

        $user = User::find($userId);

        if (!$user || $user->active_status != 1) {
            $this->clearResetSession($request);

            return redirect()
                ->route('admin.forgot-password')
                ->withErrors(['email' => 'We could not find an active account with that email address.']);
        }

        DB::transaction(function () use ($user, $request, $otpRecord, $userId) {
            $user->forceFill([
                'password' => Hash::make($request->input('password')),
                'remember_token' => Str::random(60),
            ])->save();

            $otpRecord->forceFill([
                'reset_token_used_at' => now(),
                'reset_token' => null,
                'reset_token_expires_at' => null,
            ])->save();

            PasswordResetOtp::where('user_id', $userId)
                ->whereNull('reset_token_used_at')
                ->where('id', '!=', $otpRecord->id)
                ->update([
                    'expires_at' => now(),
                    'reset_token' => null,
                    'reset_token_expires_at' => null,
                ]);
        });

        $this->clearResetSession($request);

        return redirect()
            ->route('login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    protected function issueAndSendOtp(User $user, string $email): void
    {
        $expireMinutes = (int) config('password_reset_otp.otp_expire_minutes', 10);
        $plainOtp = (string) random_int(100000, 999999);

        $otpRecord = DB::transaction(function () use ($user, $email, $plainOtp, $expireMinutes) {
            PasswordResetOtp::where('user_id', $user->id)
                ->whereNull('verified_at')
                ->where('expires_at', '>', now())
                ->update(['expires_at' => now()]);

            PasswordResetOtp::where('user_id', $user->id)
                ->whereNotNull('verified_at')
                ->whereNull('reset_token_used_at')
                ->whereNotNull('reset_token')
                ->update([
                    'reset_token' => null,
                    'reset_token_expires_at' => null,
                ]);

            return PasswordResetOtp::create([
                'user_id' => $user->id,
                'email' => $email,
                'otp' => Hash::make($plainOtp),
                'expires_at' => now()->addMinutes($expireMinutes),
                'attempts' => 0,
            ]);
        });

        Mail::to($email)->send(new PasswordResetOtpMail([
            'name' => $user->name ?: 'Admin',
            'otp' => $plainOtp,
            'expire_minutes' => $expireMinutes,
            'subject' => 'Your Password Reset OTP - Rosenthal Barbershop',
        ]));

        // Never log the plain OTP.
        unset($plainOtp);

        session([
            'password_reset_user_id' => $user->id,
            'password_reset_email' => $email,
            'password_reset_otp_id' => $otpRecord->id,
        ]);
        session()->forget('password_reset_token');
    }

    protected function currentOtpRecord(Request $request): ?PasswordResetOtp
    {
        $otpId = $request->session()->get('password_reset_otp_id');
        $userId = $request->session()->get('password_reset_user_id');

        if (!$otpId || !$userId) {
            return null;
        }

        return PasswordResetOtp::where('id', $otpId)
            ->where('user_id', $userId)
            ->first();
    }

    protected function hasValidResetToken(Request $request): bool
    {
        $userId = $request->session()->get('password_reset_user_id');
        $token = $request->session()->get('password_reset_token');
        $email = $request->session()->get('password_reset_email');

        if (!$userId || !$token || !$email) {
            return false;
        }

        $otpRecord = PasswordResetOtp::where('user_id', $userId)
            ->where('email', $email)
            ->where('reset_token', $token)
            ->whereNotNull('verified_at')
            ->whereNull('reset_token_used_at')
            ->latest('id')
            ->first();

        return $otpRecord && $otpRecord->isResetTokenValid($token);
    }

    protected function clearResetSession(Request $request): void
    {
        $request->session()->forget([
            'password_reset_user_id',
            'password_reset_email',
            'password_reset_otp_id',
            'password_reset_token',
        ]);
    }

    protected function maskEmail(?string $email): string
    {
        if (!$email || !str_contains($email, '@')) {
            return 'your email';
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = max(1, (int) floor(strlen($local) / 3));
        $maskedLocal = substr($local, 0, $visible).str_repeat('*', max(1, strlen($local) - $visible));

        return $maskedLocal.'@'.$domain;
    }
}
