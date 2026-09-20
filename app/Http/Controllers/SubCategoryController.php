<?php

namespace App\Http\Controllers;

use App\DataTables\Sub_CategoriesDataTable;
use App\Models\Main_Category;
use App\Models\Sub_Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Sub_CategoriesDataTable $dataTable)
    {
        return $dataTable->render('pages.product.sub_category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category' => Main_Category::all(),
        ];

        return view('pages.product.sub_category.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Sub_Category::where('main_category_id', $request->main_category_id)->count() == 10) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of sub categories.']);
        }

        $rules = [
            'main_category_id'      => 'required',
            'category_name'     =>
            'required|string|unique:sub_categories,category_name,NULL,id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $sub_category = new Sub_Category;
        $sub_category->main_category_id = $request->main_category_id;
        $sub_category->category_name = $request->category_name;

        $sub_category_code =
            Sub_Category::where('main_category_id', $request->main_category_id)
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        $next_code = generateCode($sub_category_code,0);
        $sub_category->code = $next_code;

        $sub_category->save();
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
    public function edit(Sub_Category $sub_category)
    {
        $data = [
            'sub_category' => $sub_category,
            'main_category' => Main_Category::findOrfail($sub_category->main_category_id),
        ];

        return view('pages.product.sub_category.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sub_Category $sub_category)
    {
        $rules = [
            // 'main_category_id'      => 'required',
            'category_name'     =>
            'required|string|unique:sub_categories,category_name,' . $sub_category->id . ',id,main_category_id,' . $sub_category->main_category_id,
        ];

        $request->validate($rules);

        // $sub_category->main_category_id = $request->main_category_id;
        $sub_category->category_name = $request->category_name;
        $sub_category->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
