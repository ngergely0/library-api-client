<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $response = Http::api()->post('/users/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $responseBody = $response->json();
            
            // Check if data exists in response
            if (empty($responseBody['data'])) {
                $message = $responseBody['message'] ?? 'Ismeretlen hiba történt.';
                return back()->withErrors([
                    'email' => $message,
                ]);
            }
            
            $token = $responseBody['data']['token'];
            $user = $responseBody['data']['user'] ?? ['name' => 'User', 'email' => $request->email];
            
            session([
                'api_token' => $token,
                'user_name' => $user['name'],
                'user_email' => $user['email'],
            ]);

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Hibás bejelentkezési adatok.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');
        session()->forget('user_name');
        session()->forget('user_email');

        return redirect('/');
    }
}
