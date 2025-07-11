<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyPhoneNotification;

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
        'password',
        'phone',
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's phone number as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the phone verification notification.
     *
     * @return void
     */
    public function sendPhoneVerificationNotification(): void
    {
        $this->notify(new VerifyPhoneNotification);
    }

    /**
     * Get the phone number that should be used for verification.
     *
     * @return string
     */
    public function getPhoneForVerification(): string
    {
        return $this->phone;
    }

    /**
     * Get the route key name for phone verification.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the phone number that should be used for password reset.
     *
     * @return string
     */
    public function getPhoneForPasswordReset(): string
    {
        return $this->phone;
    }

    /**
     * Get the username attribute for authentication.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'phone';
    }

    /**
     * Get the unique identifier for the user (phone number).
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->phone;
    }

    /**
     * Find user by phone number for authentication.
     *
     * @param string $phone
     * @return \App\Models\User|null
     */
    public static function findForAuth(string $phone): ?self
    {
        return static::where('phone', $phone)->first();
    }
}