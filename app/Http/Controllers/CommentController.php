<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($issueId)
    {
        return Comment::with('user')
            ->where('issue_id', $issueId)
            ->latest()
            ->get();
    }

    public function store(Request $request, $issueId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'issue_id' => $issueId,
            'user_id'  => auth()->id(),
            'content'  => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
