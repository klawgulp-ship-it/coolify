<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class OAuthSettings extends Component
{
    public bool $is_oauth_registration_enabled = false;

    public function mount(): void
    {
        $settings = instanceSettings();
        $this->is_oauth_registration_enabled = (bool) $settings->is_oauth_registration_enabled;
    }

    public function save(): void
    {
        $settings = instanceSettings();
        $settings->is_oauth_registration_enabled = $this->is_oauth_registration_enabled;
        $settings->save();

        $this->dispatch('success', 'OAuth registration setting saved.');
    }

    public function render()
    {
        return view('livewire.settings.oauth-settings');
    }
}
