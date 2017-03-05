<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
	public function create(Request $request)
	{
		$request->only(['name', 'message']);

		$this->response->validationParameters($request->all(), Chat::$rules);

		$user = \Auth::user();

		$chat = new Chat();
		$chat->name = $request->name;
		$chat->save();
		$chat->users()->attach($user->id);
		$chat->save();

		$msg = new Message();
		$msg->message = $request->message;
		$msg->user_id = $user->id;
		$msg->chat_id = $chat->id;
		$msg->save();

		$data = new \stdClass();
		$data = $chat;
		$data->users = $user;
		$data->last_chat_message = $msg;
		$msg->user = $user;

		return $this->response->message(201, compact('data'));
	}

	public function show(Request $request)
	{
		$request->only(['page','limit']);

		$result = [];

		if (isset($request->page) && !is_null($request->page) && isset($request->limit) && !is_null($request->limit))
		{

			$page = $request->page;
			$limit = $request->limit;

			$total_request = $page * $limit;

			$count = Chat::count();

			$page_count = $count / $limit;

			$pagination = null;

			if ($total_request <= $count && !is_null($count))
			{
				$chats = Chat::orderBy('created_at','desc')->paginate($limit);

				foreach($chats as $chat)
				{
					$data = new \stdClass();
					$data->id = $chat->id;
					$data->name = $chat->name;
					$users = [];
					foreach($chat->users as $user)
					{
						$users[] = $user;
					}
					$data->users = $users;
					$last_message = $chat->messages()->latest()->first();
					if (is_null($last_message))
					{
						$last_message = $chat->messages()->first();
					}
					$data->last_chat_message = $last_message;

					$result[] = $data;
				}

				$pagination = new \stdClass();
				$pagination->current_page = (int) $page;
				$pagination->per_page = (int) $limit;
				$pagination->page_count = is_float($page_count) ? floor($page_count) + 1 : $page_count;
				$pagination->total_count = $count;

			}
			else{

				$chats = Chat::all();

				if (!is_null($chats))
				{
					foreach($chats as $chat)
					{
						$data = new \stdClass();
						$data->id = $chat->id;
						$data->name = $chat->name;
						$users = [];
						foreach($chat->users as $user)
						{
							$users[] = $user;
						}
						$data->users = $users;
						$last_message = $chat->messages()->latest()->first();
						if (is_null($last_message))
						{
							$last_message = $chat->messages()->first();
						}
						$data->last_chat_message = $last_message;

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

		$chats = Chat::all();

		if (!is_null($chats))
		{
			foreach($chats as $chat)
			{
				$data = new \stdClass();
				$data->id = $chat->id;
				$data->name = $chat->name;
				$users = [];
				foreach($chat->users as $user)
				{
					$users[] = $user;
				}
				$data->users = $users;
				$last_message = $chat->messages()->latest()->first();
				if (is_null($last_message))
				{
					$last_message = $chat->messages()->first();
				}
				$data->last_chat_message = $last_message;

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

	public function update(Request $request, $id)
	{
		$request->only(['name']);

		$this->response->validationParameters($request->all(), ['name' => 'required']);

		$chat = Chat::find($id);

		if (is_null($chat)) return $this->response->message(404, null, null, 'Chat not found.');

		$is_related = DB::table('chat_user')
			->where('chat_id', '=', $chat->id)
			->where('user_id', '=', \Auth::user()->id)
			->first();

		if (is_null($is_related)) return $this->response->message(401, null, null, 'Invalid chat owner.');

		if (!is_null($request->name) && $request->name != '' && $request->name != $chat->name)
		{
			$chat->name = $request->name;
			$chat->save();
		}

		$data = new \stdClass();
		$data = $chat;
		foreach($chat->users as $user)
		{
			$users[] = $user;
		}
		$data->users = $users;
		$last_message = $chat->messages()->latest()->first();
		if (is_null($last_message))
		{
			$last_message = $chat->messages()->first();
		}
		$data->last_chat_message = $last_message;

		return $this->response->message(200, $data);
	}
}
