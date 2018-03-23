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
            @foreach($channels as $channel)
                <button type="button" value="{{$channel->id}}" class="list-group-item list-group-item-action" onclick="joinChannel(this)">
                    {{$channel->name}}
                </button>
            @endforeach
        </ul>
    </div>
    <div id="chat-area">
        <div id="chat-messages">
            <ul class="list-group" id="chat-list">
                <!--
                <li class="list-group-item list-group-item-action bg-light">Message</li>
                -->
            </ul>
        </div>
        <div id="chat-input-bar">
            <form action="#" id="chat-form">
                <input type="text" id="message" placeholder="Message" class="ml-3 mt-3" autocomplete="off" />
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var curChanId = 0;
        var lastMessageReceived = 0;

        //on channel click
        function joinChannel(elem) {
            //remove all messages in chat
            $('#chat-list').children().remove();

            //reset latest message id
            lastMessageReceived = 0;

            //set current channel
            curChanId = elem.value;

            //begin fetching latest messages
            getLatestMessages();
        }

        //on message submit
        $(function() {
            $('#chat-form').on('submit', function(e) {
                e.preventDefault(); //cancel form submit

                if (isNaN(curChanId) || curChanId <= 0) {
                    $('#chat-list').append('<li class="list-group-item list-group-item-action bg-light">You are not currently in a channel!</li>');
                } else { //Channel ID is set, attempt to submit message
                    var messageElement = $('#message');
                    $.ajax({
                        type: 'GET',
                        url: '../../api/sendMessage',
                        data: {channel:curChanId, message:messageElement.val()},
                        dataType: 'json',
                        success: function(data) {
                            messageElement.val('');
                            if (!data.success) { //if error occurred while sending new message
                                $('#chat-list').append(
                                    '<li class="list-group-item list-group-item-action bg-light">' +
                                        'An error occurred while attempting to send your message.<br />' +
                                        data.error +
                                    '</li>');
                            }
                            //no else statement needed b/c getLatestMessages is already checking for new messages every 2 seconds
                        }
                    });
                }
            });
        });

        //fetch latest messages in channel
        var getLatestMessages = function() {
            if (curChanId != 0) { //if current channel is set
                //fetch new messages and insert into chat
                $.ajax({
                    type: 'GET',
                    url: '../../api/getMessages',
                    data: {channel:curChanId,fromLast:lastMessageReceived},
                    dataType: 'json',
                    success: function(data) {
                        if (!data.success) {
                            $('#chat-list').append('<li class="list-group-item list-group-item-action bg-light">An error occurred while attempting to receive the existing messages.</li>');
                        } else {
                            for (var i in data.messages) {
                                msg = data.messages[i];
                                $('#chat-list').append(
                                    '<li class="list-group-item list-group-item-action bg-light">' +
                                        '<strong>' + msg[1] + ':</strong><br />' + msg[2] +
                                    '</li>'
                                );
                                lastMessageReceived = msg[0];
                            }
                        }
                    }
                });
                setTimeout(getLatestMessages, 2 * 1000); //fetch latest message every 2 seconds (2000 milliseconds)
            }
        }
    </script>
@endsection
