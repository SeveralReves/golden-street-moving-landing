<?php

namespace App\Console\Commands;

use App\Services\MoveNotifier;
use Illuminate\Console\Command;

class SendMoveReminders extends Command
{
    protected $signature = 'moves:send-reminders';

    protected $description = 'Email customers and the owner a reminder for moves starting within the next 24 hours';

    public function handle(MoveNotifier $notifier): int
    {
        $this->info("Reminders sent: {$notifier->sendDueReminders()}");

        return self::SUCCESS;
    }
}
