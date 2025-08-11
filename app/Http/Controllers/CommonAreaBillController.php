<?php

namespace App\Http\Controllers;

use App\Models\Society;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\CommonAreaBills;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommonAreaBillExport;

class CommonAreaBillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Common Area Bills', society_modules()) || !in_array('Common Area Bills', society_role_modules()), 403);
        abort_if((!user_can('Show Common Area Bills')) && (isRole() != 'Owner') && (isRole() != 'Tenant'), 403);
        return view('common_area_bills.index');

    }

    public function printOrder($id)
    {
        $utilityBill = CommonAreaBills::where('id', $id)->first();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $currencySymbol = Currency::find($currencyId)->currency_symbol;
        return view('utilityBills.print', compact('utilityBill','currencyId', 'currencySymbol'));
    }

}
