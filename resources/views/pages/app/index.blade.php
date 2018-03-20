@extends('layouts.app')

@section('title')
    Server List
@endsection

@section('content')
    <div class="container">
        <div>
            <h1 class="float-left">My Servers</h1>
            <p class="text-right"><a class="btn btn-outline-primary mb-3" href="/server/create">Create a Server</a></p>
        </div>
        @if(count($servers) > 0)
            <div class="row">
                @foreach($servers as $server)
                    <div class="col-md-5 @if ($loop->index % 2 != 0) offset-md-2 @endif">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{$server->name}}</h5>
                            </div>
                            <div class="gray-bg">
                                <img class="card-img-top server-banner-img" src="{{$server->banner}}" alt="Server banner image">
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{$server->description}}</p>
                                <a href="/server/{{$server->id}}" class="btn btn-primary">Connect</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <h3 class="text-center">You are not currently a member of any servers.<br />Join one or create your own!</h3>
        @endif
    </div>
@endsection
