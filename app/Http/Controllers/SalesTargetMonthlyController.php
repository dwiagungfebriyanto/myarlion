<?php

namespace App\Http\Controllers;

use App\DataTables\SalesTargetMonthlyDataTable;
use App\Http\Requests\SalesTargetMonthly\StoreSalesTargetMonthlyRequest;
use App\Http\Requests\SalesTargetMonthly\UpdateSalesTargetMonthlyRequest;
use App\Models\SalesTargetMonthly;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesTargetMonthlyController extends Controller
{
    private $pageTitle = 'Sales Target Monthly';

    /**
     * Display a listing of the resource.
     */
    public function index(SalesTargetMonthlyDataTable $dataTable)
    {
        $data['pageTitle']  = $this->pageTitle;
        $data['marketings'] = User::activeMarketing();

        return $dataTable->render('pages.sales_target_monthly.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesTargetMonthlyRequest $request): JsonResponse
    {
        $validated   = $request->validated();
        $month       = $validated['month'] . '-01';
        $marketingID = $validated['marketing'];

        $existsTarget = SalesTargetMonthly::where([
            'month'   => $month,
            'user_id' => $marketingID
        ])->exists();

        if (!$existsTarget) {
            try {
                $salesTargetMonthly          = new SalesTargetMonthly;
                $salesTargetMonthly->month   = $month;
                $salesTargetMonthly->user_id = $marketingID;
                $salesTargetMonthly->target  = $validated['target'];
                $salesTargetMonthly->save();
                
                return response()->json(APIresponse(true, "$this->pageTitle created successfully.", $salesTargetMonthly));
            } catch (\Throwable $th) {
                return response()->json(APIresponse(false, $th->getMessage()));
            }
        } else {
            $marketing = User::find($marketingID);

            return response()->json(APIresponse(
                false, 
                "$this->pageTitle for $marketing->name in " .date('Y F') ." already exists."
            ));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesTargetMonthlyRequest $request, SalesTargetMonthly $salesTargetMonthly): JsonResponse
    {
        $validated = $request->validated();

        try {
            $salesTargetMonthly->target = $validated['target'];

            if ($salesTargetMonthly->save()) {
                SalesTargetMonthly::updateMarketingSalestarget($salesTargetMonthly->user, $salesTargetMonthly->month);
            }
            
            return response()->json(APIresponse(true, "$this->pageTitle updated successfully.", $salesTargetMonthly));
        } catch (\Throwable $th) {
            return response()->json(APIresponse(false, $th->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesTargetMonthly $salesTargetMonthly): RedirectResponse
    {
        try {
            $salesTargetMonthly->delete();

            return redirect()->back()->with('success', "$this->pageTitle deleted successfully.");
        } catch (\Throwable $th) {
            return redirect()->back()->with('danger', $th->getMessage());
        }
    }
}
