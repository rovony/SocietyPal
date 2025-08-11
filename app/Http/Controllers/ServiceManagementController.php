<?php

namespace App\Http\Controllers;

class ServiceManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Service Provider', society_modules()) || !in_array('Service Provider', society_role_modules()), 403);
        abort_if((!user_can('Show Service Provider')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);

        return view('service-management.index');
    }

}
