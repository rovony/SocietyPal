<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\Currency;
use App\Models\Amenities;
use App\Models\BookAmenity;
use Illuminate\Http\Request;
use App\Exports\AmenitiesExport;
use Maatwebsite\Excel\Facades\Excel;

class AmenitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Amenities', society_modules()) || !in_array('Amenities', society_role_modules()), 403);
        abort_if((!user_can('Show Amenities')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('amenities.index');
    }

    public function printOrder($id)
    {
        $amenity = BookAmenity::with('user', 'amenity')->findOrFail($id);

        $bookings = BookAmenity::where('unique_id', $amenity->unique_id)
                                      ->orderBy('booking_time')->get();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $currencySymbol = Currency::find($currencyId)->currency_symbol;
        return view('amenities.print', compact('amenity', 'bookings','currencyId', 'currencySymbol'));
    }
}
