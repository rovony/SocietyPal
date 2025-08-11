<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSociety;

class Forum extends Model
{
    use HasFactory, HasSociety;

    protected $fillable = [
        'society_id',
        'category_id',
        'title',
        'description',
        'file',
        'discussion_type',
        'created_by',
        'date',
        'user_selection_type',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function category()
    {
        return $this->belongsTo(SocietyForumCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'forum_user')->withTimestamps();
    }
    
    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'forum_likes')->withTimestamps();
    }

    public function isLikedBy($user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function files()
    {
        return $this->hasMany(ForumFile::class);
    }

}
