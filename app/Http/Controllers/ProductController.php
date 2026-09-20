<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Sub_Category;
use App\Models\Main_Category;
use App\Models\Packaging;
use App\Models\Product_Type;
use App\Models\Specification;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductDataTable $dataTable)
    {
        return $dataTable->render('pages.product.list.index');
    }

    public function fetchMainCategory(Request $request)
    {
        $data = [
            'sub_category' => Sub_Category::where('main_category_id', $request->main_category)->get(),
            'specification' => Specification::where('main_category_id', $request->main_category)->get(),
            'packaging' => Packaging::where('main_category_id', $request->main_category)->get(),
            'supplier' => Supplier::where('main_category_id', $request->main_category)->get(),
            'product_type' => Product_Type::where('main_category_id', $request->main_category)->get(),
            'brand' => Brand::where('main_category_id', $request->main_category)->get(),
        ];

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'main_category' => Main_Category::all(),
            'unit' => Unit::all(),
        ];

        return view('pages.product.list.modals.create-modal', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = new Product;
        $product->main_category_id = $request->main_category;
        $product->sub_category_id = $request->sub_category;
        $product->product_type_id = $request->product_type;
        $product->brand_id = $request->brand;
        $product->specification_id = $request->specification;
        $product->packaging_id = $request->packaging;
        $product->supplier_id = $request->supplier;
        $product->unit_id = $request->unit;
        $product->note = $request->note;

        $product->sku = $this->buildSku(
            $request->main_category,
            $request->sub_category,
            $request->brand,
            $request->product_type,
            $request->specification,
            $request->packaging,
            $request->supplier
        );

        if ($product->save()) {
            $request->session()->flash('success', 'New Product data successfully added.');
        } else {
            $request->session()->flash('danger', 'Change a few things up and try submitting again.');
        }

        return redirect(route('product.list.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $list)
    {
        $data = [
            'product' => $list,
            'main_category' => Main_Category::where('id', $list->main_category_id)->first()->main_category_name,
            'sub_category' => Sub_Category::where('id', $list->sub_category_id)->first()->category_name,
            'product_type' => Product_Type::where('id', $list->product_type_id)->first()->product_type_name,
            'brand' => Brand::where('id', $list->brand_id)->first()->brand_name,
            'specification' => Specification::where('id', $list->specification_id)->first()->specification_name,
            'packaging' => Packaging::where('id', $list->packaging_id)->first()->packaging_name,
            'supplier' => Supplier::where('id', $list->supplier_id)->first()->supplier_name,
            'unit' => Unit::where('id', $list->unit_id)->first()->unit_name,
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Product $list)
    {
        $data = [
            'product' => $list,
            'main_category' => Main_Category::all(),
            'unit' => Unit::all(),
        ];
        // dd($data['product']);
        return view('pages.product.list.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $list)
    {
        $product = $list;

        $product->main_category_id = $request->main_category;
        $product->sub_category_id = $request->sub_category;
        $product->product_type_id = $request->product_type;
        $product->brand_id = $request->brand;
        $product->specification_id = $request->specification;
        $product->packaging_id = $request->packaging;
        $product->supplier_id = $request->supplier;
        $product->unit_id = $request->unit;
        $product->note = $request->note;

        $product->sku = $this->buildSku(
            $request->main_category,
            $request->sub_category,
            $request->brand,
            $request->product_type,
            $request->specification,
            $request->packaging,
            $request->supplier
        );

        if ($list->save()) {
            $request->session()->flash('success', 'Product data successfully updated.');
        } else {
            $request->session()->flash('danger', 'Change a few things up and try submitting again.');
        }

        return redirect(route('product.list.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $list)
    {
        if ($list->delete()) {
            session()->flash('success', 'Product data successfully deleted.');
        } else {
            session()->flash('danger', 'Change a few things up and try submitting again.');
        }
        return redirect(route('product.list.index'));
    }

    public function getProductOptions($selected = null, $filter = null)
    {
        $filter   = request()->filter ?? $filter;
        $products = (is_null($filter))
            ? Product::all()
            : Product::where($filter)->get();

        $options = '<option value="" disabled selected>-- Select Product SKU --</option>';

        foreach ($products as $product) {
            if (!is_array($selected)) {
                $isSelected = ($product->id === $selected) ? 'selected ' : '';
            } else {
                $isSelected = (in_array($product->id, $selected)) ? 'selected ' : '';
            }

            $options .= "<option $isSelected"
                . "data-id='$product->id'"
                . "data-sku='$product->sku'"
                . "data-unit-id='$product->unit_id'"
                . "data-unit-name='" . $product->unit->unit_name . "'"
                . "value='" . $product->id . "'>"
                . $product->skuFormat()
                . "</option>";
        }
        return $options;
    }

    private function buildSku(
        int|string $mainCategoryId,
        int|string $subCategoryId,
        int|string $brandId,
        int|string $productTypeId,
        int|string $specificationId,
        int|string $packagingId,
        int|string $supplierId
    ): string {
        return (string) $mainCategoryId
            . Sub_Category::whereKey($subCategoryId)->value('code')
            . Brand::whereKey($brandId)->value('code')
            . Product_Type::whereKey($productTypeId)->value('code')
            . Specification::whereKey($specificationId)->value('code')
            . Packaging::whereKey($packagingId)->value('code')
            . Supplier::whereKey($supplierId)->value('code');
    }
}
