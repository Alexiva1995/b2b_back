<?php

namespace App\Services;

use App\Models\MassMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class MassMessageService
 *
 */

 Class MassMessageService {

    /**
     * getMessageUnread
     * Get messages unread for user login
     * @param  mixed $user
     * @return void
     */
    public function getMessageUnread($user)
    {
       if(MassMessage::count() <= 0) return 0;

        $data = MassMessage::query()->whereNotExists(function($query) use ($user){
            $query->select(DB::raw(0))->from('message_read_user')
            ->whereRaw('message_read_user.mass_message_id = mass_messages.id')
            ->whereRaw("message_read_user.user_id = $user->id");
        })->count();

        return $data;
    }
}
