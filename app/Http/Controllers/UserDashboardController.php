<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
     // Return stats for the logged-in user
    public function stats()
    {
        $userId = Auth::id();

        $stats = [
            'open'        => Issue::where('assignee_id', $userId)->where('status', 'open')->count(),
            'in_progress' => Issue::where('assignee_id', $userId)->where('status', 'in_progress')->count(),
            'resolved'    => Issue::where('assignee_id', $userId)->where('status', 'resolved')->count(),
        ];

        return response()->json($stats);
    }

    // Return recent issues assigned to the user
    public function recentIssues()
    {
        $userId = Auth::id();

        $issues = Issue::with(['project:id,name']) // load project name
            ->where('assignee_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return response()->json($issues);
    }
}
