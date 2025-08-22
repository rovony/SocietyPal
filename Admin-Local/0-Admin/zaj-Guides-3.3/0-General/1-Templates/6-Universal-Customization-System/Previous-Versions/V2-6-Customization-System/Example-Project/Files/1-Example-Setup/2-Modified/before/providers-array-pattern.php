<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,

    // Vendor service providers (e.g., package providers)
    Froiden\LaravelInstaller\Providers\LaravelInstallerServiceProvider::class,
    
    // Existing custom providers
    App\Providers\CustomConfigProvider::class,
    App\Providers\FileStorageCustomConfigProvider::class,
];
