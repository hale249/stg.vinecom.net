<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;

class CommentController extends Controller {
    public function comment(Request $request, $id, $comment_id = null) {
        $request->validate([
            'comment' => 'required',
        ]);

        $project = Project::active()->confirmed()->find($id);
        if (!$project) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Invalid project',
            ]);
        }

        if ($comment_id && auth()->check()) {
            $replayComment = Comment::active()->where('user_id', auth()->id())->find($comment_id);
            if (!$replayComment) {
                return response()->json([
                    'type'    => 'error',
                    'message' => 'You are not allowed to reply to this comment',
                ]);
            }

            $replayComment->seen       = Status::NO;
            $replayComment->updated_at = now();
            $replayComment->save();
        }

        $comment             = new Comment();
        $comment->comment    = $request->comment;
        $comment->project_id = $id;
        $comment->user_id    = auth()->id();
        $comment->comment_id = $comment_id ?? null;
        $comment->save();
        $notifyMessage = $comment_id ? 'Replay placed successfully' : 'Comment placed successfully';

        if ($comment_id) {
            $replay = $comment;
            $data   = view('Template::partials.replay_comment', compact('replay'))->render();
        } else {
            $data = view('Template::partials.single_comment', compact('comment', 'project'))->render();
        }

        return response()->json([
            'comment' => $comment_id ? false : true,
            'type'    => 'success',
            'message' => $notifyMessage,
            'data'    => $data,
        ]);
    }
}
