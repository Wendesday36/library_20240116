<?php

namespace App\Http\Controllers;
use Illuminate\Database\QueryException; 
use App\Models\Lending;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LendingController extends Controller
{
    public function index()
    {
        return Lending::all();
    }

    public function show($user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)->where('copy_id', $copy_id)->where('start', $start)->get();
        return $lending[0];
    }


    public function destroy($user_id, $copy_id, $start)
    {
        LendingController::show($user_id, $copy_id, $start)->delete();
    }

    public function update(Request $request, $user_id, $copy_id, $start)
    {
        $lending = Lending::show($user_id, $copy_id, $start);
        // csak patch!!!
        $lending->end = $request->end;
        $lending->extension = $request->extension;
        $lending->notice = $request->notice;
        $lending->save();
    }

    public function store(Request $request)
    {
        $lending = new Lending();
        $lending->user_id = $request->user_id;
        $lending->copy_id = $request->copy_id;
        $lending->start = $request->start;
        $lending->end = $request->end;
        $lending->extension = $request->extension;
        $lending->notice = $request->notice;
        $lending->save();
    }

    public function lendingUser()
    {
        //bejelentkezett felhasználó
        $user = Auth::user();
        $lendings = Lending::with('user')->where('user_id', '=', $user->id)->get();
        return $lendings;
    }

    public function lendingUser2()
    {
        //bejelentkezett felhasználó
        $user = Auth::user();
        $lendings = Lending::with('userHas')->where('user_id', '=', $user->id)->get();
        return $lendings;
    }

    public function booksAtUser()
    {
        //bejelentkezett felhasználó
        $user = Auth::user();
        $books = DB::table('lendings as l')    //egy tábla lehet csak
            ->join('copies as c', 'l.copy_id', '=', 'c.copy_id') //kapcsolat leírása, akár több join is lehet
            ->join('books as b', 'c.book_id', '=', 'b.book_id')
            ->select('b.title', 'b.author')
            ->where('l.user_id', '=', $user->id)     //esetleges szűrés
            ->whereNull('l.end')
            ->get();                //esetleges aggregálás; ha select, akkor get() a vége
        return $books;
    }

    //hosszabbítsd meg az egyik nálad lévő könyvet (copy_id, start) ! (patch)
    public function lengthen($copy_id, $start)
    {
        //patch kérés!!!
        //bejelentkezett felhasználó
        $user = Auth::user();
        DB::table('lendings')
            ->where('copy_id', $copy_id)
            ->where('start', $start)
            ->where('user_id', $user->id)
            //update paramétere assz tömb, benne kulcs-érték párok
            ->update(['extension' => 1]);
    }

    public function booksBack()
    {
        $books = DB::table('lendings as l')
            ->join('copies as c', 'l.copy_id', '=', 'c.copy_id')
            ->join('books as b', 'c.book_id', '=', 'b.book_id')
            ->select('b.author', 'b.title')
            ->whereRaw('DATEDIFF(CURRENT_DATE, l.end) = 0')
            ->get();
        return $books;
    }
    public function bringsBack($copy_id, $start)
    {
        //visszahozom a könyvet - patch!
        $user = Auth::user();
        $lending = LendingController::show($user->id, $copy_id, $start);
        $lending->end = date(now());
        $lending->save();
        DB::table('copies')
            ->where('copy_id', $copy_id)
            ->update(['status' => 0]);
    }
    public function bringBack($copy_id, $start)
    {
        //visszahozom a könyvet patch!
        $user = Auth::user();
        $lending = LendingController::show($user->id, $copy_id, $start);
        $lending->end = date(now());
        $lending->save();
        /* DB::table('copies')
->where('copy_id', $copy_id)
->update(['status' => 0]); */
        DB::select('CALL toStore(?)', array($copy_id));
    }
    public function lendingInsertTry($copy_id){
        $user = Auth::user();
        try { 
            // Próbáld meg beszúrni a rekordot 
            DB::table('lendings')->insert([ 
                'user_id' => $user->id, 
                'copy_id' => $copy_id, 
                'start'=>date(now()),
                'extension'=>0,
                'notice'=>0
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

    public function lendingCount($copy_id){
        
            $user = Auth::user();
            try {
                $pontok = DB::table('lendings')
                    ->where('user_id', $user->id)
                    ->where('start','>',now()->subYear())
                    ->sum('notice');
                    echo $pontok;
     
                if ($pontok <= 3) {
                    DB::table('lendings')->insert([
                        'user_id' => $user->id,
                        'copy_id' => $copy_id,
                        'start' => date(now()),
                        'extension' => 0,
                        'notice' => 0,
                    ]);
                }else{
                    echo "Túl sok figyelmeztetésed van!";
                }
            } catch (QueryException $e) {
     
                if ($e->errorInfo[1] == 1062) {
                    echo "A rekord már létezik a táblában.";
                } else {
     
                    echo "Hiba történt: " . $e->getMessage();
                }
            }
         
    }
}
