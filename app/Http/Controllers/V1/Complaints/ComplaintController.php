<?php

namespace App\Http\Controllers\V1\Complaints;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $complaints = $user->complaints;
        return ResponseHelper::successWithMessageAndData('Complaints fetched successfully', $complaints);
    }

    public function categories(): \Illuminate\Http\JsonResponse
    {
        $categories = ComplaintCategory::all();
        return ResponseHelper::successWithMessageAndData('Categories fetched successfully', $categories);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'subject' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:notice_categories,id',
            'user_id' => 'required',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        if ($request->get('user_id') != Auth::id()){
            return ResponseHelper::errorWithMessage('You can not do this', UNPROCESSABLE_ENTITY);
        }

        $create = Complaint::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Complaint not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Complaint created successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $complaint = Complaint::whereId($id)->where(function ($query){
            return$query->where('user_id', Auth::id());
        })->first();

        if (!$complaint){
            return ResponseHelper::errorWithMessage('Complaint not found', NOT_FOUND);
        }

        return ResponseHelper::successWithMessageAndData('Complaint fetched successfully', $complaint);
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

        $complaint = Complaint::find($id);
        if (!$complaint){
            return ResponseHelper::errorWithMessage('Complaint not found', NOT_FOUND);
        }

        $updated = $complaint->update($validator->validated());
        if (!$updated){
            return ResponseHelper::errorWithMessage('Complaint not updated', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Complaint updated successfully');

    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $complaint = Complaint::find($id);
        if (!$complaint){
            return ResponseHelper::errorWithMessage('Complaint not found', NOT_FOUND);
        }

        $delete = $complaint->delete;
        if (!$delete){
            return ResponseHelper::errorWithMessage('Complaint not deleted', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Complaint deleted successfully');

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

        $create = ComplaintReply::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Complaint reply not sent', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Complaint reply sent successfully');
    }

    public function updateReply(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'body' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $reply = ComplaintReply::find($id);

        if (!$reply){
            return ResponseHelper::errorWithMessage('Complaint reply not found', NOT_FOUND);
        }

        $update = $reply->whereHas('user',function ($query){
            return $query->where('id',Auth::id());
        })->update($validator->validated());

        if (!$update){
            return ResponseHelper::errorWithMessage('Complaint reply not updated', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Complaint reply Updated Successfully');
    }

    public function deleteReply($id): \Illuminate\Http\JsonResponse
    {
        $reply = ComplaintReply::whereId($id)->whereHas('user',function ($query){
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
