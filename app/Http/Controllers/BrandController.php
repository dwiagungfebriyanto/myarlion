<?php

namespace App\Http\Controllers;

use App\DataTables\BrandsDataTable;
use App\Models\Brand;
use App\Models\Main_Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BrandsDataTable $dataTable)
    {
        return $dataTable->render('pages.product.brand.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category' => Main_Category::all(),
        ];

        return view('pages.product.brand.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Brand::where('main_category_id', $request->main_category_id)->count() == 10) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of brand.']);
        }

        $rules = [
            'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'brand_name'                      => 'required|string|max:255|unique:brands,brand_name,NULL,id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);


        $brand = new Brand;
        $brand->main_category_id = $request->main_category_id;
        $brand->brand_name = $request->brand_name;

        $brand_code =
            Brand::where('main_category_id', $request->main_category_id)
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        $next_code = generateCode($brand_code,0);
        $brand->code = $next_code;
        // dd($next_code);
        $brand->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $data = [
            'brand' => $brand,
            'main_category' => Main_Category::find($brand->main_category_id),
        ];
        // dd($data);
        return view('pages.product.brand.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'brand_name' => 'required|string|max:255|unique:brands,brand_name,' . $brand->id . ',id,main_category_id,' . $brand->main_category_id,
        ];

        $request->validate($rules);

        $brand->brand_name = $request->brand_name;
        $brand->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
