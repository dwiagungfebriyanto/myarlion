<?php

namespace App\Http\Controllers;

use App\DataTables\PackagingsDataTable;
use App\Models\Main_Category;
use App\Models\Packaging;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PackagingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PackagingsDataTable $dataTable)
    {
        return $dataTable->render('pages.product.packaging.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $data = [
            'main_category' => Main_Category::all(),
        ];

        return view('pages.product.packaging.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Packaging::where('main_category_id', $request->main_category_id)->count() == 9999) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of packaging.']);
        }

        $rules = [
            'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'packaging_name'           => 'required|string|max:255|unique:packagings,packaging_name,NULL,id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $packaging = new Packaging;
        $packaging->main_category_id = $request->main_category_id;
        $packaging->packaging_name = $request->packaging_name;

        $packaging_code =
            Packaging::where('main_category_id', $request->main_category_id)
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        $next_code = generateCode($packaging_code,1);
        // if ($next_code == 0) {
        //     $next_code = 1;
        // }
        $next_code =
        str_pad($next_code, 4, '0', STR_PAD_LEFT);
        $packaging->code = $next_code;

        $packaging->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $packaging = Packaging::findOrFail($id);
        $main_category = Main_Category::findOrFail($packaging->main_category_id);
        $data = [
            'main_category' => $main_category,
            'packaging' => $packaging,
        ];

        return view('pages.product.packaging.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Packaging $packaging)
    {
        $rules = [
            // 'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'packaging_name'           => 'required|string|max:255|unique:packagings,packaging_name,' . $packaging->id . ',id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $packaging->main_category_id = $request->main_category_id;
        $packaging->packaging_name = $request->packaging_name;

        $packaging->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
