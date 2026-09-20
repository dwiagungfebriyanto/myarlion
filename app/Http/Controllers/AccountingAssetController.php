<?php

namespace App\Http\Controllers;

use App\DataTables\PoAssetDataTable;
use App\Http\Requests\StorePoAssetRequest;
use App\Http\Requests\UpdatePoAssetRequest;
use App\Models\PoAsset;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountingAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PoAssetDataTable $poAssetDataTable)
    {
        $data['pageTitle'] = 'PO Asset';
        $data['suppliers'] = Supplier::orderBy('code')->get();

        return $poAssetDataTable->render('pages.po_asset.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePoAssetRequest $request)
    {
        $request->validated();

        $PoAsset              = new PoAsset;
        $PoAsset->unique_id   = $request->id;
        $PoAsset->supplier_id = $request->supplier;
        $PoAsset->note        = $request->note;
        $PoAsset->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PoAsset $asset): JsonResponse
    {
        $suppliers = Supplier::orderBy('code')->get();

        $data['actionUrl']       = route('accounting.asset.update', $asset);
        $data['poAsset']         = $asset;
        $data['supplierOptions'] = '<option disabled selected>-- Select Supplier --</option>';

        foreach ($suppliers as $supplier) {
            $isSelected = ($supplier->id === $asset->supplier_id)
                            ? 'selected'
                            : '';

            $data['supplierOptions'] .= "<option value='$supplier->id' $isSelected>"
                                        .'[' .$supplier->mainCategory->main_category_name ."] $supplier->code - $supplier->supplier_name"
                                        ."</option>";
        }

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePoAssetRequest $request, PoAsset $asset)
    {
        $request->validated();

        $asset->unique_id   = $request->id;
        $asset->supplier_id = $request->supplier;
        $asset->note        = $request->note;
        $asset->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PoAsset $asset)
    {
        $asset->delete();
    }

    public function getIdSuggestion()
    {
        $lastRecord = PoAsset::latest()->first();

        $idNumber = ($lastRecord)
            ? (int) str_replace('ASS', '', $lastRecord->unique_id) + 1
            : 1;

        return sprintf("%03d", $idNumber);
    }
}
