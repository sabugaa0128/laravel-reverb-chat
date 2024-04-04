import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});


/**
 * Subscribing to a Channel and Listening for Events
 * DOC: https://laravel.com/docs/11.x/broadcasting#namespaces
 *
 * ChatMessages is the .Namespace\\Event\\Class :
 *  app\Events\ChatMessages.php
 */
const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');
const sendMessageButton = document.getElementById('send-message');
const messageTextarea = document.getElementById('message-area');
const messagesList = document.getElementById('messages');

// Send a message button
sendMessageButton.addEventListener('click', function() {
    const message = messageTextarea.value;
    const recipientId = this.getAttribute('data-recipient-id');

    sendMessageToServer(message, recipientId);
});

// Listening for messages
window.Echo.private(`chat-channel.${userId}`)
    .listen('.chat.messages', (event) => {
        //console.log('Listen: ', event);
        const newMessage = document.createElement('li');
        newMessage.textContent = event.message;
        messagesList.appendChild(newMessage);
    });

// Ajax to store in database
function sendMessageToServer(message, recipientId) {
    //console.log('Sending message: ', message);

    fetch('/send-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: message,
            recipient_id: recipientId
        })
    })
    .then(response => response.json())
    .then(data => {
        //console.log('Success:', data);

        if (data.success) {
            appendMessageToChat(message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });

    // Reset textarea after sending
    messageTextarea.value = '';
}

// Populate chat with messages
function appendMessageToChat(message) {
    // Find the chat list in the DOM
    const chatList = document.querySelector('#chat-containner ul');

    // Create a new list item
    const newMessage = document.createElement('li');

    // adding <br> elements between lines.
    message.split('\n').forEach((line, index, array) => {
        const lineNode = document.createTextNode(line);
        newMessage.appendChild(lineNode);

        // If this is not the last line, add a <br> element
        if (index < array.length - 1) {
            newMessage.appendChild(document.createElement('br'));
        }
    });

    // Append the new list item to the chat list
    chatList.appendChild(newMessage);

    messageTextarea.focus();
}
