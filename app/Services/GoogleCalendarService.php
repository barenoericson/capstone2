<?php

namespace App\Services;

use App\Models\User;
use App\Models\Viewing;
use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;

class GoogleCalendarService
{
    private Google_Client $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl(?int $userId = null): string
    {
        if ($userId) {
            $state = encrypt($userId);
            $this->client->setState($state);
        }

        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): array
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function setUserToken(User $user): void
    {
        $this->client->setAccessToken($user->google_access_token);

        if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->update([
                'google_access_token'    => json_encode($newToken),
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
            ]);
        }
    }

    public function createViewingEvent(User $user, Viewing $viewing): ?string
    {
        $this->setUserToken($user);
        $service = new Google_Service_Calendar($this->client);

        $viewing->loadMissing(['property', 'buyer', 'agent.user']);

        $startTime = $viewing->viewing_date->format('Y-m-d') . 'T'
            . (is_string($viewing->viewing_time)
                ? $viewing->viewing_time . ':00'
                : $viewing->viewing_time->format('H:i:00'));

        $endDateTime = \Carbon\Carbon::parse($startTime)->addHour();

        $event = new Google_Service_Calendar_Event([
            'summary'     => 'Property Viewing - ' . $viewing->property->title,
            'location'    => trim(($viewing->property->address ?? '') . ', ' . ($viewing->property->city ?? ''), ', '),
            'description' => "Viewing for: {$viewing->property->title}\n"
                . "Buyer: {$viewing->buyer->name}\n"
                . "Agent: " . ($viewing->agent->user->name ?? 'N/A') . "\n"
                . ($viewing->buyer_notes ? "Notes: {$viewing->buyer_notes}" : ''),
            'start'       => [
                'dateTime' => $startTime,
                'timeZone' => 'Asia/Manila',
            ],
            'end'         => [
                'dateTime' => $endDateTime->format('Y-m-d\TH:i:00'),
                'timeZone' => 'Asia/Manila',
            ],
            'reminders'   => [
                'useDefault' => false,
                'overrides'  => [
                    ['method' => 'popup', 'minutes' => 60],
                ],
            ],
        ]);

        $calendarId = $user->google_calendar_id ?? 'primary';
        $createdEvent = $service->events->insert($calendarId, $event);

        return $createdEvent->getId();
    }
}
