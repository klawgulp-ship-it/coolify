<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToProvider(Request $request, string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        $this->validateProvider($provider);
        $settings = instanceSettings();

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'OAuth authentication failed. Please try again.']);
        }

        $email = strtolower(trim($oauthUser->getEmail()));
        if (empty($email)) {
            return redirect()->route('login')->withErrors(['oauth' => 'No email address returned from OAuth provider.']);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user, true);
            $team = $user->currentTeam();
            session(['currentTeam' => $team]);

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        if (! $settings->is_registration_enabled && ! $settings->is_registration_enabled_for_oauth) {
            return redirect()->route('login')->withErrors(['oauth' => 'Registration is disabled.']);
        }

        $user = User::create([
            'name' => $oauthUser->getName() ?: $oauthUser->getNickname() ?: $email,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);

        $team = $user->teams()->first();
        Auth::login($user, true);
        session(['currentTeam' => $user->currentTeam = $team]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function validateProvider(string $provider): void
    {
        $allowed = ['github', 'gitlab', 'google', 'bitbucket', 'azure', 'authentik'];
        if (! in_array($provider, $allowed)) {
            abort(404);
        }
    }
}
