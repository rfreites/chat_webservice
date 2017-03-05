<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function create(Request $request, $id)
    {
	    $request->only(['message']);

	    $this->response->validationParameters($request->all(), ['message' => 'required']);

	    $chat = Chat::find($id);

	    if (is_null($chat)) return $this->response->message(404, null, null, 'Chat not found');

	    $user = \Auth::user();

	    $new_message = new Message();
	    $new_message->message = $request->message;
	    $new_message->user_id = $user->id;
	    $new_message->chat_id = $chat->id;
	    $new_message->save();
		$new_message->users = $user;

	    return $this->response->message(201, $new_message);
    }

	public function show(Request $request, $id)
	{
		$request->only(['page', 'limit']);

		$result = [];

		if (isset($request->page) && !is_null($request->page) && isset($request->limit) && !is_null($request->limit))
		{

			$page = $request->page;
			$limit = $request->limit;

			$total_request = $page * $limit;

			$count = Message::count();

			$page_count = $count / $limit;

			$pagination = null;

			if ($total_request <= $count && !is_null($count))
			{

				$messages = Message::orderBy('created_at','desc')->paginate($limit);

				foreach($messages as $message)
				{
					$data = new \stdClass();
					$data->id = $message->id;
					$data->user_id = $message->user_id;
					$data->message = $message->message;
					$data->created_at = $message->created_at;
					$data->user = $message->user();

					$result[] = $data;
				}

				$pagination = new \stdClass();
				$pagination->current_page = (int) $page;
				$pagination->per_page = (int) $limit;
				$pagination->page_count = is_float($page_count) ? floor($page_count) + 1 : $page_count;
				$pagination->total_count = $count;
			}
			else{

				$messages = Message::all();

				if (!is_null($messages))
				{
					$messages = Message::paginate($limit);

					foreach($messages as $message)
					{
						$data = new \stdClass();
						$data->id = $message->id;
						$data->user_id = $message->user_id;
						$data->message = $message->message;
						$data->created_at = $message->created_at;
						$data->user = $message->user();

						$result[] = $data;
					}

					$result = array_reverse($result);
				}
			}

			if (!empty($result))
			{
				return $this->response->message(200, $result, null, null, $pagination);
			}else{
				return $this->response->message(404, $result);
			}

		}

		$messages = Message::all();

		if (!is_null($messages))
		{
			$messages = Message::all();

			foreach($messages as $message)
			{
				$data = new \stdClass();
				$data->id = $message->id;
				$data->user_id = $message->user_id;
				$data->message = $message->message;
				$data->created_at = $message->created_at;
				$data->user = $message->user();

				$result[] = $data;
			}

			$result = array_reverse($result);
		}

		if (!empty($result))
		{
			return $this->response->message(200, $result);
		}else{
			return $this->response->message(404, $result);
		}
	}
}
