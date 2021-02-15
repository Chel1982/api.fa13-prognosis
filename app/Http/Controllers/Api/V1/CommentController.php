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
        $type = $request->get('type');
        $id = $request->get('id');

        $comment = new Comment;
        $comment->comment = $commentText;
        if ($type === 'game') $comment->game_id = $id;
        if ($type === 'tournament') $comment->tournament_id = $id;
        $comment->status = self::PUBLISH;
        $comment->user_id = auth()->user()->getAuthIdentifier();
        $comment->save();

        $comment['user'] = auth()->user();

        return response()->json($comment, 200);
    }

    /**
     * @param int $id
     * @param int $count
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $type, int $id, int $count)
    {
        $count = $count < 101 ? $count : 1;

        $comments = Comment::where($type . '_id', $id)
            ->latest('created_at')
            ->with('user')
            ->paginate($count);
        return response()->json($comments, 200);
    }
}
