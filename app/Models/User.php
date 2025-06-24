<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'pending_email',
        'password',
        'role',
        'account_holder_name',
        'company_name',
        'mobile_number',
        'reg_address_line1',
        'reg_address_line2',
        'reg_district',
        'reg_state',
        'reg_country',
        'reg_pincode',
        'bill_address_line1',
        'bill_address_line2',
        'bill_district',
        'bill_state',
        'bill_country',
        'bill_pincode',
        'subscription_end_date',
        'email_otp',
        'email_otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'pending_email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // public function sendEmailVerificationNotification()
    // {
    //     $url = URL::temporarySignedRoute(
    //         'verification.verify',
    //         now()->addMinutes(60),
    //         ['id' => $this->id, 'hash' => sha1($this->pending_email)]
    //     );

    //     $this->notify(new \App\Notifications\VerifyEmailNotification($url));
    // }


      public function sendEmailVerificationNotification()
    {
        // Only send if there's a pending email
        if (!$this->pending_email) {
            return;
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->pending_email),
            ]
        );

        $this->notify(new \App\Notifications\VerifyEmailNotification($verificationUrl));
    }
}