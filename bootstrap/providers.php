<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,

    Froiden\LaravelInstaller\Providers\LaravelInstallerServiceProvider::class,
    App\Providers\CustomConfigProvider::class,
    App\Providers\FileStorageCustomConfigProvider::class,
    App\Providers\CustomizationServiceProvider::class,
];
