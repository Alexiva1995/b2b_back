<?php

namespace App\Http\Controllers;

use App\Models\MassMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MassMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = MassMessage::select('title', 'id', 'message')->with('userRead')->orderBy('id', 'DESC')->paginate(8);
        $user = User::find($request->auth_user_id);
        $listMessages = [];
        foreach ($messages as $message) {
            array_push($listMessages, [
                'id' => $message->id,
                'title' => $message->title,
                'is_read' => $user->admin == 0 ? $this->isRead($message, $user) : 1,
            ]);
        }
        return response()->json(['messages'  => [
            'data' => $listMessages,
            'current_page' => $messages->currentPage(),
            'last_page' => $messages->lastPage()
        ]], 200);
    }

    private function isRead($message, $user)
    {
       $read = DB::table('message_read_user')->where([['mass_message_id', $message->id], ['user_id', $user->id]])->first();
        if($read) return 1;
        return 0;
    }

    public function getMessage($id)
    {
        $message = MassMessage::find($id);
        return response()->json(['message' => $message], 200);
    }

    public function store(Request $request)
    {   DB::beginTransaction();
        try {
          $message =  MassMessage::create([
                'title' => $request->title,
                'message' => $request->message,
            ]);
            $message = [
                'id' => $message->id,
                'title' => $message->title,
                'is_read' => 1,
                'message' => $message->message,
            ];

            DB::commit();

            return response()->json(['message' =>$message ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();
            return response()->json('Error to Create Mass Message', 400);
        }
    }

    public function readMessage(Request $request)
    {
        $message = MassMessage::find($request->messageId);
        $message->userRead()->attach($request->messageId,['user_id' => $request->auth_user_id,'is_read' => 1]);

        return response()->json('successfull', 200);
    }

    public function destroyMessage(Request $request)
    {
        DB::beginTransaction();
        try {

            $message = MassMessage::find($request->id);
            $message->delete();
            DB::commit();
            return response()->json('success', 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json('error delete message, please contact support', 400);
        }
    }

    public function updateMessage(Request $request)
    {
        DB::beginTransaction();
        try {

            $message = MassMessage::find($request->id);
            $message->title = $request->title;
            $message->message = $request->message;
            $message->save();
            DB::commit();
            return response()->json('success', 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json('Error edit message, please contact support', 400);
        }
    }
}
