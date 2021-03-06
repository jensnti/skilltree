<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Skilltree;
use App\task;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'teacher', 'email_verified_at', 'password', 'g_token', 'g_exspiresin',  'provider_name', 'provider_id', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'provider_name', 'provider_id', 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //    protected static $recordableEvents = ['invited'];

    public function skilltrees()
    {
        return $this->hasMany(Skilltree::class, 'owner_id')->latest('updated_at');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'owner_id')->with('task'); //->with('task.skill');
    }

    public function accessibleSkilltrees()
    {
        return Skilltree::where('owner_id', $this->id)
            ->orWhereHas('members', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->latest('updated_at')
            ->get();
    }
}
