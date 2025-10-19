<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function issueReport(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        $query = Issue::with(['project.team', 'assignee']); // eager load project & team

        // Role-based access
        if ($role === 'admin') {
            // admin sees all issues
        } elseif (in_array($role, ['pm', 'teamlead'])) {
            // PM/TeamLead sees their team projects
            $query->whereHas('project', fn($q) => $q->where('team_id', $user->team_id));
        } else {
            // Developer/support sees only their own assigned issues
            $query->where('assignee_id', $user->id);
        }

        // Optional filters
        if ($request->status) $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->project_id) $query->where('project_id', $request->project_id);
        if ($request->from) $query->whereDate('created_at', '>=', $request->from);
        if ($request->to) $query->whereDate('created_at', '<=', $request->to);

        $issues = $query->orderBy('created_at', 'desc')->get();

        return response()->json($issues);
    }
}
