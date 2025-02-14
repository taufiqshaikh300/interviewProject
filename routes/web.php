<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


// Redirect '/' based on session status
Route::get('/', function () {
    if (Session::has('user_id')) {
        $role = Session::get('user_role');
        if ($role === 'organizer') {
            return redirect()->route('organizer.dashboard');
        } elseif ($role === 'attendee') {
            return redirect()->route('attendee.dashboard');
        }
    }
    return redirect()->route('auth.showlogin');
});


Route::get('/user-registration', [AuthController::class,'showRegistrationForm'])->name('auth.register');
Route::post('/user-registration',[AuthController::class,'store'])->name('auth.store');
Route::get('/login',[AuthController::class,'showLogin'])->name('auth.showlogin');
Route::post('/login',[AuthController::class,'login'])->name('auth.login');



Route::middleware(['role:organizer'])->group(function () {
    // Route::get('/organizer/dashboard', function () {
    //     return redirect()->route('organizer.allPublicEvents');
    // })->name('organizer.dashboard');

    Route::get('/organizer/dashboard', [EventController::class, 'dashboard'])->name('organizer.dashboard');


    Route::get('/organizer/addEvent', [EventController::class,'addEvent'])->name('organizer.addEvent');
    Route::post('/organizer/addEvent', [EventController::class,'storeEvent'])->name('organizer.storeEvent');
    Route::get('/organizer/Events', [EventController::class,'myEvents'])->name('organizer.allEvents');
    Route::get('/organizer/allEvents', [EventController::class,'allPublicEvents'])->name('organizer.allPublicEvents');
    Route::post('/organizer/cancel', [EventController::class, 'cancelEvent'])->name('organizer.cancel');
    Route::get('/organizer/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/organizer/{event}', [EventController::class, 'update'])->name('events.update');
    Route::get('/organizer/event/{id}/export-csv', [EventController::class, 'exportEventDetails'])->name('organizer.event.exportCSV');


    Route::get('/organizer/event/{id}', [EventController::class, 'viewEvent'])->name('organizer.viewEvent');
});

Route::middleware(['role:attendee'])->group(function () {
    Route::get('/attendee/dashboard', [AttendeeController::class, 'availableEvents'])->name('attendee.dashboard');
    Route::get('/attendee/event/{id}', [AttendeeController::class, 'viewEvent'])->name('attendee.viewEvent');
    Route::post('/event/book', [BookingController::class, 'bookTickets'])->name('event.bookTickets');
    Route::get('/event/paymentsuccess', [BookingController::class, 'paymentdone'])->name('event.paymentsuccess');
    Route::get('/attendee/mypurchase', [BookingController::class, 'mypurchase'])->name('attendee.mypurchase');
});



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
