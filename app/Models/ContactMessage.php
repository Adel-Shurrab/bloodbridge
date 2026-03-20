<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactMessage extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'read_at',
    ];

    public array $translatable = ['name', 'email', 'subject', 'message'];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
