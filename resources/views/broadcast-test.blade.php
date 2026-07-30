<!DOCTYPE html>
<html>

<head>
    <title>Broadcast Test</title>
    @vite(['resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <center>
        <h2>Broadcast Notification Test</h2>
    </center>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            console.log('DOM Loaded');

            if (typeof Echo === 'undefined') {
                console.error('Echo not loaded');
                return;
            }
            Echo.channel('test-channel')
                .listen('.test-event', (e) => {
                    console.log('Event received:', e);
                });
            let userId = @json(auth()->id());

            console.log('Subscribing to:', `notifications.${userId}`);

            // Echo.private('chat.room.1')
            //     .listen('.message.sent', (data) => {
            //         console.log('New real-time message received:', data.message);
                    
            //     });

            Echo.private(`user.${userId}`)
                .subscribed(() => {
                    console.log('Successfully subscribed');
                })
                .error((error) => {
                    console.error('Subscription error:', error);
                })
                .listen('.notification.created', (e) => {
                    console.log('Notification Received:', e);
                });

        });
    </script>

</body>

</html>