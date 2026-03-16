<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Notifications\SystemAnnouncement;
use Spatie\Translatable\HasTranslations;

class Announcement extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'body',
        'target_type',
        'target_role',
        'targeted_users_ids',
        'send_via_email',
        'status',
        'published_at',
    ];

    public array $translatable = ['title', 'body'];

    protected $casts = [
        'targeted_users_ids' => 'array',
        'send_via_email' => 'boolean',
        'status' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($announcement) {
            if ($announcement->isDirty('status') && $announcement->status === 1) {
                $announcement->published_at = now();
            }
        });

        static::saved(function ($announcement) {
            if ($announcement->wasChanged('status') && $announcement->status === 1) {
                \Illuminate\Support\Facades\Bus::chain([
                    function () use ($announcement) {
                        $users = collect();
                        if ($announcement->target_type === 'all') {
                            $users = User::whereNotNull('email_verified_at')->get();
                        } elseif ($announcement->target_type === 'role') {
                            if ($announcement->target_role === 'App\Models\Donor') {
                                $users = User::whereHas('donor')->whereNotNull('email_verified_at')->get();
                            } elseif ($announcement->target_role === 'App\Models\Organization') {
                                $users = User::whereHas('organization')->whereNotNull('email_verified_at')->get();
                            }
                        } elseif ($announcement->target_type === 'specific_users') {
                            $ids = $announcement->targeted_users_ids ?? [];
                            $users = User::whereIn('id', $ids)->whereNotNull('email_verified_at')->get();
                        }

                        \Illuminate\Support\Facades\Notification::send($users, new SystemAnnouncement($announcement));
                    }
                ])->dispatch();
            }
        });
    }
}
