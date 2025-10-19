<?php

namespace App\Http\Controllers;

use App\Models\ProjectUser;
use Illuminate\Http\Request;

class ProjectUserController extends Controller
{
    public function getAllProjectUsers()
    {
        $projectUsers = ProjectUser::with(['project', 'user'])->get();
        return response()->json($projectUsers);
    }

    // Create
    public function createProjectUser(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id'    => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'required|in:partial,delegated',
        ]);

        $projectUser = ProjectUser::create($validated);

        return response()->json($projectUser, 201);
    }

    // Show one
    public function showProjectUser($id)
    {
        $projectUser = ProjectUser::findOrFail($id);

        return response()->json($projectUser->load(['project', 'user']));
    }

    // Update
    public function updateProjectUser(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'nullable|in:partial,delegated',
        ]);

        $projectUser = ProjectUser::findOrFail($id);
        $projectUser->update($validated);

        return response()->json($projectUser);
    }

    // Delete
    public function deleteProjectUser($id)
    {
        $projectUser = ProjectUser::findOrFail($id);
        $projectUser->delete();

        return response()->json(null, 204);
    }
}
