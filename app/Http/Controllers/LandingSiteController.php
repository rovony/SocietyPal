<?php

namespace App\Http\Controllers;

use App\Models\CustomWebPage;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;

class LandingSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = GlobalSetting::first();
        return view('landing-sites.index', compact('settings'));
    }

    public function showMenu()
    {
        $customMenu = CustomWebPage::all();
        $footerSetting = FooterSetting::where('language_id', request()->get('language_id'))->first();
        return view('layouts.landing', compact('customMenu', 'footerSetting'));
    }

}
