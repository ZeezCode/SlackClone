@extends('layouts.app')

@section('title')
    Edit {{$server->name}}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>
                    Edit Server
                    <button type="button" class="btn btn-info float-sm-right float-md-none float-lg-right" data-toggle="modal" data-target="#inviteToServerModal">
                        Invite Link
                    </button>
                </h1>

                <div class="modal fade" id="inviteToServerModal" tabindex="-1" role="dialog" aria-labelledby="inviteToServerLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="inviteToServerModalLabel">Server Invite Link</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    {{ Form::label('inviteLink', 'Invite Link') }}
                                    {{ Form::text('inviteLink', env('APP_URL', 'https://slack.aidanmurphey.com') . '/invite/' . $server->invite_id, ['id' => 'inviteLinkInput', 'class' => 'form-control', 'readonly' => 'readonly']) }}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="copyLink()">Copy Link</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                {!! Form::open(['action' => ['ServerController@update', $server->id], 'method' => 'PUT', 'style' => 'clear: both;']) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('name', 'Name') }}
                                {{ Form::text('name', $server->name, ['class' => 'form-control', 'placeholder' => 'Server name']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('banner', 'Banner') }}
                                {{ Form::text('banner', getIdFromImgurLink($server->banner), ['class' => 'form-control', 'placeholder' => 'Imgur image ID']) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('description', $server->description, ['class' => 'form-control', 'placeholder' => 'Server description']) }}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::label('password', 'Password (Only enter if changing)') }}
                            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Server password']) }}
                        </div>
                        <div class="col-md-6">
                            {{ Form::label('public', 'Public') }}
                            {{ Form::checkbox('public', 1, $server->public == 1 ? true : null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    {{ Form::submit('Update', ['class' => 'btn btn-primary float-left my-3']) }}
                {!! Form::close() !!}

                {!! Form::open(['action' => ['ServerController@destroy', $server->id], 'method' => 'DELETE']) !!}
                    {{ Form::submit('Delete', ['class' => 'btn btn-danger float-right my-3']) }}
                {!! Form::close() !!}
                <hr class="d-md-none mt-3" style="clear: both;">
            </div>
            <div class="col-md-6">
                <h1>
                    Edit Channels
                    <button type="button" class="btn btn-info float-sm-right float-md-none float-lg-right" data-toggle="modal" data-target="#createChannelModal">
                        Create Channel
                    </button>
                </h1>

                <div class="modal fade" id="createChannelModal" tabindex="-1" role="dialog" aria-labelledby="createChannelLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            {!! Form::open(['action' => 'ChannelController@store']) !!}
                            <div class="modal-header">
                                <h5 class="modal-title" id="createChannelModalLabel">Create Channel</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                {{ Form::hidden('server', $server->id) }}
                                <div class="form-group">
                                    {{ Form::label('name', 'Name') }}
                                    {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Channel name']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('order', 'Order') }}
                                    {{ Form::number('order', 1, ['class' => 'form-control', 'min' => '1']) }}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                {{ Form::submit('Create Channel', ['class' => 'btn btn-primary']) }}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                @if(count($channels) > 0)
                    @foreach($channels as $channel)
                        <div class="card mb-3">
                            <div class="card-header">
                                {{$channel->name}}
                            </div>
                            <div class="card-body">
                                {!! Form::open(['action' => ['ChannelController@update', $channel->id], 'method' => 'PUT']) !!}
                                    <div class="form-group">
                                        {{ Form::label('name', 'Name') }}
                                        {{ Form::text('name', $channel->name, ['class' => 'form-control', 'placeholder' => 'Channel name']) }}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('order', 'Order') }}
                                        {{ Form::number('order', $channel->order, ['class' => 'form-control', 'min' => '1']) }}
                                    </div>
                                    {{ Form::submit('Update', ['class' => 'btn btn-primary float-left mb-1']) }}
                                {!! Form::close() !!}

                                {{ Form::open(['action' => ['ChannelController@destroy', $channel->id], 'method' => 'DELETE']) }}
                                    {{ Form::submit('Delete', ['class' => 'btn btn-danger float-right']) }}
                                {{ Form::close() }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <h1>Your server has no channels! Create one?</h1>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function copyLink() {
            /* Get the text field */
            var linkInput = $('#inviteLinkInput');

            /* Select the text field */
            linkInput.select();

            /* Copy the text inside the text field */
            document.execCommand("Copy");

            /* Notify user of copied text */
            linkInput.notify("You've copied the URL!", "success");
        }
    </script>
@endsection
