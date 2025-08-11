<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumReply extends Model
{
    use HasFactory;
    protected $table = 'forum_replies';
    protected $fillable = ['forum_id', 'user_id', 'reply', 'parent_reply_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_reply_id');
    }
}
