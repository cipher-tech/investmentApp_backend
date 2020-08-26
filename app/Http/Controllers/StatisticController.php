<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\History;
use App\Statistic;
use App\User;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    private function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }

    public function noVisits(Request $page){
        $stats = Statistic::get();
        $stats->no_visits += 1;

        $stats->save();
    }

    public function getAdminStats()
    {
        $totalUsers = User::all()->count();
        $verifiedUsers = User::where("status", 'verified')->count();

        $totalDeposits = Deposit::all()->sum("amount");
        $totalAccepedDeposits = Deposit::where("status", "accepted")->sum("amount");
        $totalPendingDeposits = Deposit::where("status", "pending")->sum("amount");
        $totalDepositsCount = Deposit::all()->count();
        $totalAccepedDepositsCount = Deposit::where("status", "accepted")->count();
        $totalPendingDepositsCount = Deposit::where("status", "pending")->count();

        $totalWidthdrawals = Deposit::all()->sum("amount");
        $totalAccepedWidthdrawals = Deposit::where("status", "accepted")->sum("amount");
        $totalPendingWidthdrawals = Deposit::where("status", "pending")->sum("amount");
        $totalWidthdrawalsCount = Deposit::all()->count();
        $totalAccepedWidthdrawalsCount = Deposit::where("status", "accepted")->count();
        $totalPendingWidthdrawalsCount = Deposit::where("status", "pending")->count();

        $totalTransactions = History::all()->count();
        $totalAcceptedTransactions = History::where("status", "accepted")->count();
        $totalPendingTransactions = History::where("status", "pending")->count();
        $totalTransactionsSum = History::all()->sum("amount");
        $totalAcceptedTransactionsSum = History::where("status", "accepted")->sum("amount");
        $totalPendingTransactionsSum = History::where("status", "pending")->sum("amount");

        $totalBitcoinTransactions = History::where("type", "bitcoin")->count();
        $totalGiftCardTransactions =  $totalTransactions - $totalBitcoinTransactions;
        $totalBitcoinTransactionsSum = History::where("type", "bitcoin")->sum("amount");
        $totalGiftCardTransactionsSum = History::where("type", "!=", "bitcoin")->sum("amount");

        $totalBuy =  History::where("action", "buy")->count();
        $totalSell =  History::where("action", "sell")->count();

        $totalBuySum =  History::where("action", "buy")->sum("amount");
        $totalSellSum =  History::where("action", "sell")->sum("amount");

        $numVisits =  Statistic::get("no_visits");
    }
}
