// DOC: https://laravel.com/docs/11.x/broadcasting#client-reverb


// Import Echo from Laravel Echo library and Pusher as the broadcasting driver.
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Pusher as the broadcasting driver for Echo. Here the environment variables.
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});


// Initialize variables for managing state and DOM elements.
let currentPage = 1;
let isLoadingOldMessages = false;
const selectUser = document.getElementById('select-user');
const currentUserId = document.querySelector('meta[name="current-user-id"]').getAttribute('content');
const chatContainer = document.getElementById('chat-container');
const messagesContainer = document.querySelector('#chat-container ul#messages-container');
const sendMessageButton = document.getElementById('send-message');
const messageTextarea = document.getElementById('message-textarea');
const errorContainer = document.getElementById('error-container');

// Event listener for the send message button.
sendMessageButton.addEventListener('click', function() {
    const recipientId = document.getElementById('send-message').getAttribute('data-recipient-id');
    const message = messageTextarea.value.trim();

    if (message && recipientId) {
        // Send the message to the server.
        sendMessageToServer(message, recipientId);
    }
});


// Send a new message to the server via AJAX
function sendMessageToServer(message, recipientId) {
    // console.log('Sending message: ', message);

    if (message && recipientId){
        fetch('/post-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                recipient_id: parseInt(recipientId)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Flag and message in case of validation failure
                if (data.error) {
                    console.error('Validation Error:', data.error);

                    // Display the error to the user, adjust selector as needed
                    errorContainer.textContent = data.error;
                } else if (data.errors) {
                    // Handle multiple validation errors (e.g., Laravel validation errors)
                    // Join all error messages into a single string to display
                    const allErrors = Object.values(data.errors).map(errorArray => errorArray.join(', ')).join('; ');
                    console.error('Validation Errors:', allErrors);

                    errorContainer.textContent = allErrors;
                }
            } else {
                // Handle success case
                // console.log('Success:', data.message);

                messageTextarea.value = ''; // Reset textarea after successful sending

                errorContainer.textContent = '';// Reset error
            }
        })
        .catch((error) => {
            console.error('Fetch Error:', error.message);

            errorContainer.textContent = 'An error occurred while sending the message.';
        });
    }


}


// Populate chat container with new messages
function appendMessageToChat(message, senderName, senderId) {
    // Create a new list item
    const newMessage = document.createElement('li');

    // Add a class based on the sender
    if (senderId == currentUserId) {
        newMessage.classList.add('current-user');
    } else {
        newMessage.classList.add('other-user');
    }

    // Create a strong element for the sender's name
    const strongElement = document.createElement('strong');
    strongElement.textContent = `${senderName}:`;

    // Append the strong element to the list item
    newMessage.appendChild(strongElement);

    // Process and append the message, maintaining line breaks
    const messageParts = message.split('\n');
    if (messageParts.length > 0) {
        // Append the first part of the message directly after the sender's name
        const firstLineNode = document.createTextNode(` ${messageParts[0]}`);
        newMessage.appendChild(firstLineNode);

        // For subsequent lines, add a <br> followed by the line
        messageParts.slice(1).forEach(line => {
            newMessage.appendChild(document.createElement('br'));
            const lineNode = document.createTextNode(line);
            newMessage.appendChild(lineNode);
        });
    }

    // Append the new list item to the chat list
    messagesContainer.appendChild(newMessage);

    scrollToBottom();

    messageTextarea.focus();
}


// Fetch older messages from the server
function fetchOldMessages(recipientId, page = 1) {
    if(recipientId){
        if (isLoadingOldMessages) return;
        isLoadingOldMessages = true;

        fetch(`/get-messages/${recipientId}?page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => response.json())
        .then(data => {
            // console.log('Old Messages:', data);

            const Data = data.data;
            Data.forEach(message => {
                prependMessageToChat(message.message, message.sender_name, message.sender_id);
            });
            if (data.current_page < data.last_page) {
                currentPage++;
            } else {
                // Optionally, remove the scroll listener if there are no more pages to load
                return;
            }
            isLoadingOldMessages = false;

            // Scroll to the bottom after loading messages
            scrollToBottom();
        })
        .catch((error) => {
            console.error('Error fetching old messages:', error);
            isLoadingOldMessages = false;
        });
    }
}

// Populate old messages to the chat container
function prependMessageToChat(message, senderName, senderId) {
    const newMessage = document.createElement('li');
    newMessage.className = senderId == currentUserId ? 'current-user' : 'other-user';
    newMessage.innerHTML = `<strong>${senderName}:</strong> ${message}`;
    // Prepend the message at the top of the container
    messagesContainer.insertBefore(newMessage, messagesContainer.firstChild);
}

// Generate a unique channel name based on the user Ids
function generateChannelName(userId1, userId2) {
    // To avoid ambiguity, you might want to sort the user IDs so that the lower ID always comes first. This ensures that both users listen and broadcast to the same channel name regardless of who initiates the chat.
    return `chat-channel.${[userId1, userId2].sort((a, b) => a - b).join('_')}`;
}

// Scroll the messages container to the bottom
function scrollToBottom() {
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 100);
}


// Listen for changes on the select element
// To update the recipient ID
function updateRecipientId() {
    const recipientId = selectUser.value;

    // Make the chat-container visible after a user is selected from the dropdown
    if(recipientId) {
        // Make the chat container visible
        chatContainer.style.display = 'block';

        // Set the recipient ID attribute on the send button
        sendMessageButton.setAttribute('data-recipient-id', recipientId);
    } else {
        // Hide the chat container if the placeholder is selected again
        chatContainer.style.display = 'none';

        sendMessageButton.setAttribute('data-recipient-id', '');
    }


    // Generate the new channel name based on the current and selected user IDs
    const channelName = generateChannelName(currentUserId, recipientId);

    /**
     * Listening for messages from Broadcast Name
     * Subscribing to a Channel and Listening for Events
     * DOC: https://laravel.com/docs/11.x/broadcasting#namespaces
     *
     * ChatMessages is the .Namespace\\Event\\Class :
     *  app\Events\ChatMessages.php
     */
    window.Echo.private(channelName).listen('.chat.messages', (event) => {
        // Log received event and channel name for debugging.
        // console.log('Listen: ', event);

        /*
        event.timestamp;
        event.status;
        */
        appendMessageToChat(event.message, event.sender, event.sender_id);
    });

    // Optionally, fetch and display messages for the new recipient
    fetchOldMessages(recipientId);
}

// Listen for changes on the select element
selectUser.addEventListener('change', updateRecipientId);

// Call updateRecipientId on page load to set the initial recipient ID
document.addEventListener('DOMContentLoaded', updateRecipientId);


// Event listener to load older messages when scrolling to the top of the container
document.querySelector('#messages-container').addEventListener('scroll', function() {
    if (this.scrollTop === 0 && !isLoadingOldMessages) {
        const recipientId = selectUser.value;

        fetchOldMessages(recipientId, currentPage);
    }
});

