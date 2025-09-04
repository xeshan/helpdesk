<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Validation\Rule;
use App\Jobs\ClassifyTicket;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }
        if ($search = $request->string('q')->toString()) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%$search%")
                  ->orWhere('body', 'like', "%$search%");
            });
        }

        $perPage = min(max((int) $request->integer('per_page', 10), 5), 100);
        $tickets = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($tickets);
    }
     public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required','string','max:255'],
            'body' => ['required','string'],
            'status' => ['nullable', Rule::in(['open','in_progress','resolved','closed'])],
        ]);

        $ticket = Ticket::create(array_merge([
            'status' => 'open',
        ], $validated));

        return response()->json($ticket, 201);
    }

    public function show(string $id)
    {
        return Ticket::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'status' => [Rule::in(['open','in_progress','resolved','closed'])],
            'category' => ['nullable','string','max:100'],
            'note' => ['nullable','string'],
        ]);

        if (array_key_exists('category', $validated)) {
            // manual override
            $ticket->category = $validated['category'];
            $ticket->category_source = 'manual';
        }

        if (array_key_exists('status', $validated)) {
            $ticket->status = $validated['status'];
        }

        if (array_key_exists('note', $validated)) {
            $ticket->note = $validated['note'];
        }

        $ticket->save();

        return response()->json($ticket);
    }

    public function classify(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        ClassifyTicket::dispatch($ticket);
        return response()->json(['queued' => true]);
    }
}
