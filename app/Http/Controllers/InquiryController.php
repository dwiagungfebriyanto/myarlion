<?php

namespace App\Http\Controllers;

use App\DataTables\InquiriesDataTable;
use App\DataTables\InquiryProductsDataTable;
use App\Models\Inquiry;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Imports\InquiryImport;
use App\Models\Channel;
use App\Models\Country;
use App\Models\Customer;
use App\Models\InquiryProduct;
use App\Models\Main_Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InquiriesDataTable $dataTable)
    {
        $data['marketings'] = User::marketing();

        return $dataTable->render('pages.inquiry.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'countries' => Country::all(),
            'channels' => Channel::all(),
            'websites' => Website::all(),
            'customers' => Customer::all(),
        ];
        return view('pages.inquiry.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inquiry = DB::transaction(function () use ($request) {
            $inquiry = new Inquiry;
            $inquiry->user_id = auth()->user()->id;
            $inquiry->date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $inquiry->channel_id = $request->channel;
            $inquiry->website_id = $request->website;
            $inquiry->customer_category = $request->customer_category;

            if ($request->customer_category === 'new_customer') {
                $customer = $this->createCustomerFromInquiryRequest($request);
                $inquiry->customer_id = $customer->id;
                $inquiry->name = $customer->name;
            } else {
                $customer = Customer::findOrFail($request->customer_name);
                $inquiry->customer_id = $customer->id;
                $inquiry->name = $customer->name;
            }

            $inquiry->country_code = $request->country;
            $inquiry->city = $request->city;
            $inquiry->phone = $request->phone;
            $inquiry->email = $request->email;
            $inquiry->note = $request->note;
            $inquiry->destination_id = $request->destination;
            $inquiry->platform = $request->platform;
            $inquiry->save();

            return $inquiry;
        });

        $data['main_category'] = Main_Category::all();
        $data['inquiry'] = $inquiry;

        $data['request'] = 'success';

        return view('pages.inquiry.components.product-content', $data);
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new InquiryImport, $request->excel_file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $request->session()->flash('danger', $failure->errors()[0]);

                return redirect()->back();
            }
        }

        return redirect()->back()->with('success', 'Data imported successfully.');
    }

    public function importTemplate()
    {
        $filePath = public_path('import_template/inquiry_import.xlsx');

        if (File::exists($filePath)) {
            return response()->download($filePath, 'inquiry_import.xlsx');
        } else {
            return back()->with('danger', 'File not found.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inquiry $inquiry)
    {
        $data = [
            'inquiry' => $inquiry,
            'country' => Country::where('id', $inquiry->country_code)->first(),
            'channel' => Channel::where('id', $inquiry->channel_id)->first(),
            'user' => User::where('id', $inquiry->user_id)->first(),
            'date' => \Carbon\Carbon::createFromFormat('Y-m-d', $inquiry->date)->format('d M Y'),
            'products' => InquiryProduct::where('inquiry_id', $inquiry->id)->get()->toArray(),
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inquiry $inquiry)
    {
        $data = [
            'inquiry' => $inquiry,
            'countries' => Country::all(),
            'channels' => Channel::all(),
            'websites' => Website::all(),
            'date' => \Carbon\Carbon::createFromFormat('Y-m-d', $inquiry->date)->format('d/m/Y'),
        ];
        return view('pages.inquiry.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inquiry $inquiry)
    {
        $inquiry->date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $inquiry->channel_id = $request->channel;
        $inquiry->website_id = $request->website;
        $inquiry->name = $inquiry->customer_id
            ? ($inquiry->customer?->name ?? $inquiry->name)
            : $request->name;
        $inquiry->country_code = $request->country;
        $inquiry->city = $request->city;
        $inquiry->phone = $request->phone;
        $inquiry->email = $request->email;
        $inquiry->note = $request->note;
        $inquiry->destination_id = $request->destination;
        $inquiry->platform = $request->platform;
        $inquiry->status = $request->status;
        $inquiry->save();

        $data['main_category'] = Main_Category::all();
        $data['inquiry'] = $inquiry;
        $data['request'] = 'success-updated';

        return view('pages.inquiry.components.product-content', $data);
    }

    private function createCustomerFromInquiryRequest(Request $request): Customer
    {
        $customer = new Customer;
        $customer->id = (Customer::max('id') ?? 0) + 1;
        $customer->name = $request->name;

        $customerCode = Customer::orderBy('code', 'asc')
            ->get()
            ->toArray();
        $customer->code = generateCode($customerCode, 1);
        $customer->email = $request->email;
        $customer->telp = $request->phone;
        $customer->country_id = $request->country;
        $customer->save();

        return $customer;
    }

    // store inquiry product
    public function storeProduct(Request $request)
    {
        $inq_product = new InquiryProduct;
        $inq_product->inquiry_id = $request->inquiry_id;
        $inq_product->main_category_id = $request->main_category;
        $inq_product->sub_category_id = $request->sub_category;
        $inq_product->product_type_id = $request->product_type;
        $inq_product->brand_id = $request->brand;
        $inq_product->specification_id = $request->specification;
        $inq_product->packaging_id = $request->packaging;

        $inq_product->save();
    }

    // edit inquiry product
    public function editProduct($id)
    {
        $productInq = InquiryProduct::find($id);
        return response()->json($productInq);
    }

    // update inquiry product
    public function updateProduct(Request $request, $id)
    {
        $inq_product = InquiryProduct::find($id);
        $inq_product->inquiry_id = $request->inquiry_id;
        $inq_product->main_category_id = $request->main_category;
        $inq_product->sub_category_id = $request->sub_category;
        $inq_product->product_type_id = $request->product_type;
        $inq_product->brand_id = $request->brand;
        $inq_product->specification_id = $request->specification;
        $inq_product->packaging_id = $request->packaging;
        $inq_product->save();
        return response()->json($message = 'updated');
    }

    // get datatables for inquiry products
    public function dataProduct(string $id)
    {
        $dataTable = app(InquiryProductsDataTable::class, ['inq_id' => $id, 'status' => Inquiry::find($id)->status]);
        return $dataTable->render('pages.inquiry.components.product-datatable');
    }

    public function destroyDataProduct(string $id)
    {
        $inq_product = InquiryProduct::find($id);
        $inq_product->delete();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inquiry $inquiry)
    {

        if ($inquiry->delete()) {
            session()->flash('success', 'Inquiry data successfully deleted.');
        } else {
            session()->flash('danger', 'Change a few things up and try submitting again.');
        }

        return redirect(route('inquiry.index'));
    }
}
