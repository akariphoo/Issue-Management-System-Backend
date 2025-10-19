<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function stats(Request $request)
    {
       $role = $request->user()->role;

    $stats = [];

    if ($role === 'admin') {
        $stats = [
            'users'    => User::count(),
            'teams'    => Team::count(),
            'projects' => Project::count(),
            'issues'   => Issue::count(),
        ];
    } elseif (in_array($role, ['pm', 'teamlead'])) {
        // Count issues assigned to self
        $myIssuesCount = Issue::where('assignee_id', $request->user()->id)->count();

        // Count team issues via project -> team_id
        $teamIssuesCount = Issue::whereHas('project', function($q) use ($request) {
            $q->where('team_id', $request->user()->team_id);
        })->count();

        $stats = [
            'projects'    => Project::count(),
            'issues'      => $myIssuesCount,
            'team_issues' => $teamIssuesCount,
        ];
    } else {
        $stats = [
            'projects' => Project::count(),
            'issues'   => Issue::where('assignee_id', $request->user()->id)->count(),
        ];
    }

    return response()->json($stats);
    }
}
