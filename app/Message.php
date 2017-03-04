<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	public $primaryKey = 'id';

	protected $table = 'messages';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'message'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'updated_at',
		'deleted_at',
	];

	public function chat()
	{
		return $this->hasOne(Chat::class);
	}

	public function user()
	{
		return $this->hasOne(User::class);
	}

	public function getCreatedAtAttribute($date, $fromFormat = 'Y-m-d H:i:s', $toFormat = \DateTime::ATOM)
	{
		return date_format(date_create_from_format($fromFormat, $date), $toFormat);
	}
}
