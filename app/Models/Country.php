<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    use HasFactory;

    protected $fillable = [
      'countries_code',
        'country_name',
        'phonecode',
        'continent',
        'capital',
    ];

    public function flag()
    {
        return $this->hasOne(Flag::class, 'name', 'countries_name'); 
    }

    public function flagUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return asset('flags/1x1/' . strtolower($this->countries_code) . '.svg');
        });
    }
}

