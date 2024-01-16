<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CopyController;
use App\Http\Controllers\LendingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//admin férhet hozzá
Route::middleware( ['admin'])->group(function () {
    Route::apiResource('/api/users', UserController::class);
});

//bejelentkezett felhasználó
Route::middleware('auth.basic')->group(function () {
    Route::post('/lendings', [LendingController::class, 'store']);
    Route::get('/reservations/{user_id}/{book_id}/{start}', [ReservationController::class, 'show']);
    Route::patch('/reservations/{user_id}/{book_id}/{start}', [ReservationController::class, 'update']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::apiResource('/copies', CopyController::class);
    //lekérdezések
    //with
    Route::get('/with/book_copy', [BookController::class, 'bookCopy']);
    Route::get('/with/lending_user', [LendingController::class, 'lendingUser']);
    Route::get('/with/lending_user2', [LendingController::class, 'lendingUser2']);
    Route::get('/with/copy_book_lending', [CopyController::class, 'copyBookLending']);
    Route::get('/with/user_l_r', [UserController::class, 'userLR']);
    //egyéb lekérdezések
    Route::get('books_at_user', [LendingController::class, 'booksAtUser']);
    //lengthen($copy_id, $start)
    Route::patch('lengthen/{copy_id}/{start}', [LendingController::class, 'lengthen']);
    //moreLendings($copy_id, $db)
    Route::get('more_lendings/{copy_id}/{db}', [CopyController::class, 'moreLendings']);
    Route::get('books_back', [LendingController::class, 'booksBack']);
    //triggerek
    Route::patch('bringBack/{copy_id}/{start}',[LendingController::class,'bringBack']); 
    //try to insert lendings
    Route::post("lendingInsertTry/{copy_id}",[LendingController::class,'lendingInsertTry']);
    Route::post("reservationInsertTry/{book_id}",[ReservationController::class,'reservationInsertTry']);
    Route::post("lendingCount/{copy_id}",[LendingController::class,'lendingCount']);
});

//bejelentkezés nélkül is hozzáférhet
Route::apiResource('/books', BookController::class);
Route::patch('/user_password/{id}', [UserController::class, 'updatePassword']);
Route::delete('/lendings/{user_id}/{copy_id}/{start}', [LendingController::class, 'destroy']);
//lekérdezések
Route::get('/publicated/{book_id}', [BookController::class, 'publicated']);
Route::get('/publicated2/{book_id}', [BookController::class, 'publicated2']);
Route::get('/publicated_count/{book_id}', [BookController::class, 'publicatedCount']);
