<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    const NEW = 'new';
    const OLD = 'old';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $notifications = Notification::where([
                ['to_user_id', '=', auth()->user()->getAuthIdentifier()],
                ['status', '=', self::NEW]
            ])
            ->with(['userFrom', 'game.tournament'])
            ->get();

        return response()->json($notifications, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        //ToDo updates are possible by id notification
        Notification::where('to_user_id', auth()->user()->getAuthIdentifier())
            ->update(['status' => self::OLD]);

        return response()->json('success', 200);
    }
}
