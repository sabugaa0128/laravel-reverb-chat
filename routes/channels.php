<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/

/**
 * Authorizing Channels
 * DOC: https://laravel.com/docs/11.x/broadcasting#example-application-authorizing-channels
 */
Broadcast::channel('chat-channel.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
