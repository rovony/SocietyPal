<?php

namespace App\Models;

use Carbon\Language;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class FrontFeature extends Model
{
    protected $table = 'front_features';

    protected $guarded = ['id'];

    protected $fillable = ['language_setting_id', 'title', 'description', 'type', 'icon', 'image']; // Ensure 'image' is fillable

    protected $appends = [
        'image_url',
    ];

    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->type === 'image') {
                if ($this->image) {
                    return asset_url_local_s3('front_feature/' . $this->image);
                }

                // Use translated title to match default keys
                $locale = $this->language?->language_code ?? app()->getLocale();

                $translatedTitles = [
                    Lang::get('landing.featureTitle1', [], $locale) => asset('landing/tenant.png'),
                Lang::get('landing.featureTitle2', [], $locale) => asset('landing/service-provider.png'),
                Lang::get('landing.featureTitle3', [], $locale) => asset('landing/book-amenity.png'),
                ];

                return $translatedTitles[$this->title] ?? '';
            }

            return '';
        });
    }

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }
}
