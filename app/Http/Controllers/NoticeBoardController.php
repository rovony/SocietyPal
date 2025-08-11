<?php

namespace App\Http\Controllers;

use App\Exports\NoticesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NoticeBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Notice Board', society_modules()) || !in_array('Notice Board', society_role_modules()), 403);
        abort_if((!user_can('Show Notice Board')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('notices.index');
    }

}
