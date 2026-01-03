<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    // Types
    public const TYPE_EMAIL = 1;
    public const TYPE_SMS = 2;
    public const TYPE_PUSH = 3;

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_SMS => 'SMS',
            self::TYPE_PUSH => 'Push Notification',
        ];
    }

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
