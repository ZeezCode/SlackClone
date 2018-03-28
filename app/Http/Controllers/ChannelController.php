<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Channel;
use App\Server;

class ChannelController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'server' => 'required|integer|min:1',
           'name' => 'required',
           'order' => 'required|integer|min:1',
        ]);

        $server = Server::find(intval($request->input('server')));
        if ($server == null) {
            return back()->withErrors(['The specified server does not exist!']);
        }

        $c = new Channel;
        $c->name = $request->input('name');
        $c->order = $request->input('order');
        $c->server_id = $server->id;
        $c->save();

        return redirect('/server/' . $server->id . '/edit')->with(
            'success',
            'You have successfully created channel: ' . $request->input('name')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $c = Channel::find($id);
        if ($c == null) {
            return redirect('/server')->withErrors(['The specified channel does not exist!']);
        }

        $s = $c->server;
        if ($s->owner_id != Auth::id()) {
            return redirect('/server')->withErrors(['You do not have permission to edit this server!']);
        }

        $this->validate($request, [
            'name' => 'required',
            'order' => 'required|integer|min:1',
        ]);

        $c->name = $request->input('name');
        $c->order = $request->input('order');
        $c->save();

        return redirect('/server/' . $s->id . '/edit')->with('success', 'You have successfully updated a channel!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $c = Channel::find($id);
        if ($c == null) {
            return redirect('/server')->withErrors(['The specified channel does not exist!']);
        }

        $s = $c->server;
        if ($s->owner_id != Auth::id()) {
            return redirect('/server')->withErrors(['You do not have permission to edit this server!']);
        }

        foreach($c->messages as $message)
            $message->forceDelete(); //delete all messages in channel

        $c->forceDelete();

        return redirect('/server/' . $s->id . '/edit')->with('success', 'You have successfully deleted channel: ' . $c->name);
    }
}
