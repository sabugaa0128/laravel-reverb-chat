<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Events\ChatMessages;
use Illuminate\Support\Facades\Crypt;

class ChatsController extends Controller
{

    /**
     * Stores a new chat message in the database and broadcasts it to the recipient.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'message' => 'required|string|max:500',
            'recipient_id' => 'required|integer',
        ];

        // Perform validation manually to customize the response
        $validator = \Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Check specifically for message length error
            if ($validator->errors()->has('message') && strlen($request->message) > 500) {
                return response()->json(['error' => 'Message cannot exceed 500 characters.'], 422);
            }
            // Handle other validation errors
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $user = auth()->user();

            $messageContent = $request->message;
            $messageContentEncrypted = Crypt::encryptString($messageContent);
            $recipientId = $request->recipient_id;

            $chatMessage  = new Chat();
            $chatMessage ->user_id = $user->id;
            $chatMessage ->recipient_id = $recipientId;
            $chatMessage ->message = $messageContentEncrypted;
            $chatMessage ->save();

            event(new ChatMessages($user, $chatMessage, $recipientId));

            return response()->json(['success' => true, 'message' => $messageContent, 'sender' => $user->name]);
        } catch (\Exception $e) {
            \Log::error('Error saving chat message: ' . $e->getMessage());

            return response()->json(['error' => 'An error occurred while saving the message'], 500);
        }

    }

    /**
     * Retrieves chat messages between the current user and the specified recipient.
     *
     * Fetches paginated chat messages involving the current user and the recipient,
     * ordered by creation time in descending order. Transforms each message to include
     * sender information before returning them in a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $recipientId  The recipient's user ID.
     */
    public function index(Request $request, $recipientId)
    {
        if(!$recipientId){
            return;
        }

        $currentUserId = auth()->id();

        try {
            $messages = Chat::with('user')
                            ->where(function ($query) use ($currentUserId, $recipientId) {
                                $query->where('user_id', $currentUserId)
                                    ->where('recipient_id', $recipientId);
                            })
                            ->orWhere(function ($query) use ($currentUserId, $recipientId) {
                                $query->where('user_id', $recipientId)
                                    ->where('recipient_id', $currentUserId);
                            })
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

            $messages->getCollection()->transform(function ($chat) {
                $message = $chat->message ?? null;
                if($message){

                    try {
                        $messageDecripted = Crypt::decryptString($message);
                    } catch (\Exception $e) {
                        \Log::error("Decryption error: " . $e->getMessage());

                        $messageDecripted = "Message cannot be decrypted.";
                    }
                    return [
                        'message' => $messageDecripted,
                        'sender_name' => $chat->user->name,
                        'sender_id' => $chat->user_id,
                    ];
                }

            });

            return response()->json($messages);

        } catch (\Exception $e) {
            \Log::error("Error fetching messages: " . $e->getMessage());

            return response()->json(['error' => 'An error occurred while fetching messages'], 500);
        }
    }


    public function getUsers(Request $request) {
        $currentUserId = auth()->id();

        // Retrieve all users except the current user
        $users = User::where('id', '!=', $currentUserId)
                     ->get(['id', 'name']);

        // Convert the users to an array suitable for the select options
        // where the user's ID is the key, and the user's name is the value
        $options = $users->pluck('name', 'id')->toArray();

        // Get the selected user ID from the request, if any
        $selectedOption = $request->input('user_id');

        return view('dashboard', compact('options', 'selectedOption'));
    }




}
