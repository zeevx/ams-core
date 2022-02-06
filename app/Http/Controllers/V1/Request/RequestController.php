<?php

namespace App\Http\Controllers\V1\Request;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\RequestCategory;
use App\Models\RequestReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $complaints = $user->requests;
        return ResponseHelper::successWithMessageAndData('Requests fetched successfully', $complaints);
    }

    public function categories(): \Illuminate\Http\JsonResponse
    {
        $categories = RequestCategory::all();
        return ResponseHelper::successWithMessageAndData('Categories fetched successfully', $categories);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'subject' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:request_categories,id',
            'user_id' => 'required',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        if ($request->get('user_id') != Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $create = \App\Models\Request::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Request not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Request created successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $complaint = \App\Models\Request::whereId($id)->where(function ($query){
            return$query->where('user_id', Auth::id());
        })->first();

        if (!$complaint){
            return ResponseHelper::errorWithMessage('Requests not found', NOT_FOUND);
        }

        return ResponseHelper::successWithMessageAndData('Requests fetched successfully', $complaint);
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

        $complaint = \App\Models\Request::find($id);
        if (!$complaint){
            return ResponseHelper::errorWithMessage('Requests not found', NOT_FOUND);
        }

        $updated = $complaint->update($validator->validated());
        if (!$updated){
            return ResponseHelper::errorWithMessage('Requests not updated', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Requests updated successfully');

    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $complaint = \App\Models\Request::find($id);
        if (!$complaint){
            return ResponseHelper::errorWithMessage('Requests not found', NOT_FOUND);
        }

        $delete = $complaint->delete;
        if (!$delete){
            return ResponseHelper::errorWithMessage('Requests not deleted', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Requests deleted successfully');

    }

    public function createReply(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'body' => 'required',
            'complaint_id' => 'required|exists:complaints,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        if ($request->get('user_id') != Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $create = RequestReply::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Requests reply not sent', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Requests reply sent successfully');
    }

    public function updateReply(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'body' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $reply = RequestReply::find($id);

        if (!$reply){
            return ResponseHelper::errorWithMessage('Requests reply not found', NOT_FOUND);
        }

        $update = $reply->whereHas('user',function ($query){
            return $query->where('id',Auth::id());
        })->update($validator->validated());

        if (!$update){
            return ResponseHelper::errorWithMessage('Requests reply not updated', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Requests reply Updated Successfully');
    }

    public function deleteReply($id): \Illuminate\Http\JsonResponse
    {
        $reply = RequestReply::whereId($id)->whereHas('user',function ($query){
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
