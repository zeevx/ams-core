<?php

namespace App\Http\Controllers\V1\Notice;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeCategory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $notices = Notice::where('user_id',Auth::id())->orWhere('user_id',0)->get();

        return ResponseHelper::successWithMessageAndData('Notices fetched successfully', $notices);
    }

    public function categories(): \Illuminate\Http\JsonResponse
    {
        $categories = NoticeCategory::all();
        return ResponseHelper::successWithMessageAndData('Categories returned successfully', $categories);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
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

        $create = Notice::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Notice not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Notice created successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $notice = Notice::whereId($id)->where(function ($query){
           return$query->where('user_id', Auth::id())->orWhere('user_id', 0);
        })->first();

        if (!$notice){
            return ResponseHelper::errorWithMessage('Notice not found', NOT_FOUND);
        }

        if ($notice->read_at == null && $notice->user_id == Auth::id()){
            $notice->update([
                'read_at' => now()
            ]);
        }

        return ResponseHelper::successWithMessageAndData('Notice fetched successfully', $notice);
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

        $notice = Notice::find($id);
        if (!$notice){
            return ResponseHelper::errorWithMessage('Notice not found', NOT_FOUND);
        }

        $updated = $notice->update($validator->validated());
        if (!$updated){
            return ResponseHelper::errorWithMessage('Notice not updated', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Notice updated successfully');

    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $notice = Notice::find($id);
        if (!$notice){
            return ResponseHelper::errorWithMessage('Notice not found', NOT_FOUND);
        }

        $delete = $notice->delete;
        if (!$delete){
            return ResponseHelper::errorWithMessage('Notice not deleted', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Notice deleted successfully');

    }
}
