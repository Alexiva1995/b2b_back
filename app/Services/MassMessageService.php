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

       $data = DB::table('mass_messages')->select(
        DB::raw('count(*) as messages_count')
       )->whereNotExists(function ($query) use ($user){
        $query->select(DB::raw(1))
        ->from('users')->join('message_read_user','mass_messages.id', '=', 'message_read_user.mass_message_id')
        ->where('message_read_user.user_id', $user->id);
       })->get('messages_count')->first();

        return $data->messages_count;
    }
}
