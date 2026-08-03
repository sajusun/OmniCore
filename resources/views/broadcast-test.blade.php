<!DOCTYPE html>
<html>

<head>
    <title>Broadcast Test</title>

    @vite(['resources/js/app.js'])

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .row {
            margin-bottom: 15px;
        }

        label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }

        input {
            width: 250px;
            padding: 8px;
        }

        button {
            padding: 8px 15px;
            cursor: pointer;
        }

        #logs {
            margin-top: 20px;
            border: 1px solid #ddd;
            background: #111;
            color: #00ff7f;
            padding: 15px;
            height: 450px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>
</head>

<body>

<div class="container">

    <h2 align="center">Broadcast Test</h2>

    <div class="row">
        <label>User ID</label>
        <input type="number" id="user_id" value="{{ auth('web')->id() }}">
    </div>

    <div class="row">
        <label>Room ID</label>
        <input type="number" id="room_id" value="" placeholder="add Romm id">
    </div>

    <div class="row">
        <button id="connect">Connect Channels</button>
        <button id="clear">Clear Logs</button>
    </div>

    <div id="logs"></div>

</div>

<script>

    document.addEventListener('DOMContentLoaded', () => {

        const logs = document.getElementById('logs');

        function write(title, data = null) {

            const time = new Date().toLocaleTimeString();

            let message = `[${time}] ${title}`;

            if (data) {
                message += '\n' + JSON.stringify(data, null, 2);
            }

            console.log(title, data);

            logs.innerHTML += message + "\n\n";
            logs.scrollTop = logs.scrollHeight;
        }

        let userChannel = null;
        let roomChannel = null;
        let publicChannel = null;

        document.getElementById('clear').onclick = () => {
            logs.innerHTML = '';
        };

        document.getElementById('connect').onclick = () => {

            if (typeof Echo === 'undefined') {
                write('Echo not loaded');
                return;
            }

            const userId = document.getElementById('user_id').value;
            const roomId = document.getElementById('room_id').value;

            write('Connecting...', {
                user_id: userId,
                room_id: roomId
            });

            /*
            |--------------------------------------------------------------------------
            | Leave Previous Channels
            |--------------------------------------------------------------------------
            */

            Echo.leave('test-channel');

            if (userChannel) {
                Echo.leave(`private-user.${userId}`);
            }

            if (roomChannel) {
                Echo.leave(`private-chat.room.${roomId}`);
            }

            /*
            |--------------------------------------------------------------------------
            | Public Channel
            |--------------------------------------------------------------------------
            */

            publicChannel = Echo.channel('test-channel')
                .subscribed(() => {
                    write('Subscribed: test-channel');
                })
                .listen('.test-event', (e) => {
                    write('Public Event Received', e);
                });

            /*
            |--------------------------------------------------------------------------
            | User Channel
            |--------------------------------------------------------------------------
            */

            userChannel = Echo.private(`user.${userId}`)
                .subscribed(() => {
                    write(`Subscribed: user.${userId}`);
                })
                .error((err) => {
                    write(`User Channel Error`, err);
                })
                .listen('.notification.created', (e) => {
                    write('Notification Received', e);
                });

            /*
            |--------------------------------------------------------------------------
            | Chat Room
            |--------------------------------------------------------------------------
            */

            roomChannel = Echo.private(`chat.room.${roomId}`)
                .subscribed(() => {
                    write(`Subscribed: chat.room.${roomId}`);
                })
                .error((err) => {
                    write(`Room Channel Error`, err);
                })
                .listen('.message.sent', (e) => {
                    write('Message Received', e);
                });

        };

    });

</script>

</body>

</html>