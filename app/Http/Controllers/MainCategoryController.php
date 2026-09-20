<?php

namespace App\Http\Controllers;

use App\DataTables\Main_CategoriesDataTable;
use App\Models\Main_Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MainCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Main_CategoriesDataTable $dataTable)
    {
        return $dataTable->render('pages.product.main_category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category' => Main_Category::all(),
        ];

        return view('pages.product.main_category.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Main_Category::count() == 9) {

            // dd($request->session());
            //  $request->session()->flash('danger', 'You have reached the maximum number of main categories.');
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of main categories.']);
        }

        $rules = [
            'code'                  => 'required|unique:main_categories,code',
            'main_category_name'    => 'required|string|unique:main_categories,main_category_name',
            // 'main_category_icon'    => 'image|mimes:jpeg,png,jpg|max:2048',
        ];

        $request->validate($rules);

        $main_category = new Main_Category();
        $main_category->code = $request->code;
        $main_category->main_category_name = $request->main_category_name;
        // $main_category->main_category_icon = $request->main_category_icon;
        $main_category->save();
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
    public function edit(Main_Category $main_category)
    {
        $data = [
            'main_category' => $main_category,
        ];

        return view('pages.product.main_category.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Main_Category $main_category)
    {
        $rules = [
            // 'code'                  => 'required|unique:main_categories,code,' . $main_category->id,
            'main_category_name'    => 'required|string|unique:main_categories,main_category_name,' . $main_category->id,
            // 'main_category_icon'    => 'image|mimes:jpeg,png,jpg|max:2048',
        ];

        $request->validate($rules);


        $main_category->main_category_name = $request->main_category_name;
        $main_category->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $main_category = Main_Category::find($id);

        if ($main_category->delete()) {
            session()->flash('success', 'Product data successfully deleted.');
        } else {
            session()->flash('danger', 'Change a few things up and try submitting again.');
        }
        // kurang erorr handing jika mendelete main category yang sudah di pakai di sub category atau di child table yg berelasi dengan main category seharusnya pake validasi terlebih dahulu

        // Tidak PERLU DELETE MAIN CATEGORY KARENA JIKA DI DELETE MAKA SUB CATEGORY DAN CHILD CATEGORY AKAN TERHAPUS JUGA


        return redirect(route('product.main-category.index'));
    }
}
