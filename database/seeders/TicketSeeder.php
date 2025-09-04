<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::factory()->count(25)->create();

        // Ensure some with notes and mixed statuses
        Ticket::factory()->count(5)->create([
            'status' => 'open',
            'note' => 'Follow up with the customer next week.',
        ]);
    }
}
