@extends('layouts.app')

@section('title')
    Join Server {{$server->name}}
@endsection

@section('content')
    <div class="container">
        <h1 class="text-center">You've been invited!</h1>
        <div class="row">
            <div class="col"></div>
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{$server->name}}</h5>
                    </div>
                    <div class="gray-bg">
                        <img class="card-img-top server-banner-img" src="{{$server->banner}}" alt="Server banner image">
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-2">{{$server->description}}</p>
                        {!! Form::open(['action' => ['ServerController@join', $server->id], 'style' => 'top: -15px;']) !!}
                            {{ Form::hidden('invite_id', $server->invite_id) }}
                            @if($server->public == 0)
                                <div class="form-group">
                                    {{ Form::label('password', 'Password') }}
                                    {{ Form::text('password', '', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                                </div>
                            @endif
                            {{ Form::submit('Join Server', ['class' => 'btn btn-primary btn-lg d-block mx-auto']) }}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="col"></div>
        </div>
    </div>
@endsection
