<?php

namespace App\Http\Controllers;

use App\Server;
use App\ServerMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    /**
     * ServerController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberships = ServerMembership::where('user_id', Auth::id())->get();
        $servers = [];
        foreach($memberships as $membership)
            array_push($servers, $membership->server);

        return view('pages.app.index')->with('servers', $servers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.app.create');
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
            'name' => 'required',
            'banner' => 'required',
            'description' => 'required',
            'public' => 'sometimes|required|integer|min:0|max:1',
            'password' => 'required_without:public',
        ]);

        $pass = $request->input('password');

        $validateId = $request->input('banner');
        if (!$validateId['isValid']) {
            return back()->withInput()->withErrors(['Invalid banner image ID!']);
        }

        $s = new Server;
        $s->name = $request->input('name');
        $s->description = $request->input('description');
        $s->owner_id = Auth::id();
        $s->banner = $validateId['link'];
        $s->public = ($request->input('public') ? true : false);
        $s->password = ($pass ? bcrypt($pass) : null);
        $s->save();

        $sm = new ServerMembership;
        $sm->user_id = Auth::id();
        $sm->server_id = $s->id;
        $sm->save();

        return redirect('/server')->with('success', 'You have successfully created your server: ' . $s->name);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $server = Server::find($id);
        if ($server == null)
            return redirect('/server')->with('error', 'The specified server does not exist!');

        if (!isUserMemberOfServer(Auth::id(), $id))
            return redirect('/server')->withErrors(['You are not a member of this server!']);

        return view('pages.app.show')->with([
            'server' => $server,
            'channels' => $server->channels()->orderBy('order', 'ASC')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        //
    }
}
