<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    // Tambahkan di dalam class User
// Tambahkan di app/Models/User.php

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->latest();
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')
            ->latest();
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()
            ->unread();
    }

    // Optimized accessor dengan caching
    public function getUnreadCountAttribute()
    {
        // Cache untuk 30 detik
        return cache()->remember(
            'user.'. $this->id .'.unread_count',
            30,
            fn() => $this->unreadMessages()->count()
        );
    }

    // Clear cache setelah message dibaca
    protected static function booted()
    {
        static::updated(function ($user) {
            cache()->forget('user.'. $user->id .'.unread_count');
        });
    }

}
