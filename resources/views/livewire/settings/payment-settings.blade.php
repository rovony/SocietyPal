<div>
    <div
        class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.paymentgatewaySettings')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.paymentHelp')</x-help-text>
        <div
            class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap items-center -mb-px">
                @if( in_array('Accept Maintenance Payment', society_modules()))
                    <li class="me-2">
                        <span wire:click="activeSetting('razorpay')" @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'razorpay',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'razorpay',
                        ])>
                            <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24">
                                <defs>
                                    <linearGradient id="a" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" stop-color="#0d3e8e" />
                                        <stop offset="100%" stop-color="#00c3f3" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#a)"
                                    d="m22.436 0-11.91 7.773-1.174 4.276 6.625-4.297L11.65 24h4.391z" />
                                <path fill="#0D3E8E" d="M14.26 10.098 3.389 17.166 1.564 24h9.008z" />
                            </svg>
                            @lang('modules.billing.razorpay')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isRazorpayEnabled,
                                'bg-red-500' => !$isRazorpayEnabled,
                            ])></span>
                        </span>
                    </li>

                    <li wire:click="activeSetting('stripe')" class="me-2">
                        <span @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'stripe',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'stripe',
                        ])>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24"
                                height="24" fill="#6772e5">
                                <path
                                    d="M111.328 15.602c0-4.97-2.415-8.9-7.013-8.9s-7.423 3.924-7.423 8.863c0 5.85 3.32 8.8 8.036 8.8 2.318 0 4.06-.528 5.377-1.26V19.22a10.25 10.25 0 0 1-4.764 1.075c-1.9 0-3.556-.67-3.774-2.943h9.497a40 40 0 0 0 .063-1.748zm-9.606-1.835c0-2.186 1.35-3.1 2.56-3.1s2.454.906 2.454 3.1zM89.4 6.712a5.43 5.43 0 0 0-3.801 1.509l-.254-1.208h-4.27v22.64l4.85-1.032v-5.488a5.43 5.43 0 0 0 3.444 1.265c3.472 0 6.64-2.792 6.64-8.957.003-5.66-3.206-8.73-6.614-8.73zM88.23 20.1a2.9 2.9 0 0 1-2.288-.906l-.03-7.2a2.93 2.93 0 0 1 2.315-.96c1.775 0 2.998 2 2.998 4.528.003 2.593-1.198 4.546-2.995 4.546zM79.25.57l-4.87 1.035v3.95l4.87-1.032z"
                                    fill-rule="evenodd" />
                                <path d="M74.38 7.035h4.87V24.04h-4.87z" />
                                <path
                                    d="m69.164 8.47-.302-1.434h-4.196V24.04h4.848V12.5c1.147-1.5 3.082-1.208 3.698-1.017V7.038c-.646-.232-2.913-.658-4.048 1.43zm-9.73-5.646L54.698 3.83l-.02 15.562c0 2.87 2.158 4.993 5.038 4.993 1.585 0 2.756-.302 3.405-.643v-3.95c-.622.248-3.683 1.138-3.683-1.72v-6.9h3.683V7.035h-3.683zM46.3 11.97c0-.758.63-1.05 1.648-1.05a10.9 10.9 0 0 1 4.83 1.25V7.6a12.8 12.8 0 0 0-4.83-.888c-3.924 0-6.557 2.056-6.557 5.488 0 5.37 7.375 4.498 7.375 6.813 0 .906-.78 1.186-1.863 1.186-1.606 0-3.68-.664-5.307-1.55v4.63a13.5 13.5 0 0 0 5.307 1.117c4.033 0 6.813-1.992 6.813-5.485 0-5.796-7.417-4.76-7.417-6.943zM13.88 9.515c0-1.37 1.14-1.9 2.982-1.9A19.66 19.66 0 0 1 25.6 9.876v-8.27A23.2 23.2 0 0 0 16.862.001C9.762.001 5 3.72 5 9.93c0 9.716 13.342 8.138 13.342 12.326 0 1.638-1.4 2.146-3.37 2.146-2.905 0-6.657-1.202-9.6-2.802v8.378A24.4 24.4 0 0 0 14.973 32C22.27 32 27.3 28.395 27.3 22.077c0-10.486-13.42-8.613-13.42-12.56z"
                                    fill-rule="evenodd" />
                            </svg>
                            @lang('modules.billing.stripe')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isStripeEnabled,
                                'bg-red-500' => !$isStripeEnabled,
                            ])></span>
                        </span>
                    </li>

                    <li wire:click="activeSetting('flutterwave')" class="me-2">
                        <span @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'flutterwave',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'flutterwave',
                        ])>
                            <svg class="w-4 h-4" width="24" height="24" xmlns:x="ns_extend;" xmlns:i="ns_ai;" xmlns:graph="ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 176 144.7" xml:space="preserve">
                                <style type="text/css">
                                    .st0{fill:#009A46;}
                                    .st1{fill:#FF5805;}
                                    .st2{fill:#F5AFCB;}
                                    .st3{fill:#FF9B00;}
                                </style>
                                <g>
                                    <path class="st0" d="M0,31.6c0-9.4,2.7-17.4,8.5-23.1l10,10C7.4,29.6,17.1,64.1,48.8,95.8s66.2,41.4,77.3,30.3l10,10c-18.8,18.8-61.5,5.4-97.3-30.3C14,80.9,0,52.8,0,31.6z"/>
                                    <path class="st1" d="M63.1,144.7c-9.4,0-17.4-2.7-23.1-8.5l10-10c11.1,11.1,45.6,1.4,77.3-30.3s41.4-66.2,30.3-77.3l10-10c18.8,18.8,5.4,61.5-30.3,97.3C112.4,130.7,84.2,144.7,63.1,144.7z"/>
                                    <path class="st2" d="M140.5,91.6c-6.1-17.5-18.5-36.2-34.9-52.6C69.8,3.2,27.1-10.1,8.3,8.6l0,0C7,10,8.2,13.3,10.9,16s6.1,3.9,7.4,2.6c11.1-11.1,45.6-1.4,77.3,30.3c15,15,26.2,31.8,31.6,47.3c4.7,13.6,4.3,24.6-1.2,30.1l0,0c-1.3,1.3-0.2,4.6,2.6,7.4s6.1,3.9,7.4,2.6C145.6,126.6,147.2,110.7,140.5,91.6z"/>
                                    <path class="st3" d="M167.5,8.6C157.9-1,142-2.6,122.9,4c-17.5,6.1-36.2,18.5-52.6,34.9c-35.8,35.8-49.1,78.5-30.3,97.3l0,0c1.3,1.3,4.7,0.2,7.4-2.6s3.9-6.1,2.6-7.4c-11.1-11.1-1.4-45.6,30.3-77.3c15-15,31.8-26.2,47.2-31.6c13.6-4.7,24.6-4.3,30.1,1.2l0,0c1.3,1.3,4.6,0.2,7.4-2.6S168.9,10,167.5,8.6z"/>
                                </g>
                            </svg>
                            @lang('modules.billing.flutterwave')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isFlutterwaveEnabled,
                                'bg-red-500' => !$isFlutterwaveEnabled,
                            ])></span>
                        </span>
                    </li>
                    <li wire:click="activeSetting('paypal')" class="me-2">
                        <span @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'paypal',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'paypal',
                        ])>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" aria-label="PayPal" role="img" viewBox="0 0 512 512" width="64px" height="64px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#FFFFFF"></rect><path fill="#002C8A" d="M377 184.8L180.7 399h-72c-5 0-9-5-8-10l48-304c1-7 7-12 14-12h122c84 3 107 46 92 112z"></path><path fill="#009BE1" d="M380.2 165c30 16 37 46 27 86-13 59-52 84-109 85l-16 1c-6 0-10 4-11 10l-13 79c-1 7-7 12-14 12h-60c-5 0-9-5-8-10l22-143c1-5 182-120 182-120z"></path><path fill="#001F6B" d="M197 292l20-127a14 14 0 0 1 13-11h96c23 0 40 4 54 11-5 44-26 115-128 117h-44c-5 0-10 4-11 10z"></path></g></svg>
                            @lang('modules.billing.paypal')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isPaypalEnabled,
                                'bg-red-500' => !$isPaypalEnabled,
                            ])></span>
                        </span>
                    </li>

                    <li wire:click="activeSetting('paystack')" class="me-2">
                        <span @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'paystack',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'paystack',
                        ])>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#0AA5FF">
                            <path d="M2 3.6c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V3.6Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V8.4Zm0 4.8c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Z" fill-rule="evenodd"/>
                        </svg>


                            @lang('modules.billing.paystack')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isPaystackEnabled,
                                'bg-red-500' => !$isPaystackEnabled,
                            ])></span>
                        </span>
                    </li>

                    <li wire:click="activeSetting('payfast')" class="me-2">
                        <span @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent' => $activePaymentSetting != 'payfast',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                                $activePaymentSetting == 'payfast',
                        ])>
                    <svg class="w-5 h-4" width="24" height="24" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none">
                        <g fill="#E63946">
                        <ellipse cx="32" cy="12" rx="20" ry="8"/>
                        <path d="M12 12v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/>
                        <path d="M12 24v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/>
                        <path d="M12 36v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8Z"/>
                        </g>
                    </svg>

                            @lang('modules.billing.payfast')
                            <span @class([
                                'flex w-3 h-3 me-3 rounded-full',
                                'bg-green-500' => $isPayfastEnabled,
                                'bg-red-500' => !$isPayfastEnabled,
                            ])></span>
                        </span>
                    </li>
                @endif

                <li class="me-2">
                    <span wire:click="activeSetting('offline')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activePaymentSetting != 'offline',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                            $activePaymentSetting == 'offline',
                    ])>
                        <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 15V17M6 7H18C18.5523 7 19 7.44772 19 8V16C19 16.5523 18.5523 17 18 17H6C5.44772 17 5 16.5523 5 16V8C5 7.44772 5.44772 7 6 7ZM6 7L18 7C18.5523 7 19 6.55228 19 6V4C19 3.44772 18.5523 3 18 3H6C5.44772 3 5 3.44772 5 4V6C5 6.55228 5.44772 7 6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11C13.1046 11 14 10.1046 14 9C14 7.89543 13.1046 7 12 7C10.8954 7 10 7.89543 10 9C10 10.1046 10.8954 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                        </svg>

                        @lang('modules.billing.offline')
                        <span @class([
                            'flex w-3 h-3 me-3 rounded-full',
                            'bg-green-500' => $paymentGateway->is_offline_payment_enabled || $paymentGateway->is_cash_payment_enabled,
                            'bg-red-500' => !$paymentGateway->is_offline_payment_enabled && !$paymentGateway->is_cash_payment_enabled,
                        ])></span>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Razor pay Form -->
        @if ($activePaymentSetting == 'razorpay')
            <form wire:submit="submitFormRazorpay">
                <div class="grid gap-6">

                    <div class="my-3">
                        <x-label for="razorpayStatus">
                            <div class="flex items-center cursor-pointer">
                                <x-checkbox name="razorpayStatus" id="razorpayStatus"
                                    wire:model.live='razorpayStatus' />

                                <div class="ms-2">
                                    @lang('modules.settings.enableRazorpay')
                                </div>
                            </div>
                        </x-label>
                    </div>

                    @if ($razorpayStatus)
                        <div>
                            <x-label for="razorpayKey" value="Razorpay KEY" />
                            <x-input id="razorpayKey" class="block w-full mt-1"
                                wire:model='razorpayKey' />
                            <x-input-error for="razorpayKey" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="razorpaySecret" value="Razorpay SECRET" />
                            <x-input-password id="razorpaySecret" class="block w-full mt-1"
                                wire:model='razorpaySecret' />
                            <x-input-error for="razorpaySecret" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="razorpayWebhookKey" value="Razorpay Webhook Key"/>
                            <x-input-password id="razorpayWebhookKey" class="block w-full mt-1" type="text" wire:model='razorpayWebhookKey'/>
                            <x-input-error for="razorpayWebhookKey" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <x-label value="Webhook URL" class="mb-1"/>
                            <div class="flex items-center">
                                <!-- Webhook URL Input -->
                                <x-input id="webhook-url" class="block w-full" type="text" value="{{ $webhookUrl }}" readonly/>
                                <button id="copy-button" type="button" onclick="copyWebhookUrl()" class="px-3 py-2 ml-2 text-white bg-gray-800 rounded-lg hover:bg-gray-700">
                                    Copy
                                </button>
                            </div>
                        </div>
                    @endif

                    <div>
                        <x-button>@lang('app.save')</x-button>
                    </div>
                </div>
            </form>
        @endif

        <!-- Stripe Form -->
        @if ($activePaymentSetting == 'stripe')
            <form wire:submit="submitFormStripe">
                <div class="grid gap-6">
                    <div class="my-3">
                        <x-label for="stripeStatus">
                            <div class="flex items-center cursor-pointer">
                                <x-checkbox name="stripeStatus" id="stripeStatus" wire:model.live='stripeStatus' />

                                <div class="ms-2">
                                    @lang('modules.settings.enableStripe')
                                </div>
                            </div>
                        </x-label>
                    </div>

                    @if ($stripeStatus)
                        <div>
                            <x-label for="stripeKey" value="Stripe KEY" />
                            <x-input id="stripeKey" class="block w-full mt-1"
                                wire:model='stripeKey' />
                            <x-input-error for="stripeKey" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="stripeSecret" value="Stripe SECRET" />
                            <x-input-password id="stripeSecret" class="block w-full mt-1"
                                wire:model='stripeSecret' />
                            <x-input-error for="stripeSecret" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="stripeWebhookKey" value="Stripe Webhook Key"/>
                            <x-input-password id="stripeWebhookKey" class="block w-full mt-1" type="text" wire:model='stripeWebhookKey'/>
                            <x-input-error for="stripeWebhookKey" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <x-label value="Webhook URL" class="mb-1"/>
                            <div class="flex items-center">
                                <!-- Webhook URL Input -->
                                <x-input id="webhook-url" class="block w-full" type="text" value="{{ $webhookUrl }}" readonly/>
                                <button id="copy-button" type="button" onclick="copyWebhookUrl()" class="px-3 py-2 ml-2 text-white bg-gray-800 rounded-lg hover:bg-gray-700">
                                    Copy
                                </button>
                            </div>
                        </div>
                    @endif

                    <div>
                        <x-button>@lang('app.save')</x-button>
                    </div>
                </div>
            </form>
        @endif

        <!-- Paystack Form -->
        @if($activePaymentSetting == 'paystack')
      <form wire:submit="submitFormPaystack">
    <div class="grid gap-6">

        <div class="my-3">
            <x-label for="paystackStatus">
                <div class="flex items-center cursor-pointer">
                    <x-checkbox name="paystackStatus" id="paystackStatus" wire:model.live='paystackStatus' />
                    <div class="ms-2">
                        @lang('modules.settings.enablePaystack')
                    </div>
                </div>
            </x-label>
        </div>

        @if ($paystackStatus)
            <div>
                <x-label for="paystackMode" :value="__('modules.settings.selectEnvironment')" required/>
                <x-select id="paystackMode" class="block w-full mt-1" wire:model.live="paystackMode">
                    <option value="test">@lang('app.test')</option>
                    <option value="live">@lang('app.live')</option>
                </x-select>
                <x-input-error for="paystackMode" class="mt-2"/>
            </div>

            @if ($paystackMode == 'live')
                <div class="grid grid-cols-2 gap-x-4">
                    <div>
                        <x-label for="paystackKey" value="Paystack Live KEY" required/>
                        <x-input id="paystackKey" class="block w-full mt-1" type="text" wire:model='paystackKey'/>
                        <x-input-error for="paystackKey" class="mt-2"/>
                    </div>

                    <div>
                        <x-label for="paystackSecret" value="Paystack Live SECRET" required/>
                        <x-input-password id="paystackSecret" class="block w-full mt-1" type="text" wire:model='paystackSecret'/>
                        <x-input-error for="livePaystackSecret" class="mt-2"/>
                    </div>
                </div>

                <div>
                    <x-label for="paystackMerchantEmail" value="Paystack Live Merchant Email" required/>
                    <x-input id="paystackMerchantEmail" class="block w-full mt-1" type="text" wire:model='paystackMerchantEmail'/>
                    <x-input-error for="paystackMerchantEmail" class="mt-2"/>
                </div>

            @else
                <div class="grid grid-cols-2 gap-x-4">
                    <div>
                        <x-label for="testPaystackKey" value="Test Paystack KEY" required/>
                        <x-input id="testPaystackKey" class="block w-full mt-1" type="text" wire:model='testPaystackKey'/>
                        <x-input-error for="testPaystackKey" class="mt-2"/>
                    </div>

                    <div>
                        <x-label for="testPaystackSecret" value="Test Paystack SECRET" required/>
                        <x-input-password id="testPaystackSecret" class="block w-full mt-1" type="text" wire:model='testPaystackSecret'/>
                        <x-input-error for="testPaystackSecret" class="mt-2"/>
                    </div>
                </div>

                <div>
                    <x-label for="testPaystackMerchantEmail" value="Test Paystack Merchant Email" required/>
                    <x-input id="testPaystackMerchantEmail" class="block w-full mt-1" type="text" wire:model='testPaystackMerchantEmail'/>
                    <x-input-error for="testPaystackMerchantEmail" class="mt-2"/>
                </div>
            @endif

            <div class="mt-4">
                <x-label value="Webhook URL" class="mb-1"/>
                <div class="flex items-center">
                    <x-input id="webhook-url" class="block w-full" type="text" value="{{ $webhookUrl }}" readonly/>
                    <button id="copy-button" type="button" onclick="copyWebhookUrl()" class="px-3 py-2 ml-2 text-white bg-gray-800 rounded-lg hover:bg-gray-700">
                        Copy
                    </button>
                </div>
            </div>
        @endif

        <div>
            <x-button>@lang('app.save')</x-button>
        </div>
    </div>
</form>

    @endif

    @if($activePaymentSetting == 'payfast')
        <form wire:submit="submitFormPayfast">
            <div class="grid gap-6">

                <div class="my-3">
                    <x-label for="payfastStatus">
                        <div class="flex items-center cursor-pointer">
                            <x-checkbox name="payfastStatus" id="payfastStatus" wire:model.live='payfastStatus' />
                            <div class="ms-2">
                                @lang('modules.settings.enablePayfast')
                            </div>
                        </div>
                    </x-label>
                </div>

            @if ($payfastStatus)
                <div>
                    <x-label for="payfastMode" :value="__('modules.settings.selectEnvironment')" required/>
                    <x-select id="payfastMode" class="block w-full mt-1" wire:model.live="payfastMode">
                        <option value="test">@lang('app.test')</option>
                        <option value="live">@lang('app.live')</option>
                    </x-select>
                    <x-input-error for="payfastMode" class="mt-2"/>
                </div>

                @if ($payfastMode == 'live')
                    <div class="grid grid-cols-2 gap-x-4">
                        <div>
                            <x-label for="payfastMerchantId" value="Payfast Live ID" required/>
                            <x-input id="payfastMerchantId" class="block w-full mt-1" type="text" wire:model='payfastMerchantId'/>
                            <x-input-error for="payfastMerchantId" class="mt-2"/>
                        </div>

                        <div>
                            <x-label for="payfastMerchantKey" value="Payfast Live KEY" required/>
                            <x-input id="payfastMerchantKey" class="block w-full mt-1" type="text" wire:model='payfastMerchantKey'/>
                            <x-input-error for="payfastMerchantKey" class="mt-2"/>
                        </div>

                        <div>
                            <x-label for="payfastPassphrase" value="Payfast Live PASSPHARASE" required/>
                            <x-input id="payfastPassphrase" class="block w-full mt-1" type="text" wire:model='payfastPassphrase'/>
                            <x-input-error for="payfastPassphrase" class="mt-2"/>
                        </div>

                    </div>



                @else
                    <div class="grid grid-cols-2 gap-x-4">
                        <div>
                            <x-label for="testPayfastMerchantId" value="Test Payfast ID" required/>
                            <x-input id="testPayfastMerchantId" class="block w-full mt-1" type="text" wire:model='testPayfastMerchantId'/>
                            <x-input-error for="testPayfastMerchantId" class="mt-2"/>
                        </div>

                        <div>
                            <x-label for="testPayfastMerchantKey" value="Test Payfast KEY" required/>
                            <x-input-password id="testPayfastMerchantKey" class="block w-full mt-1" type="text" wire:model='testPayfastMerchantKey'/>
                            <x-input-error for="testPayfastMerchantKey" class="mt-2"/>
                        </div>
                    </div>

                    <div>
                        <x-label for="testPayfastPassphrase" value="Test Payfast PASSPHARASE" required/>
                        <x-input id="testPayfastPassphrase" class="block w-full mt-1" type="text" wire:model='testPayfastPassphrase'/>
                        <x-input-error for="testPayfastPassphrase" class="mt-2"/>
                    </div>
                @endif

            @endif

                <div>
                    <x-button>@lang('app.save')</x-button>
                </div>
            </div>
        </form>
    @endif

        <!-- Flutterwave Form -->
        @if($activePaymentSetting == 'flutterwave')
            <form wire:submit="submitFormFlutterWave">
                <div class="grid gap-6">

                    <div class="my-3">
                        <x-label for="flutterwaveStatus">
                            <div class="flex items-center cursor-pointer">
                                <x-checkbox name="flutterwaveStatus" id="flutterwaveStatus" wire:model.live='flutterwaveStatus' />

                                    <div class="ms-2">
                                        @lang('modules.settings.enableFlutterwave')
                                    </div>
                            </div>
                        </x-label>
                    </div>

                    @if ($flutterwaveStatus)
                        <div>
                            <x-label for="flutterwaveMode" :value="__('modules.settings.selectEnvironment')" required/>
                            <x-select id="flutterwaveMode" class="block w-full mt-1" wire:model.live="flutterwaveMode">
                                <option value="test">@lang('app.test')</option>
                                <option value="live">@lang('app.live')</option>
                            </x-select>
                            <x-input-error for="flutterwaveMode" class="mt-2"/>
                        </div>

                        @if ($flutterwaveMode == 'live')
                            <div class="grid grid-cols-2 gap-x-4">
                                <div>
                                    <x-label for="liveFlutterwaveKey" value="Flutterwave Live KEY" required/>
                                    <x-input id="liveFlutterwaveKey" class="block w-full mt-1" type="text" wire:model='liveFlutterwaveKey'/>
                                    <x-input-error for="liveFlutterwaveKey" class="mt-2"/>
                                </div>

                                <div>
                                    <x-label for="liveFlutterwaveSecret" value="Flutterwave Live SECRET" required/>
                                    <x-input-password id="liveFlutterwaveSecret" class="block w-full mt-1" type="text" wire:model='liveFlutterwaveSecret'/>
                                    <x-input-error for="liveFlutterwaveSecret" class="mt-2"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4">
                                <div>
                                    <x-label for="liveFlutterwaveHash" value="Flutterwave Live Hash" required/>
                                    <x-input-password id="liveFlutterwaveHash" class="block w-full mt-1" type="text" wire:model='liveFlutterwaveHash'/>
                                    <x-input-error for="liveFlutterwaveHash" class="mt-2"/>
                                </div>
                                <div>
                                    <x-label for="flutterwaveWebhookSecretHash" value="Webhook Secret Hash"/>
                                    <x-input-password id="flutterwaveWebhookSecretHash" class="block w-full mt-1" type="text" wire:model='flutterwaveWebhookSecretHash'/>
                                    <x-input-error for="flutterwaveWebhookSecretHash" class="mt-2"/>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-x-4">
                                <div>
                                    <x-label for="testFlutterwaveKey" value="Test Flutterwave KEY" required/>
                                    <x-input id="testFlutterwaveKey" class="block w-full mt-1" type="text" wire:model='testFlutterwaveKey'/>
                                    <x-input-error for="testFlutterwaveKey" class="mt-2"/>
                                </div>

                                <div>
                                    <x-label for="testFlutterwaveSecret" value="Test Flutterwave SECRET" required/>
                                    <x-input-password id="testFlutterwaveSecret" class="block w-full mt-1" type="text" wire:model='testFlutterwaveSecret'/>
                                    <x-input-error for="testFlutterwaveSecret" class="mt-2"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4">
                                <div>
                                    <x-label for="testFlutterwaveHash" value="Test Flutterwave Hash" required/>
                                    <x-input-password id="testFlutterwaveHash" class="block w-full mt-1" type="text" wire:model='testFlutterwaveHash'/>
                                    <x-input-error for="testFlutterwaveHash" class="mt-2"/>
                                </div>
                                <div>
                                    <x-label for="flutterwaveWebhookSecretHash" value="Webhook Secret Hash"/>
                                    <x-input-password id="flutterwaveWebhookSecretHash" class="block w-full mt-1" type="text" wire:model='flutterwaveWebhookSecretHash'/>
                                    <x-input-error for="flutterwaveWebhookSecretHash" class="mt-2"/>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4">
                            <x-label value="Webhook URL" class="mb-1"/>
                            <div class="flex items-center">
                                <!-- Webhook URL Input -->
                                <x-input id="webhook-url" class="block w-full" type="text" value="{{ $webhookUrl }}" readonly/>
                                <button id="copy-button" type="button" onclick="copyWebhookUrl()" class="px-3 py-2 ml-2 text-white bg-gray-800 rounded-lg hover:bg-gray-700">
                                    Copy
                                </button>
                            </div>
                        </div>

                    @endif

                    <div>
                        <x-button>@lang('app.save')</x-button>
                    </div>
                </div>
            </form>
        @endif


        <!--Paypal Form-->
        @if ($activePaymentSetting == 'paypal')
        <form wire:submit.prevent="submitFormPaypal">
            <div class="grid gap-6">

                <div class="my-3">
                    <x-label for="paypalStatus">
                        <div class="flex items-center cursor-pointer">
                            <x-checkbox name="paypalStatus" id="paypalStatus" wire:model.live="paypalStatus" />
                            <div class="ms-2">
                                @lang('modules.settings.enablePayPal')
                            </div>
                        </div>
                    </x-label>
                </div>

                @if ($paypalStatus)
                    <div>
                        <x-label for="paypalMode" :value="__('modules.settings.selectEnvironment')" required />
                        <x-select id="paypalMode" class="block w-full mt-1" wire:model.live="paypalMode">
                            <option value="sandbox">@lang('app.test')</option>
                            <option value="live">@lang('app.live')</option>
                        </x-select>
                        <x-input-error for="paypalMode" class="mt-2" />
                    </div>

                    @if ($paypalMode === 'live')
                        <div class="grid grid-cols-2 gap-x-4">
                            <div>
                                <x-label for="livePaypalClientId" value="PayPal Live Client ID" required />
                                <x-input id="livePaypalClientId" class="block w-full mt-1" type="text" wire:model.live='livePaypalClientId' />
                                <x-input-error for="livePaypalClientId" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="livePaypalSecret" value="PayPal Live Secret" required />
                                <x-input-password id="livePaypalSecret" class="block w-full mt-1" type="text" wire:model.live='livePaypalSecret' />
                                <x-input-error for="livePaypalSecret" class="mt-2" />
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-x-4">
                            <div>
                                <x-label for="sandboxPaypalClientId" value="PayPal Sandbox Client ID" required />
                                <x-input id="sandboxPaypalClientId" class="block w-full mt-1" type="text" wire:model.live='sandboxPaypalClientId' />
                                <x-input-error for="sandboxPaypalClientId" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="sandboxPaypalSecret" value="PayPal Sandbox Secret" required />
                                <x-input-password id="sandboxPaypalSecret" class="block w-full mt-1" type="text" wire:model.live='sandboxPaypalSecret' />
                                <x-input-error for="sandboxPaypalSecret" class="mt-2" />
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <x-label value="Webhook URL" class="mb-1" />
                        <div class="flex items-center">
                            <x-input id="webhook-url" class="block w-full" type="text" value="{{ $webhookUrl }}" readonly />
                            <button id="copy-button" type="button" onclick="copyWebhookUrl()" class="px-3 py-2 ml-2 text-white bg-gray-800 rounded-lg hover:bg-gray-700">
                                Copy
                            </button>
                        </div>

                    </div>
                @endif

                <div>
                    <x-button>@lang('app.save')</x-button>
                </div>
            </div>
        </form>
    @endif
        <!-- Offline Form -->
        @if ($activePaymentSetting == 'offline')
            <form wire:submit="submitFormOffline" class="mt-4">
                <div class="space-y-6">
                    <div class="flex items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700
                        @if(!$stripeStatus && !$razorpayStatus && !$flutterwaveStatus && !$paypalStatus && !$paystackStatus && !$payfastStatus) opacity-50 cursor-not-allowed @endif">

                        <div class="flex items-center space-x-2">
                            <x-checkbox
                                name="enablePayViaCash"
                                id="enablePayViaCash"
                                value="cash"
                                wire:model.live="enablePayViaCash"
                                :disabled="!$stripeStatus && !$razorpayStatus && !$flutterwaveStatus && !$paypalStatus && !$paystackStatus && !$payfastStatus"
                            />
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    @lang('modules.settings.enablePayViaCash')
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @lang('modules.settings.cashPaymentDescription')
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="flex justify-start">
                        <x-button class="gap-x-2">
                            @lang('app.save')
                        </x-button>
                    </div>
                </div>
            </form>
        @endif

    </div>
    <script>
        function copyWebhookUrl() {
            let webhookUrl = document.getElementById("webhook-url").value;
            let copyButton = document.getElementById("copy-button");

            // Create a temporary textarea element
            let tempTextArea = document.createElement("textarea");
            tempTextArea.value = webhookUrl;
            document.body.appendChild(tempTextArea);

            // Select and copy the text
            tempTextArea.select();
            tempTextArea.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");

            // Remove the temporary textarea
            document.body.removeChild(tempTextArea);

            // Change button text to "Copied!"
            copyButton.innerText = "Copied!";

            // Revert text back to original after 2 seconds
            setTimeout(() => {
                copyButton.innerText = "Copy";
            }, 2000);
        }


    </script>
</div>
