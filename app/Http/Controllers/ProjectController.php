<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function getAllProjects()
    {
        $projects = Project::with('team')->get();

        return response()->json(['projects' => $projects]);
    }

    public function createProject(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'description'     => 'nullable|string|max:255',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        $project = Project::create([
            'name'     => $validated['name'],
            'description'    => $validated['description'],
            'team_id'  => $validated['team_id'],
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project'    => $project,
        ], 201);
    }

    public function showProject($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $project->update($validated);

        return response()->json(['message' => 'Project updated successfully', 'project' => $project]);
    }

    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
