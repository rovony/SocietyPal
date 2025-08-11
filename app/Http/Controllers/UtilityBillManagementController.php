<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Exports\UtilityBillExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UtilityBillManagement;
use App\Livewire\Dashboard\UtilityBills;

class UtilityBillManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Utility Bills', society_modules()) || !in_array('Utility Bills', society_role_modules()), 403);
        abort_if((!user_can('Show Utility Bills')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);
        return view('utilityBills.index');
    }

    public function printOrder($id)
    {
        $utilityBill = UtilityBillManagement::where('id', $id)->first();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $currencySymbol = Currency::find($currencyId)->currency_symbol;
        return view('utilityBills.print', compact('utilityBill','currencyId', 'currencySymbol'));
    }

}
