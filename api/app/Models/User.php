<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'user_name',
        'admin_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
