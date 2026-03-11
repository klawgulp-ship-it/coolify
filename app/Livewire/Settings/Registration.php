<?php

namespace App\Livewire\Settings;

use App\Models\InstanceSettings;
use Livewire\Component;

class Registration extends Component
{
    public bool $is_registration_enabled = false;
    public bool $is_registration_enabled_for_oauth = false;

    public function mount()
    {
        $settings = instanceSettings();
        $this->is_registration_enabled = $settings->is_registration_enabled;
        $this->is_registration_enabled_for_oauth = $settings->is_registration_enabled_for_oauth;
    }

    public function save()
    {
        $settings = instanceSettings();
        $settings->is_registration_enabled = $this->is_registration_enabled;
        $settings->is_registration_enabled_for_oauth = $this->is_registration_enabled_for_oauth;
        $settings->save();
        $this->dispatch('success', 'Registration settings saved.');
    }

    public function render()
    {
        return view('livewire.settings.registration');
    }
}
