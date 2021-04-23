<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Cryptocard;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Models\User;
use App\Repositories\CryptoCurrencyRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CryptocardController extends Controller
{
    //
    public function __construct()
    {
    }

    public function index()
    {
        $cryptocard      = new Cryptocard();
        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards';

        $status = 'all';
        $data['status']       = $status;

        if (isset($_GET['status']))
        {
            if ($_GET['status'] == "Activate") {
                $status = 1;
                $data['status']       = "Activate";
            } else if ($_GET['status'] == "Deactivate") {
                $status = 2;
                $data['status']       = "Deactivate";
            }
        }

        if (isset($_GET['from']))
        {
            $from = $_GET['from'];
        }
        else
        {
            $from = null;
        }

        if (isset($_GET['to']))
        {
            $to = $_GET['to'];
            $to = date("d-m-Y", strtotime($to));
        }
        else
        {
            $to = null;
        }
        $data['from'] = $from;
        $data['to']   = $to;

        $data['cryptocards'] = $cryptocard->getCryptocards($from, $to, $status);

        return view('user_dashboard.cryptocards.index', $data);
    }

    public function addCryptocard(Request $request)
    {
        if ($request->code && $request->senderId) {
            $cryptocards = Cryptocard::where('code', $request->code)->get();
            if (count($cryptocards) > 0) {
                return "error";
            }
            $user = User::where('id', $request->senderId)->get();
            if (count($user) == 0) {
                return "error";
            }
            $cryptocard = new Cryptocard;
            $cryptocard->code = $request->code;
            $cryptocard->status = 2;
            $cryptocard->assignedToUser = $request->senderId;
            $cryptocard->save();
            return response()->json([
                'cryptocard' => $cryptocard,
            ], 200);
        }
        return "error";
    }

    public function getCryptocard(Request $request)
    {
        if ($request->code) {
            $cryptocard = Cryptocard::where('code', $request->code)->first();
            return response()->json([
                'cryptocard' => $cryptocard,
            ], 200);
        } else if ($request->id) {
            $cryptocard = Cryptocard::find($request->id);
            return response()->json([
                'cryptocard' => $cryptocard,
            ], 200);
        }
        return "error";
    }

    public function activateCryptocard(Request $request)
    {
        if ($request->code) {
            $cryptocard = Cryptocard::where('code', $request->code)->first();
            if ($cryptocard == null) {
                return "error";
            }
            if ($request->BTC_EUR && $request->purchase_value && $request->senderId) {
                $cryptocard->status = 1;
                $cryptocard->EUR = $request->purchase_value;
                $cryptocard->BTC_EUR = $request->BTC_EUR;
                $cryptocard->BTC = $request->purchase_value / $request->BTC_EUR;
                $cryptocard->activatedFrom = Carbon::now();
                $cryptocard->rateTimestamp = Carbon::now();
                $cryptocard->assignedToUser = $request->senderId;
                $cryptocard->activatedBy = $request->senderId;
                $wallet = Wallet::where('user_id', $request->senderId)->first();
                $cryptocard->wallet_id = $wallet->id;
                $cryptocard->save();

                return response()->json([
                    'cryptocard' => $cryptocard,
                ], 200);
            }
            return response()->json([
                'message' => 'Please put required parameters',
            ], 300);
        }
        return "error";
    }
}
