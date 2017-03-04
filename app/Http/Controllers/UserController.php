<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request)
    {
	    $request->only(['name', 'email', 'password', 'password_confirmation']);

	    $this->response->validationParameters($request->all(), User::$rules);

	    if ($request->password != $request->password_confirmation)
	    {
		    $error = new \stdClass();
		    $error->password = ['Password conflict.'];
		    return $this->response->message(409, null, $error, 'Validation Failed');
	    }

	    $user = new User();
	    $user->name = $request->name;
	    $user->email = $request->email;
	    $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
	    $user->save();

	    $this->response->auth->setToken($this->getJWT());

	    return $this->response->message(201, compact('user'));
    }

	public function login(Request $request)
	{
		$request->only('email', 'password');

		$user = User::
			where('email', '=', $request->email)
			->first();

		if (is_null($user))
		{
			return $this->response->message(404, null, null, 'User not found.');
		}

		$this->response->auth->setToken($this->getJWT());

		return $this->response->message(200, compact('user'), null, null);
	}

	public function logout()
	{
		$this->auth->parseToken()->invalidate();

		return $this->response->message(204, null, null, null, true);
	}

	public function current()
	{
		$user = User::find(\Auth::user()->id);
		return $this->response->message(200, compact('user'));
	}

	public function update(Request $request)
	{
		$request->only(['name', 'email', 'password']);

		$user = User::find(\Auth::user()->id);

		if(isset($request->name) && !is_null($request->name) && $request->name != $user->name) $user->name = $request->name;

		if(isset($request->email) &&  !is_null($request->email) && $request->email != $user->email)
		{
			$this->response->validationParameters($request->all(), [
				'email' => 'unique:users|email|required',
			]);

			$user->email = $request->email;
		}

		if(isset($request->password) && isset($request->password_confirmation) && !is_null($request->password) && !is_null($request->password_confirmation))
		{
			if ($request->password != $request->password_confirmation)
			{
				$error = new \stdClass();
				$error->password = ['Password conflict.'];
				return $this->response->message(409, null, $error, 'Validation Failed');
			}

			$user->password = \Illuminate\Support\Facades\Hash::make($request->password);
		}

		$user->save();

		return $this->response->message(200, compact('user'));
	}
}
