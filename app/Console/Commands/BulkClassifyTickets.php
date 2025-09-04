<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Jobs\ClassifyTicket;

class BulkClassifyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:bulk-classify-tickets';
    protected $signature = 'tickets:bulk-classify {--all : Classify all tickets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch queued AI classification for tickets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Ticket::query();
        if (!$this->option('all')) {
            $query->whereNull('explanation');
        }
        $count = 0;
        $query->chunkById(100, function($tickets) use (&$count) {
            foreach ($tickets as $t) {
                ClassifyTicket::dispatch($t);
                $count++;
            }
        });
        $this->info("Queued classification jobs: {$count}");
        return self::SUCCESS;
    }
}
