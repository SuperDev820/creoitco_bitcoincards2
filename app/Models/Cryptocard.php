<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cryptocard extends Model
{
    //
    protected $fillable = [
        'code',
        'BTC',
        'BTC/EUR',
        'EUR',
        'rateTimestamp',
        'activatedFrom',
        'status',
        'activatedBy',
        'assignedToUser',
        'wallet_id',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function getCryptocards($from, $to, $status)
    {
        // dd($type);
        $conditions = [];
        if (empty($from) || empty($to))
        {
            $date_range = null;
        }
        else if (empty($from))
        {
            $date_range = null;
        }
        else if (empty($to))
        {
            $date_range = null;
        }
        else
        {
            $date_range = 'Available';
        }

        if (!empty($status) && $status != 'all')
        {
            $conditions['cryptocards.status'] = $status;
        }

        if (empty($date_range))
        {
            $cryptocard = $this->with([
                'wallet:id,user_id,currency_id,balance',
            ])
                ->where($conditions)
                ->orderBy('cryptocards.id', 'desc')->select('cryptocards.*')
                ->paginate(15);
        }
        else
        {
            $from        = date('Y-m-d', strtotime($from));
            $to          = date('Y-m-d', strtotime($to));
            $cryptocard = $this->with([
                'wallet:id,user_id,currency_id,balance',
            ])
                ->where($conditions)
                ->whereDate('cryptocards.activatedFrom', '>=', $from)
                ->whereDate('cryptocards.activatedFrom', '<=', $to)
                ->orderBy('cryptocards.id', 'desc')
                ->select('cryptocards.*')
                ->paginate(15);
        }
        return $cryptocard;
    }
}
