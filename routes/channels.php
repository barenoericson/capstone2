<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel: each user listens on their own chat channel
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Presence channel: tracks who is online
Broadcast::channel('presence-users', function ($user) {
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});

// Private notifications channel: viewing requests & status updates
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Presence channel per conversation: online status + typing whispers
// Channel name: presence.conversation.{smallerId}.{largerId}
Broadcast::channel('presence.conversation.{id1}.{id2}', function ($user, $id1, $id2) {
    if ((int) $user->id === (int) $id1 || (int) $user->id === (int) $id2) {
        return [
            'id'   => $user->id,
            'name' => $user->name,
        ];
    }
    return false;
});
