<?php

namespace App\Http\Controllers;

use App\Server;
use App\ServerMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $validateId = isImgurIdValid($request->input('banner'));
        if (!$validateId['isValid']) {
            return back()->withInput()->withErrors(['Invalid banner image ID!']);
        }

        //create server
        $s = new Server;
        $s->name = $request->input('name');
        $s->description = $request->input('description');
        $s->owner_id = Auth::id();
        $s->invite_id = getUniqueInviteString();
        $s->banner = $validateId['link'];
        $s->public = ($request->input('public') ? true : false);
        $s->password = ($pass ? bcrypt($pass) : null);
        $s->save();

        //add owner as member of server
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
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $server = Server::find($id);
        if ($server == null)
            return redirect('/server')->with('error', 'The specified server does not exist!');

        if (!isUserMemberOfServer(Auth::id(), $id))
            return redirect('/server')->withErrors(['You are not a member of this server!']);

        if ($server->owner_id != Auth::id()) {
            return redirect('/server')->withErrors(['You do not have permission to edit this server!']);
        }

        return view('pages.app.edit')->with([
            'server' => $server,
            'channels' => $server->channels()->orderBy('order', 'ASC')->get(),
        ]);
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
        $s = Server::find($id);
        if ($s == null) {
            return redirect('/server')->withErrors('The specified server does not exist!');
        }

        if ($s->owner_id != Auth::id()) {
            return redirect('/server')->withErrors(['You do not have permission to edit this server!']);
        }

        $this->validate($request, [
            'name' => 'required',
            'banner' => 'required',
            'description' => 'required',
            'public' => 'sometimes|required|integer|min:0|max:1',
            'password' => 'required_without:public',
        ]);

        $pass = $request->input('password');

        $validateId = isImgurIdValid($request->input('banner'));
        if (!$validateId['isValid']) {
            return back()->withErrors(['Invalid banner image ID!']);
        }

        $s->name = $request->input('name');
        $s->description = $request->input('description');
        $s->banner = $validateId['link'];
        $s->public = ($request->input('public') ? true : false);
        if ($pass != null && !empty($pass))
            $s->password = bcrypt($pass);
        $s->save();

        return redirect('/server')->with('success', 'You have successfully edited your server: ' . $s->name);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $s = Server::find($id);
        if ($s == null) {
            return redirect('/server')->withErrors(['The specified server does not exist!']);
        }

        if ($s->owner_id != Auth::id()) {
            return redirect('/server')->withErrors(['You do not have permission to edit this server!']);
        }

        foreach ($s->channels as $channel) {
            foreach($channel->messages as $message)
                $message->forceDelete(); //delete all messages in channel
            $channel->forceDelete(); //delete all channels in server
        }

        foreach($s->memberships as $membership)
            $membership->delete(); //delete all server memberships
        $s->delete(); //soft delete server

        return redirect('/server')->with('success', 'You have successfully deleted server: ' . $s->name);
    }

    /**
     * Show user an invitation to join given server
     *
     * @param  $invite_id
     * @return \Illuminate\Http\Response
     */
    public function invite($invite_id) {
        $server = Server::where('invite_id', $invite_id)->get();
        if (count($server) == 0) {
            return redirect('/server')->withErrors(['The specified invite ID does not exist!']);
        }

        $server = $server[0]; //get() returns array of results, give user first position to get actual server
        if (isUserMemberOfServer(Auth::id(), $server->id)) {
            return redirect('/server')->withErrors(['You are already a member of this server!']);
        }

        return view('pages.app.invite')->with('server', $server);
    }

    /**
     * Send a request to join a server
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request, $id) {
        $server = Server::find($id);
        if ($server == null) {
            return redirect('/server')->withErrors(['The specified server does not exist!']);
        }

        if ($server->public == 0) { //if server is private
            $inputPass = $request->input('password');
            if ($inputPass == null) { //if no password specified
                return back()->withErrors(['You must specify a password for this server!']);
            }

            if (!Hash::check($inputPass, $server->password)) { //if password is incorrect
                return back()->withInput()->withErrors(['Incorrect password specified!']);
            }
        }

        $sm = new ServerMembership;
        $sm->user_id = Auth::id();
        $sm->server_id = $server->id;
        $sm->save();

        return redirect('/server')->with('success', 'You have successfully joined server: ' . $server->name);
    }
}
