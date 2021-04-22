<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Cryptocard;
use App\Models\Transfer;
use App\Models\Wallet;
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
        if ($request->code) {
            $cryptocards = Cryptocard::where('code', $request->code)->get();
            if (count($cryptocards) > 0) {
                return response()->json([
                    'message' => 'The code already exist.',
                ], 300);
            }
            $cryptocard = new Cryptocard;
            $cryptocard->code = $request->code;
            $cryptocard->status = 2;
            $cryptocard->assignedToUser = auth()->user()->id;
            $cryptocard->save();
            return response()->json([
                'message' => 'success',
                'cryptocard' => $cryptocard,
            ], 200);
        }
        return response()->json([
            'message' => 'failed',
        ], 300);
    }

    public function getCryptocard(Request $request)
    {
        if ($request->code) {
            $cryptocard = Cryptocard::where('code', $request->code)->first();
            return response()->json([
                'message' => 'success',
                'cryptocard' => $cryptocard,
            ], 200);
        } else if ($request->id) {
            $cryptocard = Cryptocard::find($request->id);
            return response()->json([
                'message' => 'success',
                'cryptocard' => $cryptocard,
            ], 200);
        }
        return response()->json([
            'message' => 'failed',
        ], 300);
    }

    public function activateCryptocard(Request $request)
    {
        if ($request->code) {
            $cryptocard = Cryptocard::where('code', $request->code)->first();
            if ($cryptocard == null) {
                return response()->json([
                    'message' => 'Such card does not exist',
                ], 300);
            }
            if ($request->BTC_EUR && $request->purchase_value) {
                $cryptocard->status = 1;
                $cryptocard->EUR = $request->purchase_value;
                $cryptocard->BTC_EUR = $request->BTC_EUR;
                $cryptocard->BTC = $request->purchase_value / $request->BTC_EUR;
                $cryptocard->activatedFrom = Carbon::now();
                $cryptocard->rateTimestamp = Carbon::now();
                $cryptocard->assignedToUser = auth()->user()->id;
                $wallet = Wallet::where('user_id', auth()->user()->id)->first();
                $cryptocard->wallet_id = $wallet->id;
                $cryptocard->activatedBy = auth()->user()->id;
                $cryptocard->save();

                return response()->json([
                    'message' => 'Successfully updated',
                    'cryptocard' => $cryptocard,
                ], 200);
            }
            return response()->json([
                'message' => 'Please put required parameters',
                'cryptocard' => $cryptocard,
            ], 300);
        }
        return response()->json([
            'message' => 'failed',
        ], 300);
    }
}
