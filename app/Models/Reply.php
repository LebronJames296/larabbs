<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }

}
