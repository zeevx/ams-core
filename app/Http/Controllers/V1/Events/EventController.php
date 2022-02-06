<?php

namespace App\Http\Controllers\V1\Events;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Czemu\NovaCalendarTool\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $events = Event::all();
        return ResponseHelper::successWithMessageAndData('Events returned successfully', $events);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $data['start'] = date($request->get('start'));
        $data['end'] = date($request->get('end'));

        $create = Event::create($data);

        if (!$create){
            return ResponseHelper::errorWithMessage('Event not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Event created successfully');
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'start' => 'required',
            'end' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $event = Event::find($id);

        if (!$event){
            return ResponseHelper::errorWithMessage('Event not found', NOT_FOUND);
        }

        $data = $validator->validated();
        $data['start'] = date($request->get('start'));
        $data['end'] = date($request->get('end'));
        $updated = $event->update($data);

        if (!$updated){
            return ResponseHelper::errorWithMessage('Event not updated', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Event updated successfully');
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $event = Event::find($id);

        if (!$event){
            return ResponseHelper::errorWithMessage('Event not found', NOT_FOUND);
        }

        $delete = $event->delete();

        if (!$delete){
            return ResponseHelper::errorWithMessage('Event not deleted', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Event deleted successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $event = Event::find($id);

        if (!$event){
            return ResponseHelper::errorWithMessage('Event not found', NOT_FOUND);
        }

        return ResponseHelper::successWithMessageAndData('Event fetched successfully', $event);
    }
}
