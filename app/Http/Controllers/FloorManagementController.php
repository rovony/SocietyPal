<?php

namespace App\Http\Controllers;

use App\Exports\FloorExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FloorManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Floor', society_modules()) || !in_array('Floor', society_role_modules()), 403);
        abort_if((!user_can('Show Floor')), 403);
        return view('floors.index');
    }

    public function show(string $id)
    {
        abort_if(!in_array('Floor', society_modules()) || !in_array('Floor', society_role_modules()), 403);
        abort_if((!user_can('Show Floor')), 403);
        return view('floors.show', ['id' => $id]);
    }

}
