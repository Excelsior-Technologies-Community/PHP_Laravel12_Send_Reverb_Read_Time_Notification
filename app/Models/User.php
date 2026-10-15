<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_notifications',
        'sound_notifications',
        'dnd_enabled',
        'dnd_start_time',
        'dnd_end_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notifications' => 'boolean',
            'sound_notifications' => 'boolean',
            'dnd_enabled' => 'boolean',
        ];
    }

    /**
     * Check if Do Not Disturb (DND) mode is currently active.
     */
    public function isDndActive(): bool
    {
        if (!$this->dnd_enabled) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->dnd_start_time ?: '22:00';
        $end = $this->dnd_end_time ?: '08:00';

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        }

        // Crosses midnight (e.g. 22:00 to 08:00)
        return $now >= $start || $now <= $end;
    }
}
