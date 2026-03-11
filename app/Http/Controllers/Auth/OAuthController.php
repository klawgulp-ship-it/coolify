<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class OAuthController
{
    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProviderIsAllowed($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->ensureProviderIsAllowed($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()->route('login')->withErrors(['oauth' => 'OAuth authentication failed. Please try again.']);
        }

        $settings = instanceSettings();

        $user = User::where('oauth_provider', $provider)
            ->where('oauth_provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            $user = User::where('email', strtolower($socialUser->getEmail()))->first();
        }

        if ($user) {
            if (is_null($user->oauth_provider)) {
                $user->oauth_provider = $provider;
                $user->oauth_provider_id = $socialUser->getId();
                $user->save();
            }
        } else {
            $isFirstUser = User::count() === 0;
            $canRegister = $isFirstUser
                || $settings->is_registration_enabled
                || $settings->is_oauth_registration_enabled;

            if (! $canRegister) {
                return redirect()->route('login')->withErrors(['oauth' => 'Registration is disabled. Please contact an administrator.']);
            }

            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                'email' => strtolower($socialUser->getEmail()),
                'password' => null,
                'oauth_provider' => $provider,
                'oauth_provider_id' => $socialUser->getId(),
            ]);

            if ($isFirstUser) {
                $settings->is_registration_enabled = false;
                $settings->save();
            }

            $user->markEmailAsVerified();

            $team = $user->teams()->first();
            session(['currentTeam' => $user->currentTeam = $team]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    private function ensureProviderIsAllowed(string $provider): void
    {
        $allowed = ['github', 'gitlab', 'google', 'bitbucket'];

        if (! in_array($provider, $allowed, true)) {
            abort(404);
        }
    }
}
