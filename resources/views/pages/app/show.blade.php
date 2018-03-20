@extends('layouts.app')

@section('title')
    {{$server->name}}
@endsection

@section('style')
    <style>
        body {
            padding-top: 31px;
        }
    </style>
@endsection
@section('content')
    <div class="sidebar bg-white">
        <h3 class="text-center my-3">{{$server->name}}</h3>
        <ul class="list-group list-group-flush">
            <button type="button" class="list-group-item list-group-item-action">Channel 1</button>
            <button type="button" class="list-group-item list-group-item-action">Channel 2</button>
            <button type="button" class="list-group-item list-group-item-action">Channel 3</button>
            <button type="button" class="list-group-item list-group-item-action">Channel 4</button>
            <button type="button" class="list-group-item list-group-item-action">Channel 5</button>
        </ul>
    </div>
    <div class="chat">

    </div>
@endsection
