<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class StatsController extends Controller
{
    public function index()
    {
        $perStatus = Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count','status');

        $perCategory = Ticket::selectRaw('COALESCE(category, "uncategorized") as category, COUNT(*) as count')
            ->groupBy('category')->pluck('count','category');

        return [
            'perStatus' => $perStatus,
            'perCategory' => $perCategory,
            'total' => Ticket::count(),
        ];
    }
}
