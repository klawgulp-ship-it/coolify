<?php

namespace App\Livewire\Settings;

use App\Models\InstanceSettings;
use Livewire\Component;

class OauthSettings extends Component
{
    public bool $isRegistrationEnabledForOauth = false;
    public bool $isOauthOnlyRegistration = false;

    public function mount()
    {
        $settings = InstanceSettings::get();
        $this->isRegistrationEnabledForOauth = $settings->is_registration_enabled_for_oauth;
        $this->isOauthOnlyRegistration = $settings->is_oauth_only_registration;
    }

    public function submit()
    {
        $settings = InstanceSettings::get();
        $settings->is_registration_enabled_for_oauth = $this->isRegistrationEnabledForOauth;
        $settings->is_oauth_only_registration = $this->isOauthOnlyRegistration;
        $settings->save();
    }

    public function render()
    {
        return view('livewire.settings.oauth-settings');
    }
}
