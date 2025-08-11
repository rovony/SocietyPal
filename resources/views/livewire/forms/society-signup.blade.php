<div class="w-full p-6 mx-auto bg-white rounded-lg shadow-md dark:bg-gray-700 h-fit">

    @if ($showUserForm)
        <form wire:submit="submitForm">
            @csrf

            <h2 class="mt-3 mb-6 text-xl font-medium dark:text-white">@lang('auth.createAccountSignup', ['appName' => global_setting()->name])</h2>
            <div>
                <x-label for="societyName" value="{{ __('saas.societyName') }}" required/>
                <x-input id="societyName" class="block w-full mt-1" type="text" wire:model='societyName'/>
                <x-input-error for="societyName" class="mt-2"/>
            </div>

            @includeIf('subdomain::include.register-subdomain')

            <div class="mt-4">
                <x-label for="fullName" value="{{ __('saas.fullName') }}" required/>
                <x-input id="fullName" class="block w-full mt-1" type="text" wire:model='fullName'/>
                <x-input-error for="fullName" class="mt-2"/>
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('saas.email') }}" required/>
                <x-input id="email" class="block w-full mt-1" type="email" wire:model='email'/>
                <x-input-error for="email" class="mt-2"/>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('saas.password') }}" required/>
                <x-input id="password" class="block w-full mt-1" type="password" autocomplete="new-password"
                         wire:model='password'/>
                <x-input-error for="password" class="mt-2"/>
            </div>


            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required/>

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif



            <div class="grid items-center grid-cols-1 gap-2 mt-4">
                <a class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                   href="{{ route('login') }}">
                    @lang('auth.alreadyRegisteredLoginHere')
                </a>

                <x-button>
                    @lang('modules.society.nextSocietyDetails')
                </x-button>
            </div>
        </form>
    @endif

    @if ($showSocietyForm)
        <form wire:submit="submitForm2">
            @csrf

            <h2 class="mt-3 mb-6 text-xl font-medium dark:text-white">@lang('modules.society.societyDetails')</h2>

            <div class="mt-4">
                <x-label for="country" value="{{ __('saas.country') }}"/>
                <x-select id="societyCountry" class="block w-full mt-1" wire:model="country">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}">{{ $item->countries_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="country" class="mt-2"/>
            </div>

            <div class="mt-4">
                <x-label for="address" value="{{ __('saas.address') }}"/>
                <x-textarea id="address" class="block w-full mt-1" rows="3" wire:model='address'/>
                <x-input-error for="address" class="mt-2"/>
            </div>
            @if (global_setting()->google_recaptcha_status == 'active')
            <div class="form-group" id="captcha_container"></div>
           @endif

            <x-input-error for="isCaptchaVerified" class="mt-2"/>

            <div class="items-center grid-cols-1 gap-2 mt-4 lg:grid">
                @php($target = 'submitForm2')
                <x-button target="submitForm2">
                    {{ __('auth.signup') }}
                </x-button>
            </div>
        </form>
    @endif

    @if (global_setting()->google_recaptcha_status == 'active' && global_setting()->google_recaptcha_v3_status == 'active')
    <script
        src="https://www.google.com/recaptcha/api.js?render={{ global_setting()->google_recaptcha_v3_site_key }}">
    </script>
    @script
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('{{ global_setting()->google_recaptcha_v3_site_key }}').then(function (token) {
                const recaptchaInput = document.getElementById('g_recaptcha');
                @this.set('captcha', token);
            });
        });
    </script>
    @endscript
@endif

</div>
