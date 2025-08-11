<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class FrontDetail extends Model
{
    protected $table = 'front_details';

    protected $guarded = ['id'];

    protected $appends = [
        'image_url',
    ];

    public function imageUrl(): Attribute
    {
        return Attribute::get(fn(): string => $this->image ? asset_url_local_s3('header/' . $this->image) : asset('landing/dashboard.png'));

    }
}
