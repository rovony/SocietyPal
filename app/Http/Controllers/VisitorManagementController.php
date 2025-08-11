<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\VisitorsExport;
use App\Models\VisitorManagement;
use Maatwebsite\Excel\Facades\Excel;

class VisitorManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Visitors', society_modules()) || !in_array('Visitors', society_role_modules()), 403);
        abort_if((!user_can('Show Visitors')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('visitorsManagement.index');
    }

    public function printOrder($id)
    {
        $visitor = VisitorManagement::where('id', $id)->first();

        return view('visitorsManagement.print', compact('visitor'));
    }

    public function visitorApproval(VisitorManagement $visitor)
    {
        return view('visitorsManagement.approval', compact('visitor'));
    }

}
