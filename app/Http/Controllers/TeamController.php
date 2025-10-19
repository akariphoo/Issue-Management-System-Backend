<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function getAllTeams()
    {
        $teams = Team::get();

        return response()->json(['teams' => $teams]);
    }

    public function createTeam(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'team'    => $team,
        ], 201);
    }

    public function showTeam($id)
    {
        $team = Team::findOrFail($id);
        return response()->json($team);
    }

    public function updateTeam(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $team->update($validated);

        return response()->json(['message' => 'Team updated successfully', 'team' => $team]);
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully']);
    }
}
