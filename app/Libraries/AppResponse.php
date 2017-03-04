<?php
/**
 * Created by PhpStorm.
 * User: Ronny
 * Date: 3/1/17
 * Time: 8:46 PM
 */

namespace App\Libraries;
use Carbon\Carbon;
use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class AppResponse {

	/**
	 * @var string
	 * The title of the errors
	 */
	public $message;
	/**
	 * @var object
	 * An object containing all errors
	 */
	protected $errors = null;
	/**
	 * @var int
	 * response code http
	 */
	protected $httpCode = 200;
	/**
	 * @var object
	 * data response
	 */
	protected $data;
	/**
	 * @var object
	 * additional data
	 */
	protected $meta;

	/**
	 * @param JWTAuth $auth
	 */

	public function __construct(JWTAuth $auth)
	{
		$this->auth = $auth;
		$this->meta = new \stdClass;
	}

	public function validationParameters($array, $rules)
	{
		$validator = Validator::make($array, $rules);

		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			die($this->message(409, null, $this->errors, 'Validation Failed'));
		}
	}

	public function message($codeResponse, $data = null, $error = null, $message = null, $meta = false)
	{
		$this->httpCode = $codeResponse;
		if (!is_null($data)) $this->data = $data;
		if (!is_null($error)){
			$errorObj = new \stdClass();
			$this->errors = $errorObj->error = $error;
		}
		if (!is_null($message)) $this->message = $message;

		return $this->jsonResponse($meta);
	}

	protected function schema($meta = null)
	{
		$objResponse = new \stdClass();
		if (!is_null($this->message)) $objResponse->messages = $this->message;
		if (!is_null($this->data)) $objResponse->data = $this->data;
		if (!is_null($this->errors)) $objResponse->errors = $this->errors;
		if (!$meta)
		{
			$objResponse->meta = $this->meta;
		}else{
			$objResponse->meta = $meta;
		}

		if (property_exists($objResponse, 'data') && isset($objResponse->data['data']))
		{
			$objResponse->data = $objResponse->data['data'];
		}

		return $objResponse;
	}

	protected function jsonResponse($meta = null)
	{

		if ($this->auth->getToken())
		{
			return response()->json($this->schema($meta), $this->httpCode)
				->header('Content-Type', 'application/json; charset=UTF-8')
				->header('Authorization', 'Bearer '.$this->auth->getToken());
		}

		return response()->json($this->schema(), $this->httpCode)
			->header('Content-Type', 'application/json; charset=UTF-8');
	}

	public function dateFormat($date, $fromFormat = 'Y-m-d H:i:s', $toFormat = \DateTime::ATOM)
	{
		return date_format(date_create_from_format($fromFormat,$date), $toFormat);
	}
} 