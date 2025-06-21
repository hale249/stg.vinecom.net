<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'sender',
        'sent_from',
        'sent_to',
        'subject',
        'message',
        'notification_type',
        'image',
        'user_read'
    ];

    protected $casts = [
        'user_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
