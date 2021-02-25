<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CommentController\StoreRequest;
use App\Models\Comment;
use App\Models\Game;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    const PUBLISH = 'publish';
    const NEW = 'new';

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

        //ToDo fix the code according to principle 1 of SOLID
        if ($type === 'game') {
            $game = Game::where('id', $id)->with([
                    'firstTeam.userFa13email.user',
                    'secondTeam.userFa13email.user',
                ])
                ->firstOrFail()
                ->toArray();

            $toUserFirstTeam = $game['first_team']['user_fa13email']['user_id'];
            $toUserSecondTeam = $game['second_team']['user_fa13email']['user_id'];

            if ($toUserFirstTeam) {
                $notification = new Notification();
                $notification->game_id = $id;
                $notification->from_user_id = auth()->user()->getAuthIdentifier();
                $notification->to_user_id = $toUserFirstTeam;
                $notification->status = self::NEW;
                $notification->save();
            }

            if ($toUserSecondTeam) {
                $notification = new Notification();
                $notification->game_id = $id;
                $notification->from_user_id = auth()->user()->getAuthIdentifier();
                $notification->to_user_id = $toUserSecondTeam;
                $notification->status = self::NEW;
                $notification->save();
            }
        }

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
