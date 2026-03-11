<div>
    <form wire:submit.prevent='save'>
        <div class='flex flex-col gap-4'>
            <div class='flex items-center gap-2'>
                <x-forms.checkbox
                    id='is_registration_enabled'
                    wire:model='is_registration_enabled'
                    label='Enable General Self-Registration'
                    helper='Allow anyone to register using email and password.'
                />
            </div>
            <div class='flex items-center gap-2'>
                <x-forms.checkbox
                    id='is_registration_enabled_for_oauth'
                    wire:model='is_registration_enabled_for_oauth'
                    label='Enable OAuth Self-Registration'
                    helper='Allow users to register via OAuth2 providers even if general registration is disabled.'
                />
            </div>
            <div>
                <x-forms.button type='submit'>Save</x-forms.button>
            </div>
        </div>
    </form>
</div>
