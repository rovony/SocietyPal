<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if((!user_can('Show Service Provider')), 403);
        return view('service-management.service-type');
    }

}
