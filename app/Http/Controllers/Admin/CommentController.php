<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;

class CommentController extends Controller {
    public function index() {
        $pageTitle = 'Comments';
        $comments  = Comment::searchable(['project:title'])->withCount('allReplies')->with('project')->where('comment_id', null)->orderBy('seen', 'asc')->paginate(getPaginate());
        return view('admin.comment.index', compact('pageTitle', 'comments'));
    }

    public function detail($id) {
        $pageTitle     = 'Comments Detail';
        $comment       = Comment::with(['project', 'user', 'allReplies'])->findOrFail($id);
        $comment->seen = Status::YES;
        $comment->save();
        return view('admin.comment.detail', compact('pageTitle', 'comment'));
    }

    public function store(Request $request, $id, $comment_id) {
        $request->validate([
            'comment' => 'required',
        ]);

        $project = Project::confirmed()->find($id);
        if (!$project) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Invalid project',
            ]);
        }

        $replayComment = Comment::find($comment_id);
        if (!$replayComment) {
            $notify[] = ['error', 'Invalid comment'];
            return back()->withNotify($notify);
        }

        $replayComment->updated_at = now();
        $replayComment->save();

        $comment             = new Comment();
        $comment->comment    = $request->comment;
        $comment->project_id = $id;
        $comment->admin_id   = auth()->guard('admin')->user()->id;
        $comment->comment_id = $comment_id;
        $comment->save();

        $notify[] = ['success', 'Replay placed successfully'];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Comment::changeStatus($id);
    }
}
