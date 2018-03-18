@extends('layouts.app')

@section('title')
    Home
@endsection

@section('content')
    <div class="container">
        <div class="jumbotron text-center py-6">
            <h1>Welcome to SlackClone</h1>
            @guest
                <h3>Log in to get started - join a server to chat with friends/coworkers!</h3>
                <a href="/login" class="btn btn-primary btn-lg">Log in</a>
                <a href="/register" class="btn btn-success btn-lg">Register</a>
            @else
                <h3>You're already logged in! Click below to get started.</h3>
                <a href="/server" class="btn btn-primary btn-lg">My Server List</a>
            @endif
        </div>
    </div>
@endsection
