<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerAuthController extends Controller
{
    /**
     * Show scanner login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('scanner.login');
    }

    /**
     * Handle scanner login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle scanner logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('scanner.login');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->hasRole('wristband_exchange_officer')) {
            return redirect()->route('scanner.exchange');
        }

        if ($user->hasRole('wristband_validator')) {
            return redirect()->route('scanner.validate');
        }

        Auth::logout();
        return redirect()->route('scanner.login')
            ->withErrors(['email' => 'Anda tidak memiliki akses ke dalam sistem scanner.']);
    }
}
