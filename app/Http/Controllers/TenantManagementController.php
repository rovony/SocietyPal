<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantsExport;
use App\Models\Tenant;

class TenantManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Tenant', society_modules()) || !in_array('Tenant', society_role_modules()), 403);
        abort_if((!user_can('Show Tenant')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);

        return view('tenants.index');
    }

    public function show($id)
    {
        abort_if(!in_array('Tenant', society_modules()) || !in_array('Tenant', society_role_modules()), 403);

        $tenant = Tenant::with('user', 'apartments')->findOrFail($id);

        if (user_can('Show Tenant')) {
            return view('tenants.show', compact('tenant'));
        }

        if (isRole() === 'Tenant' && user()->id === $tenant->user_id) {
            return view('tenants.show', compact('tenant'));
        }

        if (isRole() === 'Owner') {
            $ownerUserId = user()->id;

            $isTenantOfOwner = $tenant->apartments()->where('user_id', $ownerUserId)->exists();

            if ($isTenantOfOwner) {
                return view('tenants.show', compact('tenant'));
            }
        }

        abort(403);
    }
}
