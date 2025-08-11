<?php

namespace App\Http\Controllers;

use App\Exports\RentsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Rent', society_modules()) || !in_array('Rent', society_role_modules()), 403);
        abort_if((!user_can('Show Rent')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);

        return view('rents.index');
    }
}
