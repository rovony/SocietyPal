<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

    <h3 class="mb-8 text-xl font-semibold dark:text-white">@lang('modules.settings.googleRecaptcha')</h3>

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <x-label for="site_key" :value="__('modules.settings.googleRecaptchaV3Key')" required />
                <x-input
                    id="site_key"
                    class="block w-full mt-1"
                    type="text"
                    wire:model="site_key"
                    autocomplete="off"
                    placeholder="Enter Site Key"
                />
                <x-input-error for="site_key" class="mt-2" />
            </div>

            <div>
                <x-label for="secret_key" :value="__('modules.settings.googleRecaptchaV3Secret')" required />
                <x-input
                    id="secret_key"
                    class="block w-full mt-1"
                    type="text"
                    wire:model="secret_key"
                    autocomplete="off"
                    placeholder="Enter Secret Key"
                />
                <x-input-error for="secret_key" class="mt-2" />
            </div>
        </div>

        <input type="hidden" wire:model.defer="recaptchaToken">

        @error('captchaToken')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="pt-4">
            <x-button
                type="button"
                onclick="handleCaptcha()"
                class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
                {{ __('Send') }}
            </x-button>
        </div>
    </form>

    <script>
        function handleCaptcha() {
            const siteKey = document.getElementById('site_key').value;


            // Load reCAPTCHA dynamically
            if (!window.grecaptcha) {
                const script = document.createElement('script');
                script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
                script.onload = () => executeCaptcha(siteKey);
                document.body.appendChild(script);
            } else {
                executeCaptcha(siteKey);
            }
        }

        function executeCaptcha(siteKey) {
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: 'submit' }).then(function (token) {
                    @this.set('captchaToken', token);
                    @this.submit();
                });
            });
        }
    </script>
</div>
