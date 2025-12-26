<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    // Types
    public const TYPE_EMAIL = 'email';
    public const TYPE_SMS = 'sms';
    public const TYPE_PUSH = 'push';

    public const TYPE_LIST = [
        self::TYPE_EMAIL,
        self::TYPE_SMS,
        self::TYPE_PUSH,
    ];

    // Defaults
    public const DEFAULT_TYPE = self::TYPE_EMAIL;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
