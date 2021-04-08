<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CryptocardController extends Controller
{
    //
    public function __construct()
    {
    }

    public function index(UsersDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        return $dataTable->render('admin.users.index', $data);
    }
}
