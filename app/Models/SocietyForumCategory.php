<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSociety;
use App\Models\Forum;

class SocietyForumCategory extends Model
{
    use HasFactory, HasSociety;
    protected $table = 'society_forum_category';

    protected $fillable = [
        'society_id',
        'name',
        'icon',
        'image',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function forums()
    {
        return $this->hasMany(Forum::class, 'category_id');
    }
}
