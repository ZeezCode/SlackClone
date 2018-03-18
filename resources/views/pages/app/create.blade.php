@extends('layouts.app')

@section('title')
    Create a Server
@endsection

@section('content')
    <div class="container">
        <h1>
            Create a Thread
            <a href="/server" class="btn btn-primary float-right">Back</a>
        </h1>
        <div class="container">
            {!! Form::open(['action' => 'ServerController@store']) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('name', 'Name') }}
                            {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Server name']) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('banner', 'Banner') }}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">https://i.imgur.com/</span>
                                </div>
                                {{ Form::text('banner', '', ['class' => 'form-control', 'placeholder' => 'Banner ID and extension']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('description', 'Description') }}
                    {{ Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Server description']) }}
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {{ Form::label('password', 'Password (Leave empty if public)') }}
                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Server password']) }}
                    </div>
                    <div class="col-md-6">
                        {{ Form::label('public', 'Public') }}
                        {{ Form::checkbox('public', 1, null, ['class' => 'form-control']) }}
                    </div>
                </div>
                {{ Form::submit('Submit', ['class' => 'btn btn-primary btn-lg my-3']) }}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
