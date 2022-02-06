<?php

namespace App\Http\Controllers\V1\Messages;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $messages = $user->sent_messages;

        return ResponseHelper::successWithMessageAndData('Messages fetched successfully', $messages);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
           'subject' => 'required',
           'body' => 'required',
           'sender_id' => 'required|exists:users,id',
           'receiver_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        if ($request->get('sender_id') != Auth::id() || $request->get('receiver_id') == Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $create = Message::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Message not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Message created successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $message = Message::whereId($id)->where(function ($query){
                return $query->whereHas('sender',function ($query){
                    return $query->where('id', Auth::id());
                })->orWhereHas('receiver', function ($query){
                    return $query->where('id', Auth::id());
                });
        })->with('replies')->first();
        if (!$message){
            return ResponseHelper::errorWithMessage('Message not found', NOT_FOUND);
        }

        if ($message->read_at == null && $message->receiver_id == Auth::id()){
            $message->update([
               'read_at' => now()
            ]);
        }

        return ResponseHelper::successWithMessageAndData('Message fetched successfully', $message);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'subject' => 'required',
            'body' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $update = Message::whereId($id)->whereHas('sender',function ($query){
            return $query->where('id',Auth::id());
        })->update($validator->validated());

        if (!$update){
            return ResponseHelper::errorWithMessage('Message not updated', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Message Updated Successfully');
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $message = Message::whereId($id);
        if (!$message->first()){
            return ResponseHelper::errorWithMessage('Message not found', NOT_FOUND);
        }

        $delete = $message->whereHas('sender',function ($query){
            return $query->where('id',Auth::id());
        })->delete();

        if (!$delete){
            return ResponseHelper::errorWithMessage('Message not deleted', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Message deleted successfully');
    }

    public function createReply(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'body' => 'required',
            'message_id' => 'required|exists:messages,id',
            'sender_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        if ($request->get('sender_id') != Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $message = Message::find($request->message_id);
        if (($message->receiver_id != Auth::id() || $message->sender_id != Auth::id()) && $request->sender_id != Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $create = Reply::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Reply not sent', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Reply sent successfully');
    }

    public function updateReply(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'body' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $reply = Reply::find($id);

        if (!$reply){
            return ResponseHelper::errorWithMessage('Reply not found', NOT_FOUND);
        }

        $update = $reply->whereHas('sender',function ($query){
            return $query->where('id',Auth::id());
        })->update($validator->validated());

        if (!$update){
            return ResponseHelper::errorWithMessage('Reply not updated', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Reply Updated Successfully');
    }

    public function deleteReply($id): \Illuminate\Http\JsonResponse
    {
        $reply = Reply::whereId($id)->whereHas('sender',function ($query){
            return $query->where('id',Auth::id());
        })->first();
        if (!$reply){
            return ResponseHelper::errorWithMessage('Reply not found', NOT_FOUND);
        }

        $delete = $reply->delete();
        if (!$delete){
            return ResponseHelper::errorWithMessage('Reply not deleted', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Reply deleted successfully');
    }
}
