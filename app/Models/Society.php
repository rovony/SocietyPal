<?php

namespace App\Models;

use App\Models\Tower;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Laravel\Cashier\Billable;

class Society extends Model
{
    use HasFactory, Billable;

    protected $guarded = ['id'];

    const ABOUT_US_DEFAULT_TEXT = '<p class="text-lg text-gray-600 mb-6">
                Welcome to our society, where community, comfort, and security come together! We strive to create a warm and
                well-managed living environment where every resident feels at home. Whether you’re a homeowner, tenant, or visitor,
                we’re dedicated to making your experience smooth and enjoyable.
            </p>
            <p class="text-lg text-gray-600 mb-6">
                Our society is built on values of cooperation and excellence. From well-maintained amenities to seamless management
                services, we work hard to ensure that daily life here is peaceful, convenient, and fulfilling. Our goal is to make
                community living as effortless as possible.
            </p>
            <p class="text-lg text-gray-600 mb-6">
                But we’re not just about management—we’re about people. We foster a friendly, connected neighborhood where
                residents can engage, socialize, and support one another. Our team is always here to assist, ensuring a safe,
                welcoming, and vibrant community.
            </p>
            <p class="text-lg text-gray-600">
                So, whether you’re settling in, visiting, or just passing by, we invite you to experience the warmth of our society.
                Together, we build more than homes—we build lasting relationships.
            </p>
            <p class="text-lg text-gray-800 font-semibold mt-6">Welcome home! 🏡✨</p>';

    const FAVICON_BASE_PATH_SOCIETY = 'favicons/society/';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $appends = [
        'logo_url',
    ];


    // This comes from the FaviconTrait and is overridden in the here model
    public function getFaviconBasePath(): string
    {
        return self::FAVICON_BASE_PATH_SOCIETY . $this->hash . '/';
    }

    protected $casts = [
        'license_expire_on' => 'datetime',
        'trial_expire_on' => 'datetime',
        'license_updated_at' => 'datetime',
        'subscription_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function logoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->logo ? asset_url_local_s3('logo/' . $this->logo) : global_setting()->logoUrl;
        });
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function towers()
    {
        return $this->hasMany(Tower::class);
    }

    public function paymentGateways(): HasOne
    {
        return $this->hasOne(PaymentGatewayCredential::class)->withoutGlobalScopes();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function societyPayment(): HasMany
    {
        return $this->hasMany(SocietyPayment::class)->where('status', 'paid')->orderByDesc('id');
    }

    public function currentInvoice(): HasOne
    {
        return $this->hasOne(GlobalInvoice::class, 'society_id')->latest();
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public static function societyAdmin($society)
    {
        return $society->users()->orderBy('id')->first();
    }


    /**
     * Get URL for Android Chrome 192x192 favicon
     * Returns society's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAndroidChrome192Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom favicon if exists, otherwise use global setting
            return $this->upload_fav_icon_android_chrome_192
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_192)
                : global_setting()->upload_fav_icon_android_chrome_192_url;
        });
    }

    /**
     * Get URL for Android Chrome 512x512 favicon
     * Returns society's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAndroidChrome512Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom favicon if exists, otherwise use global setting
            return $this->upload_fav_icon_android_chrome_512
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_512)
                : global_setting()->upload_fav_icon_android_chrome_512_url;
        });
    }

    /**
     * Get URL for Apple Touch Icon (180x180)
     * Returns society's custom icon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAppleTouchIconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom icon if exists, otherwise use global setting
            return $this->upload_fav_icon_apple_touch_icon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_apple_touch_icon)
                : global_setting()->upload_fav_icon_apple_touch_icon_url;
        });
    }

    /**
     * Get URL for 16x16 favicon
     * Returns society's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIcon16Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom favicon if exists, otherwise use global setting
            return $this->upload_favicon_16
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_16)
                : global_setting()->upload_fav_icon_16_url;
        });
    }

    /**
     * Get URL for 32x32 favicon
     * Returns society's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIcon32Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom favicon if exists, otherwise use global setting
            return $this->upload_favicon_32
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_32)
                : global_setting()->upload_fav_icon_32_url;
        });
    }

    /**
     * Get URL for main favicon.ico file
     * Returns society's custom favicon if available, otherwise falls back to global setting
     */
    public function faviconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom favicon if exists, otherwise use global setting
            return $this->favicon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->favicon)
                : global_setting()->favicon_url;
        });
    }

    /**
     * Get URL for webmanifest file (used for PWA support)
     * Returns society's custom webmanifest if available, otherwise falls back to global setting
     */
    public function webmanifestUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use society's custom webmanifest if exists, otherwise use global setting
            return $this->webmanifest
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->webmanifest)
                : global_setting()->webmanifest_url;
        });
    }
}
