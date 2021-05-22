<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade; 
use App\Models\TradeResult; 
use Illuminate\Support\Facades\DB; 

class TradesController extends Controller
{
    
    public function positions(Trade $trade) {

        $position = [];
        $trades = DB::table('trades')
                        ->where('trades.status', 0) 
                        ->get();
   
        $trades = $this->group_trades( $trades );
        

        foreach ( $trades as $key => $trade ) {

            $position[] = $this->calculate_position($trade);
        }

        return (array) $position;

    }

    public function group_trades( $trades ) {

        $data = [];

        foreach ( $trades as $trade ) {

            $data[$trade->stock_code][] = $trade;
        } 

        return $data;
    }

    public function calculate_position( $trades ) {

        $row = [];

        $ave_price = 0;
        $total_cost = 0;
        $total_shares = 0;  
        foreach ( $trades as $trade ) {
           
            $total_shares += $trade->shares - $trade->sold; 
            $total_cost += $total_shares * $trade->purchase_price + $this->calculateBuyingFees($total_shares, $trade->purchase_price); //Need to get fees of transaction $trade->fees; 
        
        }
 
        $ave_price = ($total_cost / $total_shares);

        return array(
            'ave_price' => $ave_price,
            'total_cost' => $total_cost,
            'total_shares' => $total_shares,
            'stock_code' => $trades[0]->stock_code
        );
    }

    public function getClosedTrades() {

        $closedTrades = Trade::where('status','=', 1)  
                                ->orderBy('purcased_date','DESC')
                                ->orderBy('id', 'DESC')
                                ->get();
        $data = [];
        
        foreach ( $closedTrades as $trade ) {

            $transaction = $this->getTradeTransactions($trade->id);
            $avgSellPrice = $transaction->total_price / $transaction->total_records;
            $avgSell = $this->calculateAvgSellPrice( $avgSellPrice, $trade->shares, $transaction->total_fees );
            $avgBuy = $this->calculateAvgBuyPrice( $trade->purchase_price, $trade->shares );
       
            $total_buying_cost = $avgBuy * $trade->shares;
            $total_selling_cost = $avgSell * $trade->shares;
            $gain_loss_percentage = $this->calculateGainLossPercentage($total_buying_cost, $total_selling_cost);
            $profit_loss = $this->calculateProfitLoss($total_buying_cost, $total_selling_cost);
            $result = $gain_loss_percentage >= 0 ? 'win' : 'loss';

            $data[] = array(
                'date' => $trade->date,
                'stock_code' => $trade->stock_code,
                'avg_buy' => number_format($avgBuy,4),
                'avg_sell' => number_format($avgSell,4),
                'side' => 'Long',
                'result' => $result,
                'profit_loss' => number_format($profit_loss,2),
                'gain_loss_percentage' => number_format($gain_loss_percentage,2) . '%',
                'action' => ''
            );
        }  

        return $data;
        
    } 

    public function getTopGainers() {

        return DB::table('trade_results')
                    ->join('trades', 'trades.id', '=', 'trade_results.trade_id')
                    ->select('trade_results.win', 'trade_results.gain_loss_percentage', 'trade_results.gain_loss_amount', 'trades.stock_code')
                    ->where('trade_results.win', '=', '1')
                    ->orderBy('gain_loss_percentage', 'ASC')
                    ->limit(5)
                    ->get();
                            
    }
    
    public function getTopLosers() {

        $topLosers = DB::table('trade_results')
                    ->join('trades', 'trades.id', '=', 'trade_results.trade_id')
                    ->select('trade_results.win', 'trade_results.gain_loss_percentage', 'trade_results.gain_loss_amount', 'trades.stock_code')
                    ->where('trade_results.win', '=', '0')
                    ->orderBy('gain_loss_percentage', 'DESC')
                    ->limit(5)
                    ->get();

        foreach ( $topLosers as $loser) {

            $loser->gain_loss_amount = $loser->gain_loss_amount * -1;
        }

        return $topLosers;
    }

    public function getTradeTransactions( $trade_id ) {

        return $transaction = DB::table('transactions')
                                ->select(
                                    DB::raw('SUM(price) as total_price'),
                                    DB::raw('COUNT(id) as total_records'),
                                    DB::raw('SUM(fees) as total_fees')
                                )
                                ->where('trade_id', '=', $trade_id)
                                ->where('type','=','sell')
                                ->first();
    }

    public function calculateProfitLoss( $buyPrice, $sellPrice ) {

        return $sellPrice - $buyPrice;
    }

    public function calculateAvgSellPrice( $price, $shares, $fees) {
 
        $sellAmount = ($price * $shares) - $fees;
        $avgSell = $sellAmount / $shares; 
        
        return $avgSell;
    } 

    public function calculateAvgBuyPrice( $price, $shares ) {
        
        $fees = $this->calculateBuyingFees( $shares, $price );  
      
        $avgPrice = $price * $shares + $fees;

        return $avgPrice / $shares;
        
    }

    public function calculateGainLossPercentage( $totalBuy, $totalSold ) {

        $percentageGain = ($totalSold - $totalBuy) / $totalBuy;
        return $percentageGain * 100;
    }

    public function calculateBuyingFees( $shares, $price ) {
    
        // BUYING FEES CALCULATION
        // Commission = ( TOTAL SHARES * PRICE ) * .25% 
        // VAT = Commission * 12%
        // PSE Trans Fee = ( TOTAL SHARES * PRICE ) * 0.005%
        // SCCP = ( TOTAL SHARES * PRICE ) * 0.01%
        
        $commission = ( $shares * $price ) * 0.0025;
        $vat = $commission * 0.12;
        $trans_fee = ( $shares * $price ) * 0.00005;
        $sccp = ( $shares * $price ) * 0.0001; 
        $fees = $commission + $vat + $trans_fee + $sccp;
        
        return $fees; 
      
    }

    public function getAccountPerformanceSummary() {

        $totalTradesTaken = $this->getTotalTradesTaken();
        $winningPercentage = $this->getWinningPercentage();
        $averageWins = $this->calculateAverageGain();
        $averageLosses = $this->calculateAverageLosses();
        $winLossRatio = $this->getWinLossRatio();

        return array(
            'totalTrades' => $totalTradesTaken,
            'winningPercentage' => $winningPercentage,
            'averageWins' => $averageWins,
            'averageLosses' => $averageLosses,
            'winLossRatio' => $this->getWinLossRatio(),
            'adjustedWinLossRatio' => 0
        );

    }

    public function getTotalTradesTaken() {

        return Trade::where('status', 1)->count();
    }

    public function getTotalLossTrades() {

        return TradeResult::where('win', '=', 0)->count();
    }

    public function getTotalWinTrades() {

        return TradeResult::where('win', '=', 1)->count();
    }

    //calculate trade winning percentage
    public function getWinningPercentage() {

        $totalTrades = $this->getTotalTradesTaken(); 
        $winTrades = $this->getTotalWinTrades();


        if ( $totalTrades && $winTrades)
            return $winTrades / $totalTrades * 100;

    }

    public function getWinLossRatio() {

        $wins = $this->getTotalWinTrades();
        $losses = $this->getTotalLossTrades();
 
        if ( $wins && $losses) 
            return $wins / $losses;

        return 0;
    }

    public function calculateAverageGain() {
 
        $gains = TradeResult::where('win', '=', 1)
                                ->select(
                                    DB::raw('SUM(gain_loss_percentage) as totalGains'), 
                                    DB::raw('count(id) as numRows'))
                                ->first();
        
        if ( $gains->totalGains )
            return number_format($gains->totalGains / $gains->numRows,2);

    }

    public function calculateAverageLosses() {
 
        $trades = TradeResult::where('win', '=', 0)
                                ->select(
                                    DB::raw('SUM(gain_loss_percentage) as totalGains'), 
                                    DB::raw('count(id) as numRows'))
                                ->first();
        
        if ( $trades->totalGains)
            return number_format($trades->totalGains / $trades->numRows,2);                       

    }

    public function calculateAdjustedWinLossRatio() {

        //Average Gain * % Of winning Trades / average loss * % of losing Trades

        $averageGain = $this->calculateAverageGain();

    }

    public function monthlyTracker() {

        $pastYear = date('Y-m-d', strtotime('-12 months'));
    }
}
