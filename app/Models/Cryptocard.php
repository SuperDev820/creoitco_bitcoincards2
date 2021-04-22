<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;
use Auth;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'assignedToUser');
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
                ->orderBy('cryptocards.id')->select('cryptocards.*')
                ->paginate(15);
        }
        else
        {
            $from        = date('Y-m-d', strtotime($from));
            $to          = date('Y-m-d', strtotime($to));
            $cryptocard = $this->with([
                'wallet:id,user_id,currency_id,balance',
                'user:id,first_name,last_name'
            ])
                ->where($conditions)
                ->whereDate('cryptocards.activatedFrom', '>=', $from)
                ->whereDate('cryptocards.activatedFrom', '<=', $to)
                ->orderBy('cryptocards.id')
                ->select('cryptocards.*')
                ->paginate(15);
        }
        return $cryptocard;
    }

    public function createNewCryptocard($request)
    {
        $cryptocard = new self();
        
        $cryptocard->code = $request->code;
        $cryptocard->BTC  = $request->EUR / $request->BTC_EUR;
        $cryptocard->BTC_EUR = $request->BTC_EUR;
        $cryptocard->EUR = $request->EUR;
        if ($request->status == 'Activate') {
            $cryptocard->status = 1;
            $cryptocard->activatedBy = auth()->user()->id;
        } else if ($request->status == 'Deactivate') {
            $cryptocard->status = 2;
        }
        $first_name = explode(" ",$request->user)[0];
        $last_name  = explode(" ",$request->user)[1];
        $user = User::where('first_name', $first_name)->where('last_name', $last_name)->first();
        $cryptocard->assignedToUser = $user->id;
        $cryptocard->rateTimestamp = Carbon::now();
        $cryptocard->activatedFrom = Carbon::now();
        $cryptocard->wallet_id = $request->wallet_id;
        
        $cryptocard->save();
        return $user;
    }

    public function updateCryptocard($request)
    {
        $cryptocard = $this->find($request->id);
        if ($cryptocard->BTC_EUR != $request->BTC_EUR) {
            $cryptocard->rateTimestamp = Carbon::now();
        }
        $cryptocard->code = $request->code;
        $cryptocard->BTC  = $request->EUR / $request->BTC_EUR;
        $cryptocard->BTC_EUR = $request->BTC_EUR;
        $cryptocard->EUR = $request->EUR;
        if ($request->status == 'Activate') {
            $cryptocard->status = 1;
            $cryptocard->activatedBy = auth()->user()->id;
            $cryptocard->activatedFrom = Carbon::now();
        } else if ($request->status == 'Deactivate') {
            $cryptocard->status = 2;
        }
        $first_name = explode(" ",$request->user)[0];
        $last_name  = explode(" ",$request->user)[1];
        $user = User::where('first_name', $first_name)->where('last_name', $last_name)->first();
        $cryptocard->assignedToUser = $user->id;
        $cryptocard->wallet_id = $request->wallet_id;
        
        $cryptocard->save();
        return $user;
    }

    public function getCryptocardsList($from, $to, $user)
    {
        $conditions = [];

        if (!empty($from) && !empty($to))
        {
            $date_range = 'Available';
        }
        else
        {
            $date_range = null;
        }
        //
        $cryptocards = $this->with([
            'user:id,first_name,last_name',
            'wallet:id,user_id,currency_id,balance'
        ])->where($conditions);
        //
        //if user is not empty, check both user_id & end_user_id columns
        if (!empty($user))
        {
            $cryptocards->where(function ($q) use ($user)
            {
                $q->where(['cryptocards.assignedToUser' => $user]);
            });
        }
        //
        //
        if (!empty($date_range))
        {
            $cryptocards->whereDate('cryptocards.activatedFrom', '>=', $from)->whereDate('cryptocards.activatedFrom', '<=', $to)->select('cryptocards.*');
        }
        else
        {
            $cryptocards->select('cryptocards.*');
        }
        //
        return $cryptocards;
    }

    public function getCryptocardsUsersResponse($search)
    {
        $getCryptocardsUsers = $this->whereHas('user', function ($query) use ($search)
        {
            $query->where('first_name', 'LIKE', '%' . $search . '%')->orWhere('last_name', 'LIKE', '%' . $search . '%');
        })
        ->distinct('assignedToUser');

        $getTrxUsers = $getCryptocardsUsers->with(['user:id,first_name,last_name'])->get(['assignedToUser'])->map(function ($cryptocardA)
        {
            $arr['user_id']    = $cryptocardA->assignedToUser;
            $arr['first_name'] = $cryptocardA->user->first_name;
            $arr['last_name']  = $cryptocardA->user->last_name;
            return $arr;
        });
        
        //
        if ($getTrxUsers->isNotEmpty())
        {
            return $getTrxUsers->unique();
        }
    }

    public function getCryptocardsUsersName($user)
    {
        $getUserCryptocard = $this->where(function ($q) use ($user)
        {
            $q->where(['assignedToUser' => $user]);
        });

        $userCryptocard = $getUserCryptocard->with(['user:id,first_name,last_name'])->first();
        
        if (!empty($userCryptocard))
        {
            return $userCryptocard->user;
        }
    }
}
