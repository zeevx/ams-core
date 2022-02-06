<?php

namespace App\Http\Controllers\V1\Invoice;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $invoices = $user->invoices;
        return ResponseHelper::successWithMessageAndData('Invoices fetched successfully', $invoices);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $create = Invoice::create($validator->validated());

        if (!$create){
            return ResponseHelper::errorWithMessage('Invoice not created', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Invoice created successfully');
    }

    public function view($id): \Illuminate\Http\JsonResponse
    {
        $invoice = Invoice::whereId($id)->with(['items'])->first();

        if (!$invoice){
            return ResponseHelper::errorWithMessage('Invoice not found', NOT_FOUND);
        }

        return ResponseHelper::successWithMessageAndData('Invoice fetched successfully', $invoice);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $invoice = Invoice::find($id);
        if (!$invoice){
            return ResponseHelper::errorWithMessage('Invoice not found', NOT_FOUND);
        }

        $updated = $invoice->update($validator->validated());
        if (!$updated){
            return ResponseHelper::errorWithMessage('Invoice not updated', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Invoice updated successfully');
    }

    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice){
            return ResponseHelper::errorWithMessage('Invoice not found', NOT_FOUND);
        }

        $delete = $invoice->delete;
        if (!$delete){
            return ResponseHelper::errorWithMessage('Invoice not deleted', INTERNAL_SERVER_ERROR);

        }

        return ResponseHelper::successWithMessage('Invoice deleted successfully');

    }

    public function createItem(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'quantity' => 'required|integer',
            'amount' => 'required|integer',
            'operation' => 'required|in:add,subtract',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $create = InvoiceItem::create($validator->validated());
        if (!$create){
            return ResponseHelper::errorWithMessage('Invoice item not added', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Invoice item added successfully');
    }

    public function updateItem(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'quantity' => 'required|integer',
            'amount' => 'required|integer',
            'operation' => 'required|in:add,subtract',
        ]);

        if ($validator->fails()){
            return ResponseHelper::errorWithMessage($validator->messages()->first(), UNPROCESSABLE_ENTITY);
        }

        $invoice = InvoiceItem::find($id);

        if (!$invoice){
            return ResponseHelper::errorWithMessage('Invoice item not found', NOT_FOUND);
        }

        $update = $invoice->update($validator->validated());

        if (!$update){
            return ResponseHelper::errorWithMessage('Invoice item not updated', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Invoice item Updated Successfully');
    }

    public function deleteItem($id): \Illuminate\Http\JsonResponse
    {
        $invoice = InvoiceItem::find($id);
        if (!$invoice){
            return ResponseHelper::errorWithMessage('Invoice not found', NOT_FOUND);
        }

        $delete = $invoice->delete();
        if (!$delete){
            return ResponseHelper::errorWithMessage('Invoice not deleted', INTERNAL_SERVER_ERROR);
        }

        return ResponseHelper::successWithMessage('Invoice deleted successfully');
    }
}
