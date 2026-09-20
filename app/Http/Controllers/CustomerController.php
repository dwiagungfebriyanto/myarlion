<?php

namespace App\Http\Controllers;

use App\DataTables\CustomersDataTable;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Imports\CustomerImport;
use App\Imports\CustomerImportUpdate;
use App\Exports\CustomerExport;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CustomersDataTable $dataTable)
    {
        $data = [
            'countries' => Country::all(),
        ];

        return $dataTable->render('pages.customer.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.customer.modals.create-modal');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $customer       = new Customer;
        $customer->id   = Customer::max('id') + 1;
        $customer->name = $validated['name'];

        $customer_code = Customer::orderBy('code', 'asc')
            ->get()
            ->toArray();

        $next_code      = generateCode($customer_code, 1);
        $customer->code = $next_code;

        $customer->tax         = $validated['tax'];
        $customer->address     = $validated['address'];
        $customer->country_id  = $validated['country'] ?? null;
        $customer->telp        = $validated['telp'];
        $customer->no_rekening = $validated['no_rekening'];
        $customer->fax         = $validated['fax'];
        $customer->email       = $validated['email'];
        $customer->contact     = $validated['contact'];
        $customer->note        = $validated['note'];

        try {
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Customer data successfully saved.',
                'data'    => $customer
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $data['customer']  = $customer;
        $data['countries'] = Country::all();

        return view('pages.customer.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Customer $customer)
    {
        $validated = $request->validated();

        $customer->name        = $validated['name'];
        $customer->tax         = $validated['tax'];
        $customer->address     = $validated['address'];
        $customer->country_id  = $validated['country'] ?? null;
        $customer->telp        = $validated['telp'];
        $customer->no_rekening = $validated['no_rekening'];
        $customer->fax         = $validated['fax'];
        $customer->email       = $validated['email'];
        $customer->contact     = $validated['contact'];
        $customer->note        = $validated['note'];

        try {
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Customer data successfully updated.',
                'data'    => $customer
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new CustomerImport, $request->excel_file);
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
        $fileName = 'customer_import.xlsx';
        $filePath = public_path("import_template/$fileName");

        if (File::exists($filePath)) {
            return response()->download($filePath, $fileName);
        } else {
            return back()->with('danger', 'File not found.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->delete()) {
            session()->flash('success', 'Customer data successfully deleted.');
        } else {
            session()->flash('danger', 'Change a few things up and try to remove again.');
        }

        return redirect()->route('customer.index');
    }

    public function importUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file_update' => 'required|file|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('danger', 'Please upload a valid Excel file with a maximum size of 5 MB');
        }

        $file = $request->file('excel_file_update');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('danger', 'Please upload a valid Excel file (xlsx/xls)');
        }

        try {
            Excel::import(new CustomerImportUpdate, $file);

            return redirect()->back()->with('success', 'Customer data updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('danger', 'Error updating Customer data: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        $fromCode = $request->input('fromCode');
        $toCode = $request->input('toCode');

        return Excel::download(new CustomerExport($fromCode, $toCode), 'customers_template_update.xlsx');
    }
}
