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
        <form action="#" id="chat-form">
            <input type="text" id="message" placeholder="Message" class="ml-3 mt-3" autocomplete="off" />
        </form>
    </div>
@endsection

@section('script')
    <script>
        var curChanId = 0; //current channel ID
        var lastMessageReceived = 0;//ID of last message received in current channel
        var originalTitle = document.title; //current and original title of page
                                            // used for later altering w/o losing base title
        //on channel click
        function joinChannel(elem) {
            //cancel if no change
            if (elem.value === curChanId) return;

            //change page title to include channel name
            document.title = elem.innerText + ' - ' + originalTitle;

            //remove messages from chat
            $('#chat-list').children().remove();

            //set current channel
            var oldChan = curChanId;
            curChanId = elem.value;

            //reset latest message id
            lastMessageReceived = 0;

            //remove active state from previous channel
            $('button').removeClass('active');

            //add active state to current channel
            $(elem).addClass('active');

            //begin fetching latest messages if not already
            if (oldChan === 0)
                getLatestMessages();
        }

        //when document's ready
        $(function() {
            //on message submit
            $('#chat-form').on('submit', function(e) {
                e.preventDefault(); //cancel form submit
                var messageElement = $('#message');
                var msg = messageElement.val(); //save message so we can reset input element
                messageElement.val(''); //remove message from input element

                if (isNaN(curChanId) || curChanId <= 0) {
                    $('#chat-list').append('<li class="list-group-item list-group-item-action bg-light">You are not currently in a channel!</li>');
                } else { //Channel ID is set, attempt to submit message
                    $.ajax({
                        type: 'GET',
                        url: '../../api/sendMessage',
                        data: {channel:curChanId, message:msg},
                        dataType: 'json',
                        success: function(data) {
                            if (!data.success) { //if error occurred while sending new message
                                $('#chat-list').append(
                                    '<li class="list-group-item list-group-item-action bg-light">' +
                                        'An error occurred while attempting to send your message.<br />' +
                                        data.error +
                                    '</li>');
                            }
                            //no else statement needed b/c getLatestMessages is already checking for new messages every x seconds
                        }
                    });
                }
            });
        });

        //fetch latest messages in channel
        var getLatestMessages = function() {
            if (curChanId !== 0) { //if current channel is set
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
                                        '<strong>' + msg['name'] + ':</strong>' +
                                        '<span class="float-right">' + msg['created_at'] + '</span>' +
                                        '<br />' +
                                        msg['message'] +
                                        '</li>'
                                );

                                lastMessageReceived = msg['id'];
                            }
                        }
                    }
                });
                setTimeout(getLatestMessages, 1000); //fetch latest message every x seconds
            }
        }
    </script>
@endsection
