<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    protected array $allowedProviders = ['github', 'gitlab', 'google', 'microsoft', 'authentik', 'keycloak'];

    public function redirect(Request $request, string $provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'OAuth authentication failed. Please try again.']);
        }

        $settings = instanceSettings();
        $email = strtolower(trim($oauthUser->getEmail()));

        if (empty($email)) {
            return redirect()->route('login')->withErrors(['oauth' => 'No email address returned from OAuth provider.']);
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($settings->is_oauth_only_registration && ! is_null($existingUser->password)) {
                $existingUser->forceFill(['password' => null])->save();
            }
            Auth::login($existingUser, true);
            $team = $existingUser->currentTeam ?? $existingUser->teams()->first();
            session(['currentTeam' => $existingUser->currentTeam = $team]);

            return redirect()->intended(route('dashboard'));
        }

        $canRegister = $settings->is_registration_enabled
            || $settings->is_registration_enabled_for_oauth
            || User::count() === 0;

        if (! $canRegister) {
            return redirect()->route('login')->withErrors(['oauth' => 'Registration is disabled. Please contact your administrator.']);
        }

        $name = trim($oauthUser->getName() ?? $oauthUser->getNickname() ?? explode('@', $email)[0]);

        $isFirstUser = User::count() === 0;

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $settings->is_oauth_only_registration ? null : Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ];

        if ($isFirstUser) {
            $userData['id'] = 0;
        }

        $user = User::create($userData);
        $team = $user->teams()->first();

        if ($isFirstUser) {
            $settings->is_registration_enabled = false;
            $settings->save();
        }

        session(['currentTeam' => $user->currentTeam = $team]);
        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
