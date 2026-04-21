<?php

namespace App\Console\Commands;

use App\Models\CheckoutRequest;
use App\Notifications\ItemStatusNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-due-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due date notifications for checkout requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $weekFromNow = Carbon::today()->addDays(7);

        $requests = CheckoutRequest::with(['user', 'equipment'])
            ->whereIn('status', ['approved', 'active', 'overdue'])
            ->get();

        foreach ($requests as $request) {
            if (! $request->user || ! $request->equipment || ! $request->end_date) {
                continue;
            }

            $dueDate = Carbon::parse($request->end_date)->startOfDay();

            if ($dueDate->equalTo($weekFromNow)) {
                $request->user->notify(new ItemStatusNotification($request->equipment, 'due_week'));
                $this->info("Sent 7-day reminder for request ID {$request->id}");
            } elseif ($dueDate->equalTo($today)) {
                $request->user->notify(new ItemStatusNotification($request->equipment, 'due_today'));
                $this->info("Sent due-today reminder for request ID {$request->id}");
            } elseif ($dueDate->lt($today)) {
                $request->update(['status' => 'overdue']);
                $request->user->notify(new ItemStatusNotification($request->equipment, 'overdue'));
                $this->info("Sent overdue notification for request ID {$request->id}");
            }
        }

        return self::SUCCESS;
    }
}
