<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\DataTables\Admin\CryptocardsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Cryptocard;
use App\Models\User;

class CryptocardController extends Controller
{
    protected $helper;
    protected $cryptocard;
    //
    public function __construct()
    {
        $this->helper         = new Common();
        $this->cryptocard           = new Cryptocard();
    }

    public function index(CryptocardsDataTable $dataTable)
    {
        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards_list';

        if (isset($_GET['btn']))
        {
            $data['user']     = $user     = $_GET['user_id'];

            $data['getName'] = $getName = $this->cryptocard->getCryptocardsUsersName($user);

            if (empty($_GET['from']))
            {
                $data['from'] = null;
                $data['to']   = null;
            }
            else
            {
                $data['from'] = $_GET['from'];
                $data['to']   = $_GET['to'];
            }
        }
        else
        {
            // dd('init');
            $data['from']     = null;
            $data['to']       = null;
            $data['user']     = null;
        }
        return $dataTable->render('admin.cryptocards.index', $data);
    }

    public function create()
    {
        // dd(session()->all());

        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards_list';

        return view('admin.cryptocards.create', $data);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        if ($_POST)
        {
            $rules = array(
                'code'            => 'required',
                'BTC'             => 'required|numeric',
                'BTC_EUR'         => 'required|numeric',
                'EUR'             => 'required|numeric',
                'user'            => 'required',
                'wallet_id'       => 'required|integer',
            );

            $fieldNames = array(
                'code'            => 'Code',
                'BTC'             => 'BTC',
                'BTC_EUR'         => 'BTC/EUR',
                'EUR'             => 'Purchase Value',
                'user'            => 'User',
                'wallet_id'       => 'Wallet',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                try
                {
                    // Create cryptocard
                    $cryptocard = $this->cryptocard->createNewCryptocard($request);

                    \DB::commit();
                    $this->helper->one_time_message('success', 'Cryptocard Created Successfully');
                    return redirect('admin/cryptocards');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('admin/cryptocards');
                }
            }
        }
    }

    public function edit($id)
    {
        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards_list';

        $data['cryptocards'] = $cryptocards = Cryptocard::find($id);
        // dd($cryptocards);

        $data['user'] = $user = User::select('id', 'first_name', 'last_name')->where('id', $cryptocards->assignedToUser)->first();

        return view('admin.cryptocards.edit', $data);
    }

    public function update(Request $request)
    {
        // dd($request->all());

        if ($_POST)
        {
            $rules = array(
                'code'            => 'required',
                'BTC'             => 'required|numeric',
                'BTC_EUR'         => 'required|numeric',
                'EUR'             => 'required|numeric',
                'user'            => 'required',
                'wallet_id'       => 'required|integer',
            );

            $fieldNames = array(
                'code'            => 'Code',
                'BTC'             => 'BTC',
                'BTC_EUR'         => 'BTC/EUR',
                'EUR'             => 'Purchase Value',
                'user'            => 'User',
                'wallet_id'       => 'Wallet',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                try
                {
                    // Create cryptocard
                    $cryptocard = $this->cryptocard->updateCryptocard($request);

                    \DB::commit();
                    $this->helper->one_time_message('success', 'Cryptocard Updated Successfully');
                    return redirect('admin/cryptocards');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('admin/cryptocards');
                }
            }
        }
    }

    public function destroy($id)
    {
        // $id = decrypt($id);

        $cryptocard = Cryptocard::find($id);
        if ($cryptocard)
        {
            try
            {
                $cryptocard->delete();

                \DB::commit();

                $this->helper->one_time_message('success', 'Cryptocard Deleted Successfully');
                return redirect('admin/cryptocards');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('admin/cryptocards');
            }
        }
    }

    public function cryptocardsUserSearch(Request $request)
    {
        $search = $request->search;
        $user   = $this->cryptocard->getCryptocardsUsersResponse($search);

        $res = [
            'status' => 'fail',
        ];
        if ($user != null)
        {
            $res = [
                'status' => 'success',
                'data'   => $user,
            ];
        }
        return json_encode($res);
    }
}
