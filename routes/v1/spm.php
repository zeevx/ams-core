<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\V1\Complaints\ComplaintController;
use App\Http\Controllers\V1\Dashboard\DashboardController;
use App\Http\Controllers\V1\Events\EventController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Messages\MessageController;
use App\Http\Controllers\V1\Notice\NoticeController;
use App\Http\Controllers\V1\Request\RequestController;
use App\Http\Controllers\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'senior-property-manager'], function (){
    Route::group(['middleware' => ['authenticated','role:senior-property-manager']], function(){
        Route::get('/',[DashboardController::class,'index'])->name('dashboard');

        //User Route
        Route::group(['prefix' => 'user','namespace' => 'users'], function (){
            Route::get('/', [UserController::class,'user'])->name('index');
            Route::post('/update', [UserController::class,'update'])->name('update');
            Route::post('/update/profile', [UserController::class,'updateProfile'])->name('update.profile');
            Route::post('/fetch', [UserController::class,'fetch'])->name('fetch');
        });

        //Messages Route
        Route::group(['prefix' => 'messages','namespace' => 'messages'], function (){
            Route::get('/', [MessageController::class,'index'])->name('index');
            Route::post('/create', [MessageController::class,'create'])->name('create');
            Route::get('/view/{id}', [MessageController::class,'view'])->name('view');
            Route::post('/update/{id}', [MessageController::class,'update'])->name('update');
            Route::post('/delete/{id}', [MessageController::class,'delete'])->name('delete');
            Route::group(['prefix' => 'replies','namespace' => 'replies'], function (){
                Route::post('/create', [MessageController::class,'createReply'])->name('create');
                Route::post('/update/{id}', [MessageController::class,'updateReply'])->name('update');
                Route::post('/delete/{id}', [MessageController::class,'deleteReply'])->name('delete');
            });
        });

        //Notice Route
        Route::group(['prefix' => 'notices','namespace' => 'notices'], function (){
            Route::get('/', [NoticeController::class,'index'])->name('index');
            Route::get('/categories', [NoticeController::class,'categories'])->name('index');
            Route::post('/create', [NoticeController::class,'create'])->name('create');
            Route::get('/view/{id}', [NoticeController::class,'view'])->name('view');
            Route::post('/update/{id}', [NoticeController::class,'update'])->name('update');
            Route::post('/delete/{id}', [NoticeController::class,'delete'])->name('delete');
        });

        //Complaints Route
        Route::group(['prefix' => 'complaints','namespace' => 'complaints'], function (){
            Route::get('/', [ComplaintController::class,'index'])->name('index');
            Route::get('/categories', [ComplaintController::class,'categories'])->name('index');
            Route::post('/create', [ComplaintController::class,'create'])->name('create');
            Route::get('/view/{id}', [ComplaintController::class,'view'])->name('view');
            Route::post('/update/{id}', [ComplaintController::class,'update'])->name('update');
            Route::post('/delete/{id}', [ComplaintController::class,'delete'])->name('delete');
            Route::group(['prefix' => 'replies','namespace' => 'replies'], function (){
                Route::post('/create', [ComplaintController::class,'createReply'])->name('create');
                Route::post('/update/{id}', [ComplaintController::class,'updateReply'])->name('update');
                Route::post('/delete/{id}', [ComplaintController::class,'deleteReply'])->name('delete');
            });
        });

        //Requests Routes
        Route::group(['prefix' => 'requests','namespace' => 'requests'], function (){
            Route::get('/', [RequestController::class,'index'])->name('index');
            Route::get('/categories', [RequestController::class,'categories'])->name('index');
            Route::post('/create', [RequestController::class,'create'])->name('create');
            Route::get('/view/{id}', [RequestController::class,'view'])->name('view');
            Route::post('/update/{id}', [RequestController::class,'update'])->name('update');
            Route::post('/delete/{id}', [RequestController::class,'delete'])->name('delete');
            Route::group(['prefix' => 'replies','namespace' => 'replies'], function (){
                Route::post('/create', [RequestController::class,'createReply'])->name('create');
                Route::post('/update/{id}', [RequestController::class,'updateReply'])->name('update');
                Route::post('/delete/{id}', [RequestController::class,'deleteReply'])->name('delete');
            });
        });

        //Events Route
        Route::group(['prefix' => 'events','namespace' => 'events'], function (){
            Route::get('/', [EventController::class,'index'])->name('index');
            Route::post('/create', [EventController::class,'create'])->name('create');
            Route::get('/view/{id}', [EventController::class,'view'])->name('view');
            Route::post('/update/{id}', [EventController::class,'update'])->name('update');
            Route::post('/delete/{id}', [EventController::class,'delete'])->name('delete');
        });

        //Invoice Routes
        Route::group(['prefix' => 'invoices','namespace' => 'invoices'], function (){
            Route::get('/', [InvoiceController::class,'index'])->name('index');
            Route::post('/create', [InvoiceController::class,'create'])->name('create');
            Route::get('/view/{id}', [InvoiceController::class,'view'])->name('view');
            Route::post('/update/{id}', [InvoiceController::class,'update'])->name('update');
            Route::post('/delete/{id}', [InvoiceController::class,'delete'])->name('delete');
            Route::group(['prefix' => 'items','namespace' => 'items'], function (){
                Route::post('/create', [InvoiceController::class,'createItem'])->name('create');
                Route::post('/update/{id}', [InvoiceController::class,'updateItem'])->name('update');
                Route::post('/delete/{id}', [InvoiceController::class,'deleteItem'])->name('delete');
            });
        });


    });

});

//Not Found
Route::fallback(function () {
    return ResponseHelper::errorWithMessage('Route Not found', NOT_FOUND);
});
