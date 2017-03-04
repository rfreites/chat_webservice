<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

	public $primaryKey = 'id';

	protected $table = 'users';

	public static $rules = [
				'name' => 'required',
				'email' => 'unique:users|email|required',
				'password' => 'required',
			];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
	    'created_at',
	    'updated_at',
	    'deleted_at',
    ];

	public function chats()
	{
		return $this->hasMany(Chat::class);
	}

	public function messages()
	{
		return $this->hasMany(Message::class, 'user_id', 'id');
	}

	public function getCreatedAtAttribute($date, $fromFormat = 'Y-m-d H:i:s', $toFormat = \DateTime::ATOM)
	{
		return date_format(date_create_from_format($fromFormat, $date), $toFormat);
	}
}
