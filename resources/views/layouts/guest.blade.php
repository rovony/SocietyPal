<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
    <link rel="manifest" href="{{ url('manifest.json') }}?url={{ urlencode(ltrim(Request::getRequestUri(), '/')) }}&hash={{ $society->hash }}" crossorigin="use-credentials">
    <meta name="theme-color" content="#ffffff">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ societyOrGlobalSetting()->upload_fav_icon_apple_touch_icon_url }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ societyOrGlobalSetting()->upload_fav_icon_android_chrome_192_url }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ societyOrGlobalSetting()->upload_fav_icon_android_chrome_512_url }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ societyOrGlobalSetting()->upload_favicon_16_url }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ societyOrGlobalSetting()->upload_favicon_32_url }}">
    <link rel="shortcut icon" href="{{ societyOrGlobalSetting()->favicon_url }}">
    <link rel="manifest" href="{{ societyOrGlobalSetting()->webmanifest_url }}">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ societyOrGlobalSetting()->logoUrl }}">
    @if ($society->meta_keyword)
        <meta name="keyword" content="{{ $society->meta_keyword ?? '' }}">
    @endif
        <meta name="description" content="{{ $society->meta_description ?? $society->name }}">
    <title>{{ $society->name }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        :root {
            /* --color-base: 219, 41, 41; */
            --color-base: {{ $society->theme_rgb }};
            --livewire-progress-bar-color: {{ $society->theme_hex }};
        }
    </style>

    @if (File::exists(public_path() . '/css/app-custom.css'))
        <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
    @endif



</head>

<body class="font-sans antialiased dark:bg-gray-900">

    <div class="mx-auto max-w-lg lg:max-w-screen-xl min-h-svh shadow-md lg:shadow-none">
        <div class="flex mt-4 overflow-hidden  dark:bg-gray-900">
            <div id="main-content" class="w-full h-full overflow-y-auto dark:bg-gray-900">
                <main>
                    @yield('content')

                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>

    </div>
    @stack('modals')

    <footer class="p-4 bg-white sm:p-6 dark:bg-gray-800 border-t ">
        <div class="mx-auto max-w-screen-xl">
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">&copy; {{ now()->year }} <a
                        href="" class="hover:underline">{{ $society->name }}</a>. @lang('app.allRightsReserved')
                </span>
                <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0 rtl:space-x-reverse">
                    @if (languages()->count() > 1)
                        @livewire('shop.languageSwitcher')
                    @endif

                    @if ($society->facebook_link)
                        <a href="{{ $society->facebook_link }}"
                            class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                    @if ($society->instagram_link)
                        <a href="{{ $society->instagram_link }}"
                            class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                    @if ($society->twitter_link)
                        <a href="{{ $society->twitter_link }}"
                            class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 50 50 " aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100"
                                viewBox="0 0 50 50">
                                <path
                                    d="M 6.9199219 6 L 21.136719 26.726562 L 6.2285156 44 L 9.40625 44 L 22.544922 28.777344 L 32.986328 44 L 43 44 L 28.123047 22.3125 L 42.203125 6 L 39.027344 6 L 26.716797 20.261719 L 16.933594 6 L 6.9199219 6 z">
                                </path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>


    @livewireScripts


    @include('layouts.update-uri')
    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}" defer data-navigate-track></script>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script src="https://js.stripe.com/v3/"></script>


    <form action="{{ route('stripe.maintenance_payment') }}" method="POST" id="order-payment-form" class="hidden">
        @csrf

        <input type="hidden" id="order_payment" name="order_payment">

        <div class="form-row">
            <label for="card-element">
                Credit or debit card
            </label>
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>

            <!-- Used to display Element errors. -->
            <div id="card-errors" role="alert"></div>
        </div>

        <button>Submit Payment</button>
    </form>

    @if (user()->society_id)
        @if (society()->paymentGateways->stripe_status)
        <script>
                const stripe = Stripe('{{ society()->paymentGateways->stripe_key }}');
            const elements = stripe.elements({
                    currency: '{{ strtolower(society()->currency->currency_code) }}',
            });
        </script>
        @endif
    @endif
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('service-worker.js') }}')
                    .then(registration => {
                        console.log('Service Worker registered:', registration);
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
        self.addEventListener("fetch", (event) => {
            if (event.request.mode === "navigate") {
                event.respondWith(
                    fetch(event.request.url)
                );
            }
        });
    </script>

    <script>
        @if ($society->is_pwa_install_alert_show == 1)
                var isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            var isInStandaloneMode = ('standalone' in window.navigator) && window.navigator.standalone;
            var deferredPrompt;

            window.addEventListener("beforeinstallprompt", (event) => {
                event.preventDefault(); // Prevent default install prompt
                deferredPrompt = event; // Store the event for later use

                // Prevent showing again if user has dismissed in this tab
                if (!sessionStorage.getItem("pwaDismissed")) {
                    ['scroll', 'click'].forEach(evt => {
                        window.addEventListener(evt, showInstallPrompt, { once: true });
                    });
                }
            });

            function showInstallPrompt() {
                if (deferredPrompt) {
                    deferredPrompt.prompt(); // Show the install prompt

                    deferredPrompt.userChoice.then(({ outcome }) => {
                        console.log(`User ${outcome === 'accepted' ? 'accepted' : 'dismissed'} the PWA install`);

                        if (outcome === 'dismissed') {
                            sessionStorage.setItem("pwaDismissed", "true"); // Prevent showing again in this session
                        }

                        deferredPrompt = null;
                    });
                }
            }

            // Handle iOS specific add to home screen prompt
            if (isIOS && !isInStandaloneMode) {
                ['scroll', 'click'].forEach(evt => {
                    window.addEventListener(evt, showIOSInstallInstructions, { once: true });
                });
            }

            function showIOSInstallInstructions() {
                if (document.getElementById('iosInstallInstructions')) return;

                const instructions = document.createElement('div');
                instructions.id = 'iosInstallInstructions';
                instructions.innerHTML = `
                    <div style="position: fixed; bottom: 10px; left: 10px; right: 10px; background: #fff; padding:
                 <div style="position: fixed; bottom: 10px; left: 10px; right: 10px; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; text-align: center; z-index: 1000;">
                    <p class="flex relative">@lang('messages.installAppInstruction')
                        <img class="absolute right-0 left-auto mr-5" src="{{ asset('img/share-ios.svg') }}" alt="Share Icon" style="width: 20px; vertical-align: middle;">

                    </p>
                    @lang('messages.addToHomeScreen').
                    <button id="closeInstructions" class="block text-center mx-auto" style="margin-top: 10px; padding: 5px 10px;">@lang('app.close')</button>
                </div>
            `;
            document.body.appendChild(instructions);

            // Add click event to close button
            document.getElementById('closeInstructions').addEventListener('click', function () {
                document.getElementById('iosInstallInstructions').remove();
            });
        }

    @endif

</script>

    <x-livewire-alert::flash />

    @stack('scripts')
</body>
</html>
