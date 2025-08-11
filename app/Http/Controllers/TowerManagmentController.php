<?php

namespace App\Http\Controllers;

use App\Exports\TowerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TowerManagmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Tower', society_modules()) || !in_array('Tower', society_role_modules()), 403);
        abort_if((!user_can('Show Tower')), 403);
        return view('towers.index');
    }

    public function show(string $id)
    {
        abort_if(!in_array('Tower', society_modules()) || !in_array('Tower', society_role_modules()), 403);
        abort_if((!user_can('Show Tower')), 403);
        return view('towers.show', ['id' => $id]);
    }

}
