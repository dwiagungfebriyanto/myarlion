<?php

namespace App\Http\Controllers;

use App\DataTables\SalesTargetDataTable;
use App\Http\Requests\SalesTarget\StoreSalesTargetRequest;
use App\Http\Requests\SalesTarget\UpdateSalesTargetRequest;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Http\Request;

class SalesTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SalesTargetDataTable $dataTable)
    {
        $data['pageTitle'] = 'Sales Target';
        $data['marketings'] = User::marketing();

        return $dataTable->render('pages.sales_target.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesTargetRequest $request)
    {
        $request->validated();

        $salesTarget = new SalesTarget;
        $salesTarget->user_id = $request->marketing;
        $salesTarget->target = $request->target;
        $salesTarget->year = $request->year;
        $salesTarget->save();

        return 0;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesTarget $salesTarget)
    {
        return response()->json($salesTarget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesTargetRequest $request, SalesTarget $salesTarget)
    {
        $request->validated();

        $salesTarget->target = $request->target;

        if ($salesTarget->save()) {
            $salesTarget::updateMarketingSalestarget($salesTarget->user, $salesTarget->year);
        }

        return 0;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesTarget $salesTarget)
    {
        $salesTarget->delete();

        return redirect(route('sales-target.index'));
    }
}
