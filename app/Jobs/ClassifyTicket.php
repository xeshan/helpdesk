<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TicketClassifier $classifier): void
    {
        $ticket = $this->ticket->fresh();
        if (!$ticket) return;

        $result = $classifier->classify($ticket);

        // Preserve manual category if already overridden
        $newCategory = $ticket->category_source === 'manual' ? $ticket->category : $result['category'];

        $ticket->forceFill([
            'category' => $newCategory,
            'explanation' => $result['explanation'],
            'confidence' => $result['confidence'],
            'category_source' => $ticket->category_source === 'manual' ? 'manual' : 'ai',
        ])->save();
    }
}
