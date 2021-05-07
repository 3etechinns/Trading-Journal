<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Equity;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    //

    public function store(Request $request) {
   
        $date = $request['data']['date'];
        $amount = $request['data']['amount'];
        $action = $request['data']['action'];
        $totalEquity = $request['totalEquity'] + $amount;
        $remainingCash = $request['availableCash'] + $amount;

        DB::beginTransaction();

        try { 

            $bank = Bank::create([
                'date' => $date,
                'amount' => $amount,
                'action' => $action
            ]);
            
            Equity::create([
                'date' => $date,
                'total_equity' => $totalEquity,
                'remaining_cash' => $remainingCash,
                'action' => $action,
                'action_reference_id' => $bank->id
            ]);

            DB::commit();
            return 1;

        } catch (\Exception $e) {
            
            DB::rollBack();
            throw $e;
            return 0;
        }
          
    }
}
