<?php

namespace App\Http\Middleware;

use App\Libraries\AppResponse;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class Authentication
{
	protected $auth;
	protected $response;

	public function __construct(AppResponse $response, JWTAuth $auth)
	{
		$this->response = $response;
		$this->auth = $auth;
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		    try {

			    if (! $user = $this->auth->parseToken()->authenticate()) {
				    return $this->response->message(404, null, null, 'User not found.');
			    }

		    } catch (TokenExpiredException $e) {

			    return $this->response->message($e->getStatusCode(), null, null, 'Token expired.');

		    } catch (TokenInvalidException $e) {

			    return $this->response->message($e->getStatusCode(), null, null, 'Token invalid.');

		    } catch (JWTException $e) {

			    return $this->response->message($e->getStatusCode(), null, null, 'Token absent.');
		    }

        return $next($request)
	        ->header('Access-Control-Allow-Origin', '*')
	        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
