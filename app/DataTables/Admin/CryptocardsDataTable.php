<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Common;
use App\Models\Cryptocard;
use Yajra\DataTables\Services\DataTable;

class CryptocardsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('code', function ($cryptocard)
            {
                return $cryptocard->code;
            })
            ->addColumn('BTC', function ($cryptocard)
            {
                return $cryptocard->BTC;
            })
            ->editColumn('BTC_EUR', function ($cryptocard)
            {
                return $cryptocard->BTC_EUR;
            })
            ->addColumn('EUR', function ($cryptocard)
            {
                return $cryptocard->EUR;
            })
            ->addColumn('rateTimestamp', function ($cryptocard)
            {
                return !empty($cryptocard->rateTimestamp) ? $cryptocard->rateTimestamp : '-';
            })
            ->addColumn('activatedFrom', function ($cryptocard)
            {
                return !empty($cryptocard->activatedFrom) ? $cryptocard->activatedFrom : '-';
            })
            ->addColumn('status', function ($cryptocard)
            {
                $status = '';

                if ($cryptocard->status == 1) {
                    $status = '<span class="label label-success">Active</span>';
                } else if ($cryptocard->status == 2) {
                    $status = '<span class="label label-danger">Deactive</span>';
                }
                return $status;
            })
            ->addColumn('assignedToUser', function ($cryptocard)
            {
                $assignedToUser = $cryptocard->user->first_name.' '.$cryptocard->user->last_name;

                return $assignedToUser;
            })
            ->addColumn('wallet_id', function ($cryptocard)
            {
                return $cryptocard->wallet_id;
            })
            ->addColumn('action', function ($cryptocard)
            {
                $edit = $delete = '';
                $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_cryptocard')) ? '<a href="' . url('admin/cryptocards/edit/' . $cryptocard->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_cryptocard')) ? '<a href="' . url('admin/cryptocards/delete/' . $cryptocard->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="glyphicon glyphicon-trash"></i></a>' : '';
                return $edit . $delete;
            })
            ->rawColumns(['BTC','BTC_EUR','EUR','status','action'])
            ->make(true);
    }

    public function query()
    {
        if (isset($_GET['btn']))
        {
            $user     = $_GET['user_id'];

            if (empty($_GET['from']))
            {
                $from  = null;
                $to    = null;
                $query = (new Cryptocard())->getCryptocardsList($from, $to, $user);
            }
            else
            {
                $from         = setDateForDb($_GET['from']);
                $to           = setDateForDb($_GET['to']);
                $query = (new Cryptocard())->getCryptocardsList($from, $to, $user);
            }
        }
        else
        {
            $from     = null;
            $to       = null;
            $user     = null;

            $query = (new Cryptocard())->getCryptocardsList($from, $to, $user);

        }
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'cryptocards.id', 'title' => 'ID', 'searchable' => false, 'visible' => false])

            // ->addColumn(['data' => 'status', 'name' => 'document_verification.status', 'title' => 'Document Verification Status', 'visible' => false])

            ->addColumn(['data' => 'code', 'name' => 'cryptocards.code', 'title' => 'Code'])

            ->addColumn(['data' => 'BTC', 'name' => 'cryptocards.BTC', 'title' => 'BTC'])

            ->addColumn(['data' => 'BTC_EUR', 'name' => 'cryptocards.BTC_EUR', 'title' => 'BTC/EUR'])

            ->addColumn(['data' => 'EUR', 'name' => 'cryptocards.EUR', 'title' => 'Purchase Value'])

            ->addColumn(['data' => 'rateTimestamp', 'name' => 'cryptocards.rateTimestamp', 'title' => 'Rate Time'])

            ->addColumn(['data' => 'status', 'name' => 'cryptocards.status', 'title' => 'Status'])

            ->addColumn(['data' => 'activatedFrom', 'name' => 'cryptocards.activatedFrom', 'title' => 'Activated From'])

            ->addColumn(['data' => 'assignedToUser', 'name' => 'user.first_name', 'title' => 'Assigned To User', 'visible' => false])
            ->addColumn(['data' => 'assignedToUser', 'name' => 'user.last_name', 'title' => 'Assigned To User'])

            ->addColumn(['data' => 'wallet_id', 'name' => 'cryptocards.wallet_id', 'title' => 'Wallet'])

            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false])

            ->parameters([
                'order'      => [[0, 'desc']],
                //centering all texts in columns
                "columnDefs" => [
                    [
                        "className" => "dt-center",
                        "targets" => "_all"
                    ]
                ],
                'pageLength' => \Session::get('row_per_page'),
                'language'   => \Session::get('language'),
            ]);
    }
}
