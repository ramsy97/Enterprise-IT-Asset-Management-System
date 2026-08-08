<?php

namespace App\Jobs;

use App\Models\AssetAssignment;
use App\Notifications\AssignmentStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAssignmentNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AssetAssignment $assignment,
        public string $action,
    ) {}

    public function handle(): void
    {
        $assignment = $this->assignment->load('employee');

        if ($assignment->employee) {
            $assignment->employee->notify(new AssignmentStatusNotification($assignment, $this->action));
        }
    }
}
