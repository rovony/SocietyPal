<?php

namespace App\Http\Controllers;

class ServiceClockInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Service Time Logging', society_modules()) || !in_array('Service Time Logging', society_role_modules()), 403);
        abort_if((!user_can('Show Service Time Logging')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('service-clock-in-out.index');
    }

}
