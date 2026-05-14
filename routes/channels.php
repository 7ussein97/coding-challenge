<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('judges', function ($user) {
    return $user->isJudge() || $user->isAdmin();
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->isAdmin() || ($user->team && (int) $user->team->id === (int) $teamId);
});
