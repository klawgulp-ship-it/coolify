<div>
    <form wire:submit.prevent='save'>
        <div class='flex flex-col gap-4'>
            <div class='flex items-center gap-2'>
                <x-forms.checkbox
                    id='is_oauth_registration_enabled'
                    label='Allow OAuth-only self-registration'
                    helper='When enabled, users can register via OAuth even if general self-registration is disabled.'
                    wire:model.defer='is_oauth_registration_enabled'
                />
            </div>
            <div>
                <x-forms.button type='submit'>Save</x-forms.button>
            </div>
        </div>
    </form>
</div>
