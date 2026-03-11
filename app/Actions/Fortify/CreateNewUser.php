<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $settings = instanceSettings();
        $isOAuthRegistration = ! empty($input['oauth_id']);

        if (! $settings->is_registration_enabled) {
            if (! $isOAuthRegistration || ! $settings->is_oauth_only_registration_enabled) {
                abort(403);
            }
        }

        $passwordRules = $isOAuthRegistration
            ? ['nullable']
            : ['required', Password::defaults(), 'confirmed'];

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $passwordRules,
        ])->validate();

        if (User::count() == 0) {
            // If this is the first user, make them the root user
            // Team is already created in the database/seeders/ProductionSeeder.php
            $userData = [
                'id' => 0,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => isset($input['password']) ? Hash::make($input['password']) : null,
            ];

            if ($isOAuthRegistration) {
                $userData['oauth_id'] = $input['oauth_id'];
                $userData['oauth_provider'] = $input['oauth_provider'] ?? null;
                if ($settings->is_oauth_only_registration_enabled) {
                    $userData['is_oauth_only'] = true;
                }
            }

            $user = User::create($userData);
            $team = $user->teams()->first();

            // Disable registration after first user is created
            $settings = instanceSettings();
            $settings->is_registration_enabled = false;
            $settings->save();
        } else {
            $userData = [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => isset($input['password']) ? Hash::make($input['password']) : null,
            ];

            if ($isOAuthRegistration) {
                $userData['oauth_id'] = $input['oauth_id'];
                $userData['oauth_provider'] = $input['oauth_provider'] ?? null;
                if ($settings->is_oauth_only_registration_enabled) {
                    $userData['is_oauth_only'] = true;
                }
            }

            $user = User::create($userData);
            $team = $user->teams()->first();
            if (isCloud()) {
                $user->sendVerificationEmail();
            } else {
                $user->markEmailAsVerified();
            }
        }
        // Set session variable
        session(['currentTeam' => $user->currentTeam = $team]);

        return $user;
    }
}
