<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_id',
        'file',
    ];

    protected $appends = [
        'file_url',
    ];

    const FILE_PATH = 'forum-files';

    public function getFileUrlAttribute()
    {
        if ($this->file) {
            return asset_url_local_s3(ForumFile::FILE_PATH . '/' . $this->file);
        }
        return null;
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }
}
