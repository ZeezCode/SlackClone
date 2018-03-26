<?php

namespace App\Http\Controllers;

use App\Message;
use App\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['getMessages']]);
    }

    public function index() {
        return view('pages.index');
    }

    public function getMessages(Request $request) {
        $channelId = $request->input('channel');
        $fromLast = $request->input('fromLast');

        $result = [
            'success' => 'true',
        ];

        if (is_numeric($channelId) && $channelId > 0 && is_numeric($fromLast)) {
            $channel = Channel::find($channelId);
            if ($channel == null) {
                $result['success'] = false;
                $result['error'] = 'The specified channel does not exist!';
            } else {
                if (!isUserMemberOfServer(Auth::id(), $channel->server_id)) {
                    $result['success'] = false;
                    $result['error'] = 'You are not a member of this server!';
                } else {
                    $messages = $channel->messages()->where('id', '>', $fromLast)->orderBy('id', 'ASC')->get();
                    $result['messages'] = [];
                    foreach ($messages as $message) {
                        $timestamp = $message->created_at;
                        $timestamp->setTimezone('America/New_York');
                        $timestamp = date('m/d/Y \a\t g:i a', strtotime($timestamp));
                        array_push($result['messages'], [
                            'id' => $message->id,
                            'name' => htmlentities($message->user->name),
                            'message' => nl2br(htmlentities($message->message)),
                            'created_at' => $timestamp,
                        ]);
                    }
                }
            }
        } else {
            $result['success'] = false;
            $result['error'] = 'Invalid ID! Given either non-number or negative number.';
        }

        return json_encode($result);
    }

    public function sendMessage(Request $request)
    {
        $channelId = $request->input('channel');
        $inputMsg = trim($request->input('message'));

        $result = ['success' => 'true'];

        if ($inputMsg == null || empty($inputMsg)) { //if message is invalid/nonexistent
            $result['success'] = false;
            $result['error'] = 'No message supplied!';
        } else if (!is_numeric($channelId) && $channelId <= 0) { //message is valid but channel is not
            $result['success'] = false;
            $result['error'] = 'Invalid ID! Given either no number or invalid number!';
        } else { //message is valid, channel is potentially valid
            $channel = Channel::find($channelId);
            if ($channel == null) { //if specified channel does not exist
                $result['success'] = false;
                $result['error'] = 'The specified channel does not exist!';
            } else { //specified channel exists
                if (!isUserMemberOfServer(Auth::id(), $channel->server_id)) { //if user is not member of server
                    $result['success'] = false;
                    $result['error'] = 'You are not a member of this server!';
                } else { //user is member of server, all checks clear, create new message
                    $message = new Message;
                    $message->user_id = Auth::id();
                    $message->channel_id = intval($channelId);
                    $message->message = $inputMsg;
                    $message->save();
                }
            }
        }
        return json_encode($result);
    }
}
