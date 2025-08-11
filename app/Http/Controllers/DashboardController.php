<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\Models\BookAmenity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        if (user()->hasRole('Super Admin')) {
            return redirect(RouteServiceProvider::SUPERADMIN_HOME);
        }

        return view('dashboard.index');
    }

    public function superadmin()
    {
        return view('dashboard.superadmin');
    }

    public function accountUnverified()
    {
        return view('dashboard.pending-approval');
    }

}
