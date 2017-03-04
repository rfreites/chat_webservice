<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
	public $primaryKey = 'id';

	protected $table = 'chats';

	public static $rules = [
		'name' => 'required',
		'message' => 'required'
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function users()
	{
		return $this->belongsToMany(User::class);
	}

	public function messages()
	{
		return $this->hasMany(Message::class, 'chat_id', 'id');
	}

	public function getCreatedAtAttribute($date, $fromFormat = 'Y-m-d H:i:s', $toFormat = \DateTime::ATOM)
	{
		return date_format(date_create_from_format($fromFormat, $date), $toFormat);
	}
}
