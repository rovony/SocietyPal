<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfflineRequestController extends Controller
{
    public function offlinePlanRequests()
    {
        abort_if( (isRole() != 'Admin'), 403);
        return view('maintenance.offline-request');
    }

}
