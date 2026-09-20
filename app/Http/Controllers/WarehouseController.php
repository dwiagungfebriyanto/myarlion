<?php

namespace App\Http\Controllers;

use App\DataTables\WarehouseDataTable;
use App\Models\Warehouse;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WarehouseDataTable $datatable)
    {
        $data['pageTitle'] = 'Warehouse';

        return $datatable->render('pages.warehouse.index', $data);
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
    public function store(StoreWarehouseRequest $request)
    {
        $request->validated();

        $warehouse                 = new Warehouse;
        $warehouse->warehouse_name = $request->warehouse_name;
        $warehouse->status         = $request->status ? 'Active' : 'Inactive';
        $warehouse->address        = $request->address;
        $warehouse->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $data['actionUrl'] = route('warehouse.update', $warehouse);
        $data['warehouse'] = $warehouse;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $request->validated();

        $warehouse->warehouse_name = $request->warehouse_name;
        $warehouse->status         = $request->status ? 'Active' : 'Inactive';
        $warehouse->address        = $request->address;
        $warehouse->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        //
    }

    public function getWarehouseOptions($except=null, $selected=null)
    {
        if (!is_array($except) && !is_null($except)) {
            $except = array($except);
        }

        $warehouses = Warehouse::getActiveWarehouse($except);

        return selectGenerate('Warehouse', $warehouses, 'id', 'warehouse_name', $selected);
    }
}
