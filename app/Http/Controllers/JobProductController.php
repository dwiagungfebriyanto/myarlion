<?php

namespace App\Http\Controllers;

use App\DataTables\JobProductsDataTable;
use App\Models\BankAccount;
use App\Models\Job;
use App\Models\Main_Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobProductController extends Controller
{
    public function index(Job $job)
    {
        $data['job']            = $job;
        $data['mainCategories'] = Main_Category::orderBy('main_category_name')->get();
        $data['bankAccounts']   = BankAccount::orderBy('bank_id')->get();

        return app(JobProductsDataTable::class, ['job_id' => $job->id])
            ->render('pages.job.edit.tabs.product', $data);
    }

    public function store(Request $request, Job $job)
    {
        $rules = [
            'product_id' => "required|exists:products,id|unique:jobs_has_products,product_id,NULL,id,job_id,$job->id",
            'qty'        => 'required|numeric|gt:0',
            'price'      => 'required|numeric|min:0',
            'note'       => 'nullable|string',
        ];

        $request->validate($rules);

        $job->jobProducts()->attach($request->product_id, [
            'quantity' => $request->qty,
            'price'    => $request->price,
            'note'     => $request->note,
        ]);

        activity('job_product')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties($request->all())
            ->event('job_product_created')
            ->log('job_product_created');

        return response()->json(['message' => 'Data product has been added to Job.']);
    }

    public function editModal($job_id, $id)
    {
        $jobProduct = Job::query()
            ->join('jobs_has_products', 'jobs.id', '=', 'jobs_has_products.job_id')
            ->join('products', 'products.id', '=', 'jobs_has_products.product_id')
            ->select('jobs_has_products.*', 'products.supplier_id', 'products.main_category_id')
            ->where('jobs_has_products.job_id', $job_id)
            ->where('jobs_has_products.id', $id)
            ->firstOrFail();
        
        $data = [
            'jobProduct'     => $jobProduct,
            'mainCategories' => Main_Category::orderBy('main_category_name')->get(),
        ];

        return view('pages.job.edit.components.modal-edit-job-product', $data);
    }

    public function update(Request $request, Job $job, $job_product_id)
    {
        $jobProduct = DB::table('jobs_has_products')
            ->where('id', $job_product_id)
            ->where('job_id', $job->id)
            ->first();

        abort_if(is_null($jobProduct), 404);

        $productIdRules = ['required', 'exists:products,id'];

        if ((int) $request->product_id !== (int) $jobProduct->product_id) {
            $productIdRules[] = Rule::unique('jobs_has_products', 'product_id')
                ->where('job_id', $job->id);
        }

        $request->validate([
            'product_id' => $productIdRules,
            'qty'        => 'required|numeric|gt:0',
            'price'      => 'required|numeric|min:0',
            'note'       => 'nullable|string',
        ]);

        DB::table('jobs_has_products')
            ->where('id', $job_product_id)
            ->update([
                'product_id'  => $request->product_id,
                'quantity'    => $request->qty,
                'price'       => $request->price,
                'note'        => $request->note,
                'updated_at'  => now(),
            ]);

        activity('job_product')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties([
                'old' => $jobProduct,
                'new' => $request->all(),
            ])
            ->event('job_product_updated')
            ->log('job_product_updated');

        session()->flash('success', 'Data product has been updated to Job.');

        return redirect(route('job_product.index', $job));
    }

    public function destroy(Job $job, string $id)
    {
        $jobProduct = DB::table('jobs_has_products')
            ->where('id', $id)
            ->where('job_id', $job->id)
            ->first();

        abort_if(is_null($jobProduct), 404);

        DB::table('jobs_has_products')
            ->where('id', $id)
            ->delete();

        activity('job_product')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties(['old' => $jobProduct])
            ->event('job_product_deleted')
            ->log('job_product_deleted');

        session()->flash('success', 'Data product has been deleted from Job.');

        return redirect(route('job_product.index', $job));
    }

    public function getProductOptions($selected = null): JsonResponse
    {
        $selectedProductId = request('selected', $selected);
        $selectedProductId = ($selectedProductId === '' || is_null($selectedProductId))
            ? null
            : (int) $selectedProductId;

        $products = Product::query()
            ->with([
                'product_type:id,product_type_name',
                'specification:id,specification_name',
                'packaging:id,packaging_name',
                'unit:id,unit_name',
            ])
            ->when(request('main_category_id'), function ($query, $mainCategoryId) {
                $query->where('main_category_id', $mainCategoryId);
            })
            ->when(request('supplier_id'), function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->orderBy('sku')
            ->get();

        $placeholderSelected = is_null($selectedProductId) ? 'selected' : '';
        $options = "<option value='' disabled $placeholderSelected>-- Select product --</option>";

        foreach ($products as $product) {
            $isSelected = ((string) $product->id === (string) $selectedProductId) ? 'selected' : '';
            $label = e($product->skuFormat());
            $unitName = e($product->unit?->unit_name ?? '-');
            $sku = e($product->sku);

            $options .= "<option value='$product->id' data-sku='$sku' data-stock='$product->qty' data-unit-name='$unitName' $isSelected>$label</option>";
        }

        return response()->json(['options' => $options]);
    }

    public function getProductDetail(Product $product): JsonResponse
    {
        $product->load('unit:id,unit_name');

        return response()->json([
            'id'   => $product->id,
            'sku'  => $product->sku,
            'qty'  => $product->qty,
            'unit' => $product->unit,
        ]);
    }
}
