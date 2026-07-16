<!DOCTYPE html>
<html>

<head>
    <title>Reverb Test</title>
    @vite(['resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <h2>Reverb Notification Test</h2>

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
    const userId = 9;

    console.log('Subscribing to:', `notifications.${userId}`);

    Echo.private(`notifications.${userId}`)
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