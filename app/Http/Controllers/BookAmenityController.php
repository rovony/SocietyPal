<?php

namespace App\Http\Controllers;

use App\Exports\BookAmenitiesExport;
use App\Models\BookAmenity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookAmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Book Amenity', society_modules()) || !in_array('Book Amenity', society_role_modules()), 403);
        abort_if((!user_can('Show Book Amenity')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);

        return view('book-amenity.index');
    }

}
