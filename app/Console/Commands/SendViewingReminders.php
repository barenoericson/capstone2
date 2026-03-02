<?php

namespace App\Console\Commands;

use App\Mail\ViewingReminder;
use App\Models\Viewing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendViewingReminders extends Command
{
    protected $signature = 'viewings:send-reminders';
    protected $description = 'Send 24-hour reminder emails for upcoming approved viewings';

    public function handle(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $viewings = Viewing::where('status', Viewing::STATUS_APPROVED)
            ->where('viewing_date', $tomorrow)
            ->where('reminder_sent', false)
            ->with(['property', 'buyer', 'agent.user'])
            ->get();

        foreach ($viewings as $viewing) {
            try {
                Mail::to($viewing->buyer->email)->send(new ViewingReminder($viewing, 'buyer'));
            } catch (\Exception $e) {
                Log::warning("Reminder email to buyer failed (viewing {$viewing->id}): " . $e->getMessage());
            }

            try {
                Mail::to($viewing->agent->user->email)->send(new ViewingReminder($viewing, 'agent'));
            } catch (\Exception $e) {
                Log::warning("Reminder email to agent failed (viewing {$viewing->id}): " . $e->getMessage());
            }

            $viewing->update([
                'reminder_sent'    => true,
                'reminder_sent_at' => now(),
            ]);
        }

        $this->info("Sent reminders for {$viewings->count()} viewings.");
    }
}
