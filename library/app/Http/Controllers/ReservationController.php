<?php

namespace App\Http\Controllers;
use Illuminate\Database\QueryException; 
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(){
        //select * from reservations
        return Reservation::all();
    }

    public function show ($user_id, $book_id, $start)
    {
        $reservation = Reservation::where('user_id', $user_id)
        ->where('book_id', $book_id)
        ->where('start', $start)
        ->get();
        //egyelemű listát ad vissza a get, az elemet szeretnénk visszakapni
        return $reservation[0];
    }


    public function destroy($user_id, $book_id, $start){
        $this->show($user_id, $book_id, $start)->delete();
    }
    
    public function update(Request $request, $user_id, $book_id, $start){
        $reservation = $this->show($user_id, $book_id, $start);
        //csak patch!!!
        $reservation->message = $request->message;
        $reservation->save();
    }
    
    public function store(Request $request){
        $reservation = new Reservation();
        $reservation->user_id = $request->user_id;
        $reservation->book_id = $request->book_id;
        $reservation->start = $request->start;
        $reservation->message = $request->message;
        $reservation->save();
        
    }
    public function reservationInsertTry($book_id){
        $user = Auth::user();
        try { 
            // Próbáld meg beszúrni a rekordot 
            DB::table('reservations')->insert([ 
                'user_id' => $user->id, 
                'book_id' => $book_id, 
                'start'=>date(now()),
                'message'=>0
                // többi oszlop és érték... 
            ]);  
        } 
        catch (QueryException $e) { 
            // Ha kivétel történik (például az egyediségi megsértése), akkor itt kezeld 
            if ($e->errorInfo[1] == 1062) { // 1062 az egyediségi megsértés hibakódja MySQL-ben 
                echo "A rekord már létezik a táblában.";
        //return redirect "/"; 
            } else { 
                // Más típusú kivétel esetén kezelheted őket itt 
                echo "Hiba történt: " . $e->getMessage(); 
            } 
        }
    }
}
