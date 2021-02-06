<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CommentController\StoreRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    const PUBLISH = 'publish';

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $commentText = $request->get('comment');
        $gameId = $request->get('game_id');

        $comment = new Comment;
        $comment->comment = $commentText;
        $comment->game_id = $gameId;
        $comment->status = self::PUBLISH;
        $comment->user_id = auth()->user()->getAuthIdentifier();
        $comment->save();

        $comment['user'] = auth()->user();

        return response()->json($comment, 200);
    }

    /**
     * @param int $id
     * @param int $count
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentsByGame(int $id, int $count)
    {
        $count = $count < 101 ? $count : 1;

        $comments = Comment::where('game_id', $id)
            ->latest('created_at')
            ->with('user')
            ->paginate($count);
        return response()->json($comments, 200);
    }
}
