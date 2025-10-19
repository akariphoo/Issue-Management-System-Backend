<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'users' => User::count(),
            'teams' => Team::count(),
            'projects' => Project::count(),
            'open_issues' => Issue::where('status', 'open')->count(),
        ]);
    }

    public function recentIssues()
    {
        $issues = Issue::with('assignee')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($issues);
    }
}
