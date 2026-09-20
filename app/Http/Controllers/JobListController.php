<?php

namespace App\Http\Controllers;

use App\DataTables\JobsDataTable;
use App\Imports\JobImport;
use App\Models\Channel;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\InventoryStock;
use App\Models\Job;
use App\Models\JobCommission;
use App\Models\OutcomeCheque;
use App\Models\JobPoStock;
use App\Models\JobStatement;
use App\Models\Main_Category;
use App\Models\SalesTarget;
use App\Models\SalesTargetMonthly;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobInvoiceExport;

class JobListController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(JobsDataTable $dataTable)
    {
        $data['customers']  = Customer::all();
        $data['marketings'] = User::activeMarketing();

        return $dataTable->render('pages.job.index', $data);
    }

    // public function fetchInventoryStock(Request $request)
    // {
    //     $data = InventoryStock::where('warehouse_id', $request->wharehouse)
    //         ->where(function ($query) {
    //             $query->where('amount', '>', 0)
    //                 ->orWhere('weight', '>', 0);
    //         })->join('products', 'products.id', '=', 'inventory_stocks.product_id')
    //         ->join('units', 'units.id', '=', 'inventory_stocks.unit_id')
    //         ->join('product_types', 'product_types.id', '=', 'products.product_type_id')
    //         ->join('specifications', 'specifications.id', '=', 'products.specification_id')
    //         ->join('packagings', 'packagings.id', '=', 'products.packaging_id')
    //         ->join('warehouses', 'warehouses.id', '=', 'inventory_stocks.warehouse_id')
    //         ->select('inventory_stocks.*', 'products.sku as sku', 'product_types.product_type_name as product_type_name', 'specifications.specification_name as specification_name', 'packagings.packaging_name as packaging_name', 'warehouses.warehouse_name as warehouse_name', 'units.unit_name as unit_name')
    //         ->get();

    //     return response()->json($data);
    // }

    public function fetchStock($id)
    {
        $data = InventoryStock::with('unit')->where('id', $id)->first();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jobs =
            Job::orderBy('code', 'asc')
            ->get()
            ->toArray();

        $data = [
            'job_id'         => generateCode($jobs, 60),
            'customer'       => Customer::all(),
            'countries'      => Country::all(),
            'currencies'     => Currency::all(),
            'channels'       => Channel::all(),
            'inquiries'      => Inquiry::whereNull('job_id')->orderBy('date', 'desc')->get(),
            'mainCategories' => Main_Category::orderBy('main_category_name')->get(),
            'id'             => Job::max('id') + 1,
        ];

        return view('pages.job.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'job_id'                           => 'required|unique:jobs,code',
            'source'                           => 'required|in:inquiry,non_inquiry,reguler',
            'currency'                         => 'required|exists:currencies,id',
            'amount'                           => 'nullable|numeric',
            'est_profit'                       => 'nullable|numeric',
            'job_products'                     => 'required|array|min:1',
            'job_products.*.product_id'        => 'required|distinct|exists:products,id',
            'job_products.*.qty'               => 'required|numeric|gt:0',
            'job_products.*.price'             => 'required|numeric|min:0',
            'job_products.*.note'              => 'nullable|string',
        ];

        if ($request->source === 'inquiry') {
            $rules['inquiry'] = 'required|exists:inquiries,id';
        } else {
            $rules['customer'] = 'required|exists:customers,id';
            $rules['channel']  = 'required|exists:channels,id';
            $rules['country']  = 'required|exists:countries,id';
        }

        $validated = $request->validate($rules);

        try {
            $job = DB::transaction(function () use ($validated) {
                $job              = new Job;
                $job->id          = (Job::max('id') ?? 0) + 1;
                $job->code        = $validated['job_id'];
                $job->source      = $validated['source'];
                $job->employee_id = auth()->id();
                $job->currency_id = $validated['currency'];
                $job->amount      = $validated['amount'] ?? 0;
                $job->est_profit  = $validated['est_profit'] ?? 0;

                if ($validated['source'] === 'inquiry') {
                    $this->storeInquiryJob($job, (int) $validated['inquiry']);
                } else {
                    $job->customer_id = $validated['customer'];
                    $job->country_id  = $validated['country'];
                    $job->channel_id  = $validated['channel'];

                    if (!$job->save()) {
                        throw new \RuntimeException('Failed to save job.');
                    }
                }

                $persistedJobId = Job::where('code', $validated['job_id'])->value('id');
                if (empty($persistedJobId) || (int) $persistedJobId <= 0) {
                    throw new \RuntimeException('Failed to resolve persisted job ID for job products.');
                }
                $job->id = (int) $persistedJobId;

                $pivotData = collect($validated['job_products'])
                    ->mapWithKeys(function ($product) {
                        return [
                            (int) $product['product_id'] => [
                                'quantity' => $product['qty'],
                                'price'    => $product['price'],
                                'note'     => $product['note'] ?? null,
                            ],
                        ];
                    })
                    ->all();

                $job->jobProducts()->attach($pivotData);

                activity('job_product')
                    ->causedBy(auth()->user())
                    ->performedOn($job)
                    ->withProperties(['job_products' => $validated['job_products']])
                    ->event('job_product_created')
                    ->log('job_product_created');

                return $job;
            });
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to save Job and Job Product.',
                'error'   => $th->getMessage(),
            ], 500);
        }

        $savedJobId = Job::where('code', $validated['job_id'])->value('id');

        if (empty($savedJobId) || (int) $savedJobId <= 0) {
            return response()->json([
                'message' => 'Job was created but failed to resolve the saved Job ID.',
            ], 500);
        }

        session()->flash('success', 'Data Job has been saved successfully.');

        return response()->json([
            'job_id'       => (int) $savedJobId,
            'redirect_url' => route('job_product.index', (int) $savedJobId),
            'message'      => 'Data Job has been saved successfully.',
        ]);
    }

    public function storeInquiryJob($job, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $customer = $this->resolveInquiryCustomer($inquiry);

        $job->customer_id = $customer->id;
        $job->country_id  = $inquiry->destination_id ?? null;

        if (!$job->save()) {
            throw new \RuntimeException('Failed to save inquiry job.');
        }

        $inquiry->status = 'sales';
        $inquiry->job_id = $job->id;
        $inquiry->save();
    }

    private function resolveInquiryCustomer(Inquiry $inquiry): Customer
    {
        if (!is_null($inquiry->customer_id)) {
            $customer = Customer::find($inquiry->customer_id);
            if (!is_null($customer)) {
                return $customer;
            }
        }

        $matchedCustomer = Customer::where('name', $inquiry->name)
            ->orderBy('id')
            ->first();

        if (!is_null($matchedCustomer)) {
            $inquiry->customer_id = $matchedCustomer->id;
            $inquiry->name = $matchedCustomer->name;
            $inquiry->save();

            return $matchedCustomer;
        }

        if ($inquiry->customer_category === 'new_customer') {
            $customer = $this->createCustomerFromInquiry($inquiry);
            $inquiry->customer_id = $customer->id;
            $inquiry->name = $customer->name;
            $inquiry->save();

            return $customer;
        }

        throw new \RuntimeException('Inquiry customer is not mapped.');
    }

    private function createCustomerFromInquiry(Inquiry $inquiry): Customer
    {
        $customer = new Customer;
        $customer->id = (Customer::max('id') ?? 0) + 1;
        $customer->name = $inquiry->name;

        $customerCode = Customer::orderBy('code', 'asc')->get()->toArray();
        $customer->code = generateCode($customerCode, 1);
        $customer->email = $inquiry->email;
        $customer->telp = $inquiry->phone;
        $customer->country_id = $inquiry->country_code;
        $customer->save();

        return $customer;
    }

    public function import(Request $request)
    {
        Excel::import(new JobImport, $request->excel_file);

        return redirect(route('job.list.index'))->with('success', 'Data imported successfully.');
    }

    public function exportInvoice(Request $request, Job $job)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        $fileName = 'Invoice_' . ($job->code ?? $job->id) . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new JobInvoiceExport($job->id, $request->bank_account_id), $fileName);
    }

    public function importTemplate()
    {
        $filePath = public_path('import_template/job_import.xlsx');

        if (File::exists($filePath)) {
            return response()->download($filePath, 'job_import.xlsx');
        } else {
            return back()->with('danger', 'File not found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $job = Job::find($id);

        $data = [
            'job'        => $job,
            'channels'   => Channel::all(),
            'countries'  => Country::all(),
            'currencies' => Currency::all(),
            'customer'   => Customer::all(),
            'marketing'  => User::find($job->employee_id),
        ];

        return response()
            ->view('pages.job.edit', $data)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $job = Job::find($id);

        $prevPeriod          = Str::limit($job->period_job, 4, '');  // untuk pembanding jika ada perubahan periode job
        $prevYearMonthPeriod = Str::limit($job->period_job, 7, '');  // untuk pembanding jika ada perubahan periode job

        $job->customer_id         = $request->customer;
        $job->currency_id         = $request->currency;
        $job->country_id          = $request->country;
        $job->est_profit          = isset($request->est_profit) ? $request->est_profit : 0;
        $job->channel_id          = $request->channel;
        $job->period_job          = $request->period;

        if ($job->save()) {
            // Update sales target achievement (ditangani JobObserver)
            updateTargetAchievements($job->marketing, $request->period);

            // Update sales target achievement yang lama
            // jika periode job berubah
            $yearPeriod = Str::limit($request->period, 4, '');

            if (!empty($prevPeriod) && $yearPeriod !== $prevPeriod) {
                SalesTarget::updateMarketingSalestarget($job->marketing, $prevPeriod);
            }
            
            $yearMonthPeriod = Str::limit($request->period, 7, '');
            
            if (!empty($prevYearMonthPeriod) && $yearMonthPeriod !== $prevYearMonthPeriod) {
                SalesTargetMonthly::updateMarketingSalestarget($job->marketing, $prevYearMonthPeriod);
            }

            $request->session()->flash('success', "Job data successfully updated");
        } else {
            $request->session()->flash('danger', 'Change a few things up and try submitting again.');
        }

        return redirect(route('job.list.edit', $job->id));
    }

    public function getTotalExpenseStock($id)
    {
        // expenses
        $total_cost     = OutcomeCheque::where('code', $id)->sum('amount');
        $total_stock    = JobStatement::where('job_id', $id)->sum('total');
        $total_po_stock = JobPoStock::where('job_id', $id)->sum('amount');
        $total_expenses = $total_cost + $total_stock + $total_po_stock;

        $total_commission = JobCommission::where('job_id', $id)->sum('nominal');

        // income
        $total_income     = Job::findOrFail($id)->totalIncome();

        $gross_profit = $total_income - $total_expenses;
        $net_profit = $gross_profit - $total_commission;

        $data = [
            'total_cost'       => $total_cost,
            'total_stock'      => $total_stock,
            'total_po_stock'   => $total_po_stock,
            'total_expenses'   => $total_expenses,
            'total_commission' => $total_commission,

            'gross_profit' => $gross_profit,
            'net_profit'   => $net_profit,
        ];

        return $data;
    }

    public function getStock()
    {
        $stock = InventoryStock::with([
            'product' => function ($query) {
                $query
                    ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
                    ->join('specifications', 'products.specification_id', '=', 'specifications.id')
                    ->join('packagings', 'products.packaging_id', '=', 'packagings.id')
                    ->select('products.*', 'product_types.product_type_name as product_type_name', 'specifications.specification_name as specification_name', 'packagings.packaging_name as packaging_name');
            },
            'warehouse',
            'inventoryIn.poStock',
        ])->get();
        return response()->json($stock);
    }
}
