<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
   @php
        $lastSegment = last(request()->segments());
    @endphp
    @if (user()->society_id)
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}&hash={{ user()->society->hash }}@endif" crossorigin="use-credentials">
    @else
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}@endif" crossorigin="use-credentials">
    @endif
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="{{ global_setting()->name }}">
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

    <title>{{ global_setting()->name }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    @stack('styles')

    @include('sections.theme_style', ['baseColor' => societyOrGlobalSetting()->theme_rgb, 'baseColorHex' => societyOrGlobalSetting()->theme_hex])

    @if (File::exists(public_path() . '/css/app-custom.css'))
        <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
    @endif


    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }

    </script>

    <script>
        if (localStorage.getItem("menu-collapsed") === "true") {
            document.documentElement.style.visibility = 'hidden';
            window.addEventListener('DOMContentLoaded', () => {
                const sidebar = document.getElementById('sidebar');
                const openIcon = document.getElementById('toggle-sidebar-open');
                const closeIcon = document.getElementById('toggle-sidebar-close');

                if (sidebar) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex', 'lg:flex');
                }

                if (openIcon && closeIcon) {
                    openIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }

                setTimeout(() => {
                    document.documentElement.style.visibility = 'visible';
                }, 50);
            });
        } else {
            // Handle expanded state icons without hiding the page
            window.addEventListener('DOMContentLoaded', () => {
                const openIcon = document.getElementById('toggle-sidebar-open');
                const closeIcon = document.getElementById('toggle-sidebar-close');

                if (openIcon && closeIcon) {
                    openIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });
        }
    </script>

</head>

<body class="font-sans antialiased dark:bg-gray-900" id="main-body">

    @if (user()->society_id)
        @livewire('navigation-menu')
    @else
        @livewire('superadmin-navigation-menu')
    @endif

    <div class="flex h-screen pt-16 overflow-hidden rtl:flex-row-reverse bg-gray-50 dark:bg-gray-900">

        @if (user()->society_id)
            @livewire('sidebar')
        @else
            @livewire('superadmin-sidebar')
        @endif


        <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 ltr:lg:ml-64 rtl:lg:mr-64 dark:bg-gray-900">
            <main>
                @yield('content')

                {{ $slot ?? ''}}

            </main>


        </div>


    </div>

    @stack('modals')


    @livewireScripts


    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}" defer data-navigate-track></script>
    <x-livewire-alert::flash />

    @if (user()->society_id)
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        @livewire('settings.upgradeLicense')

        <script src="https://js.stripe.com/v3/"></script>

        <form action="{{ route('stripe.license_payment') }}" method="POST" id="license-payment-form" class="hidden">
            @csrf

            <input type="hidden" id="license_payment" name="license_payment">
            <input type="hidden" id="package_type" name="package_type">
            <input type="hidden" id="package_id" name="package_id">
            <input type="hidden" id="currency_id" name="currency_id">

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

        @if (superadminPaymentGateway()->stripe_status)
            <script>
                const stripe = Stripe('{{ superadminPaymentGateway()->stripe_key }}');
                const elements = stripe.elements({
                    currency: '{{ strtolower(society()->currency->currency_code) }}',
                });
            </script>
        @endif

        <script src="https://checkout.flutterwave.com/v3.js"></script>
        <form action="{{ route('flutterwave.initiate-payment') }}" method="POST" id="flutterwavePaymentformNew" class="hidden">
            @csrf
            <input type="hidden" name="payment_id">
            <input type="hidden" name="amount">
            <input type="hidden" name="currency">
            <input type="hidden" name="society_id">
            <input type="hidden" name="package_id">
            <input type="hidden" name="package_type">
        </form>

        <script src="https://js.paystack.co/v1/inline.js"></script>
        <form action="{{ route('paystack.initiate-payment') }}" method="POST" id="paystackPaymentformNew" class="hidden">
            @csrf
            <input type="hidden" name="payment_id">
            <input type="hidden" name="amount">
            <input type="hidden" name="currency">
            <input type="hidden" name="society_id">
            <input type="hidden" name="package_id">
            <input type="hidden" name="package_type">
            <input type="hidden" name="email">
        </form>

        @if (superadminPaymentGateway()->paypal_status)
            <script src="https://www.paypal.com/sdk/js?client-id={{ superadminPaymentGateway()->paypal_client_id }}&currency={{ society()->currency->currency_code }}"></script>
        @endif
        <form action="{{ route('paypal.initiate-payment') }}" method="POST" id="paypalPaymentForm" class="hidden">
            @csrf
            <input type="hidden" name="payment_id">
            <input type="hidden" name="amount">
            <input type="hidden" name="currency">
            <input type="hidden" name="society_id">
            <input type="hidden" name="package_id">
            <input type="hidden" name="package_type">
        </form>

        <form action="{{ route('payfast.initiate-payment') }}" method="POST" id="payfastPaymentForm" class="hidden">
            @csrf
            <input type="hidden" name="payment_id">
            <input type="hidden" name="amount">
            <input type="hidden" name="currency">
            <input type="hidden" name="society_id">
            <input type="hidden" name="package_id">
            <input type="hidden" name="package_type">
        </form>

    @endif


    <script>
        var elem = document.getElementById("main-body");
        function openFullscreen() {
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    /* Safari */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    /* IE11 */
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    /* Safari */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    /* IE11 */
                    document.msExitFullscreen();
                }
            }
        }
    </script>

<script>
        function hideNotificationIfResponded() {
            const permission = Notification.permission;
            if (permission === 'granted' || permission === 'denied') {
                const alertBox = document.getElementById('notification-alert');
                if (alertBox) {
                    alertBox.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            hideNotificationIfResponded();
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
                    .then(registration => console.log("Service Worker registered:", registration))
                    .catch(error => console.error("Service Worker registration failed:", error));
            }
        });

        document.addEventListener('livewire:navigated', () => {
            hideNotificationIfResponded();
        });

        document.addEventListener('click', async (e) => {
            if (e.target && e.target.id === 'subscribe-button') {
                if ('Notification' in window && 'serviceWorker' in navigator) {
                    const permission = await Notification.requestPermission();

                    localStorage.setItem('notificationPermission', permission);

                    hideNotificationIfResponded();

                    if (permission !== 'granted') {
                        console.warn("Push notifications permission denied.");
                        return;
                    }
                    try {
                        const registration = await navigator.serviceWorker.register("{{ asset('service-worker.js') }}");
                        console.log("Service Worker registered:", registration);
                        subscribeUserToPush(registration);
                    } catch (error) {
                        console.error("Service Worker registration failed:", error);
                    }
                } else if ('safari' in window && 'pushNotification' in window.safari) {
                    handleSafariPush();
                } else {
                    console.error("Push notifications are not supported in this browser.");
                }
            }
        });
        async function subscribeUserToPush(registration) {
            try {
                const applicationServerKey = "{{ global_setting()->vapid_public_key }}";

                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                console.log("Push Subscription:", subscription);

                await fetch("/subscribe", {
                    method: "POST",
                    body: JSON.stringify(subscription),
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                console.log("Push subscription saved on the server.");
            } catch (error) {
                console.error("Subscription error:", error);
            }
        }

        function handleSafariPush() {
            const permissionData = window.safari.pushNotification.permission("{{ config('app.safari_push_id') }}");

            if (permissionData.permission === "default") {
                window.safari.pushNotification.requestPermission(
                    "https://yourdomain.com",
                    "{{ config('app.safari_push_id') }}",
                    {},
                    (permission) => {
                        localStorage.setItem('notificationPermission', permission.permission);
                        hideNotificationIfResponded();
                        console.log("Safari push permission:", permission);
                    }
                );
            } else {
                localStorage.setItem('notificationPermission', permissionData.permission);
                hideNotificationIfResponded();
                console.log("Safari push subscription:", permissionData.deviceToken);
            }
        }
    </script>


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

    @include('layouts.service-worker-js')
    @stack('scripts')
</body>

</html>
