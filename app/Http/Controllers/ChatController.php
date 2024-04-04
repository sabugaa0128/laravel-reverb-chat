<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;

class ChatController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:191',
            'recipient_id' => 'required|integer',
        ]);

        $message = new Chat();
        $message->user_id = auth()->id();
        $message->message = $request->message;
        $message->recipient_id = $request->recipient_id;
        $message->save();

        return response()->json(['success' => true, 'message' => $request->message]);
    }


}
