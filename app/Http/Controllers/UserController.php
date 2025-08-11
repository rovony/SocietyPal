<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('User', society_modules()) || !in_array('User', society_role_modules()), 403);
        abort_if((!user_can('Show User')) && (isRole() != 'Manager') && (isRole() != 'Guard'), 403);
        return view('users.index');
    }

    public function show($id)
    {
        abort_if(!in_array('User', society_modules()) || !in_array('User', society_role_modules()), 403);

        $user = User::with('roles')->findOrFail($id);

        if (user_can('Show User')) {
            return view('users.show', compact('user'));
        }
        if ((isRole() === 'Manager' || isRole() === 'Guard') && user()->id === $user->id) {
            return view('users.show', compact('user'));
        }

        abort(403);
    }

}
