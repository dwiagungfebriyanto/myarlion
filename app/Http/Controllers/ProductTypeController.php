<?php

namespace App\Http\Controllers;

use App\DataTables\ProductTypesDataTable;
use App\Models\Main_Category;
use App\Models\Product_Type;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpParser\Node\Expr\Cast\String_;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductTypesDataTable $dataTable)
    {
        return $dataTable->render('pages.product.product_type.index');
    }

    public function fetchMainCategory(Request $request)
    {
        $product_type_code =
            Product_Type::where('main_category_id', $request->main_category_id)
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        $next_code = generateCode($product_type_code,0);
        // if ($next_code == 0) {
        //     $next_code = 1;
        // }
        $next_code =
        str_pad($next_code, 3, '0', STR_PAD_LEFT);
        $data = [
            'product_type_code' => $next_code,
        ];

        return response()->json($data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category_id' => Main_Category::all(),
        ];

        return view('pages.product.product_type.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        if (Product_Type::where('main_category_id', $request->main_category_id)->count() == 999) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of product type.']);
        }
        $rules = [
            'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'code'                        => 'required|string|max:3|unique:product_types,code,NULL,id,main_category_id,' . $request->main_category_id,
            'product_type_name'            => 'required|string|max:255|unique:product_types,product_type_name,NULL,id,main_category_id,' . $request->main_category_id,
        ];

        $request->validate($rules);

        $product_type = new Product_Type;
        $product_type->main_category_id = $request->main_category_id;
        if (strlen($request->code) == 3) {
            $product_type->code = $request->code;
        } else {
            $product_type->code
            = str_pad($request->code, 3, '0', STR_PAD_LEFT);
        }
        $product_type->product_type_name = $request->product_type_name;
        $product_type->save();

    }

    /**
     * Display the specified resource.
     */
    public function show(Product_Type $product_Type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product_Type = Product_Type::find($id);
        $main_category = Main_Category::find($product_Type->main_category_id);
        $data = [
            'product_type' => $product_Type,
            'main_category' => $main_category,
        ];

        return view('pages.product.product_type.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product_Type = Product_Type::find($id);
        $rules = [
            // 'main_category_id'             => 'required|exists:\App\Models\Main_Category,id',
            'code'                        => 'required|string|max:3|unique:product_types,code,' . $product_Type->id . ',id,main_category_id,' . $product_Type->main_category_id,
            'product_type_name'            => 'required|string|max:255|unique:product_types,product_type_name,' . $product_Type->id . ',id,main_category_id,' . $product_Type->main_category_id,
        ];

        $request->validate($rules);

        // $product_Type->main_category_id = $request->main_category_id;
        if (strlen($request->code) == 3) {
            $product_Type->code = $request->code;
        } else {
            $product_Type->code
            = str_pad($request->code, 3, '0', STR_PAD_LEFT);
        }
        $product_Type->product_type_name = $request->product_type_name;
        $product_Type->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product_Type $product_Type)
    {
        //
    }
}
