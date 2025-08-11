<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('society.index');
    }

    public function show($slug)
    {
        return view('society.show', compact('slug'));
    }
}
