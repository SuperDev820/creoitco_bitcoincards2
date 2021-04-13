<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Cryptocard;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Repositories\CryptoCurrencyRepository;
use Auth;
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
}
