<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceApartmentExport;
use App\Exports\MaintenanceExport;
use App\Models\MaintenanceManagement;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Maintenance', society_modules()) || !in_array('Maintenance', society_role_modules()), 403);
        abort_if((!user_can('Show Maintenance')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);
        return view('maintenance.index');
    }

    public function show($id)
    {
        abort_if(!in_array('Maintenance', society_modules()) || !in_array('Maintenance', society_role_modules()), 403);
        abort_if((!user_can('Show Maintenance')), 403);
        $maintenance = MaintenanceManagement::findOrFail($id);
        return view('maintenance.show', compact('maintenance'));

    }
   
}
