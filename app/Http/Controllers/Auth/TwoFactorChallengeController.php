<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwoFactorChallengeController extends Controller
{
    public function create()
    {
        return view('auth.two-factor-challenge');
    }

    public function store(Request $request)
    {
        $code = $request->code;
        $recovery_code = $request->recovery_code;

        if ($code) {
            $valid = $request->user()->verifyTwoFactorCode($code);
        } elseif ($recovery_code) {
            $valid = $request->user()->verifyTwoFactorRecoveryCode($recovery_code);
        } else {
            return back()->withErrors(['code' => 'Please provide a verification code.']);
        }

        if ($valid) {
            session()->put('two_factor_confirmed', true);
            
            // Ensure CSRF token is preserved
            session()->keep(['_token']);
            
            // Log successful verification
            Log::info('2FA verification successful', [
                'user' => $request->user()->email,
                'method' => $code ? 'code' : 'recovery_code'
            ]);

            return redirect()->intended(
                session()->pull('url.intended', route('home'))
            );
        }

        return back()->withErrors(['code' => 'The provided code was invalid.']);
    }

    public function showRecoveryForm()
    {
        return view('auth.2fa-recovery');
    }

    public function recovery(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = auth()->user();
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes));

        if (in_array($request->recovery_code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$request->recovery_code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();

            session(['2fa.confirmed' => true]);

            // Notify about recovery code usage
            $user->notify(new SecurityAlert(
                'Recovery Code Used',
                'A recovery code was used to access your account. Please generate new recovery codes.',
                'Generate New Codes',
                route('profile.2fa.recovery-codes'),
                'warning'
            ));

            return redirect()->intended(route('welcome.back'));
        }

        return back()->withErrors(['recovery_code' => 'The provided recovery code was invalid.']);
    }
}
