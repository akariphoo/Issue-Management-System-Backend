<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    public function getAllIssues()
    {
        return Issue::with(['project', 'assignee', 'reporter'])->get();
    }

    public function showIssue($id)
    {
        $issue = Issue::with(['reporter', 'assignee', 'project'])->findOrFail($id);
        $user = auth()->user();

        return response()->json([
            'issue' => $issue,
            'userRole' => $user->role, // <- here
        ]);
    }

    public function createIssue(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,in_review,resolved,closed',
            'project_id' => 'required|exists:projects,id',
            'reporter_id' => 'required|exists:users,id',
            'assignee_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        $issue = Issue::create($data);

        if ($issue->assignee_id) {
            Notification::create([
                'user_id' => $issue->assignee_id,
                'issue_id' => $issue->id,
                'type' => 'issue_assigned',
                'message' => "You have been assigned to issue: {$issue->title}"
            ]);
        }

        return $issue;
    }

    public function updateIssue(Request $request, $id)
    {
        $issue = Issue::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,in_review,resolved,closed',
            'project_id' => 'required|exists:projects,id',
            'assignee_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        if (in_array($data['status'], ['resolved', 'closed'])) {
            $data['resolved_at'] = now();
        }

        $issue->update($data);

        if ($issue->assignee_id) {
            Notification::create([
                'user_id' => $issue->assignee_id,
                'issue_id' => $issue->id,
                'type' => 'issue_assigned',
                'message' => "You have been assigned to issue: {$issue->title}"
            ]);
        }

        return $issue;
    }

    public function deleteIssue($id)
    {
        $issue = Issue::findOrFail($id);
        $issue->delete();
        return response()->json(['message' => 'Issue deleted']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,in_review,resolved,closed',
        ]);

        $issue = Issue::findOrFail($id);

        $user = auth()->user();
        if (!in_array($user->role, ['pm', 'teamlead', 'senior-developer', 'mid-developer', 'junior-developer', 'admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $issue->status = $request->status;

        if ($request->status === 'resolved') {
            $issue->resolved_at = now();
        } else {
            $issue->resolved_at = null;
        }

        $issue->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'issue' => $issue
        ]);
    }

    public function myIssues(Request $request)
    {
        $userId = $request->user()->id;

        $issues = Issue::with(['project', 'assignee'])
            ->where('assignee_id', $userId)
            ->get();

        return response()->json($issues);
    }

    public function teamIssues()
    {
        $user = Auth::user();

        // Only allow PM or Team Lead
        if (!in_array($user->role, ['pm', 'teamlead'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get team members (excluding self)
        $teamMembers = User::where('team_id', $user->team_id)->pluck('id');

        // Get issues assigned to team members
        $issues = Issue::with(['assignee', 'project'])
            ->whereIn('assignee_id', $teamMembers)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($issues);
    }
}
