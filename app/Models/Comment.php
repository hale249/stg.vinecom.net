<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Comment extends Model
{
    use GlobalStatus;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function allReplies()
    {
        return $this->hasMany(Comment::class, 'comment_id', 'id')->with('replies');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'comment_id', 'id')->with('replies')->where('status', Status::ENABLE);
    }

    public function scopeComment($query)
    {
        return $query->where('comment_id', null);
    }

    public function seenBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->seen == Status::YES) {
                $html = '<span class="badge badge--status badge--success">' . trans('Seen') . '</span>';
            } else {
                $html = '<span><span class="badge badge--status badge--warning">' . trans('Unseen');
            };
            return $html;
        });
    }
}
