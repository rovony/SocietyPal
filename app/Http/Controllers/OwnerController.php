<?php

namespace App\Http\Controllers;

use App\Exports\OwnersExport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Owner', society_modules()) || !in_array('Owner', society_role_modules()), 403);
        abort_if((!user_can('Show Owner')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);

        return view('owners.index');
    }

    public function show($id)
    {
        abort_if(!in_array('Owner', society_modules()) || !in_array('Owner', society_role_modules()), 403);
        abort_if((!user_can('Show Owner')) && (isRole() != 'Owner'), 403);
        $owner = User::findOrFail($id);

        if (user_can('Show Owner')) {
            return view('owners.show', compact('owner'));
        }

        if (isRole() === 'Owner' && user()->id === $owner->id) {
            return view('owners.show', compact('owner'));
        }

        abort(403);
    }
}
