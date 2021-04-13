<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\DataTables\Admin\CryptocardsDataTable;
use App\Http\Controllers\Controller;

class CryptocardController extends Controller
{
    //
    public function __construct()
    {
    }

    public function index(CryptocardsDataTable $dataTable)
    {
        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards_list';
        return $dataTable->render('admin.cryptocards.index', $data);
    }

    public function create()
    {
        // dd(session()->all());

        $data['menu']     = 'cryptocards';
        $data['sub_menu'] = 'cryptocards_list';

        return view('admin.cryptocards.create', $data);
    }
}
