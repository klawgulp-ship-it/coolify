<div class="flex items-center gap-2">
    <x-forms.checkbox
        id="is_registration_enabled_for_oauth"
        label="Allow OAuth2 Registration"
        helper="Allow new users to self-register via OAuth2 even when general registration is disabled."
    />
</div>
<div class="flex items-center gap-2">
    <x-forms.checkbox
        id="is_oauth_only_registration"
        label="OAuth2-Only Mode"
        helper="Restrict all users to OAuth2 login only. Password-based registration and login will be blocked. Existing passwords will be cleared on next OAuth login."
    />
</div>
