<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ApartmentExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ApartmentManagement;
use App\Models\Tenant;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Apartment', society_modules()) || !in_array('Apartment', society_role_modules()), 403);
        abort_if((!user_can('Show Apartment')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('apartments.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(!in_array('Apartment', society_modules()), 403);
        abort_if(!in_array('Apartment', society_role_modules()), 403);

        $apartment = ApartmentManagement::with(['apartmentTenants', 'user'])->findOrFail($id);
    
        if (user_can('Show Apartment')) {
            return view('apartments.show', ['id' => $id]);
        }
    
        if (isRole() === 'Owner' && $apartment->user_id === user()->id) {
            return view('apartments.show', ['id' => $id]);
        }
    
        if (isRole() === 'Tenant') {
            $tenant = Tenant::where('user_id', user()->id)->first();
            if ($tenant && $apartment->apartmentTenants->where('tenant_id', $tenant->id)->isNotEmpty()) {
                return view('apartments.show', ['id' => $id]);
            }
        }
    
        abort(403);
    }
}
