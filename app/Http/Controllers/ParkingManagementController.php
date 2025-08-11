<?php

namespace App\Http\Controllers;

use App\Exports\ParkingExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ParkingManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Parking', society_modules()) || !in_array('Parking', society_role_modules()), 403);
        abort_if((!user_can('Show Parking')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);
        return view('parkings.index');
    }

}
