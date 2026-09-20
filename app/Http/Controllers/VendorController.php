<?php

namespace App\Http\Controllers;

use App\DataTables\VendorsDataTable;
use App\Imports\VendorImport;
use App\Imports\VendorImportUpdate;
use App\Exports\VendorExport;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;



class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(VendorsDataTable $dataTable)
    {
        return $dataTable->render('pages.vendor.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return response()->json(['success' => true, 'new_code' => Vendor::getNewCode()]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'vendor_name' => 'required|string|max:255|unique:vendors,vendor_name',
            'email'       => 'email|max:255|string|nullable|unique:vendors,email,',
            'telp'        => 'max:15|nullable',
            'fax'         => 'numeric|nullable',
        ];

        $request->validate($rules);

        $vendorCode = Vendor::orderBy('code', 'asc')
            ->get()
            ->toArray();

        $nextCode = generateCode($vendorCode, 1);

        $vendor              = new Vendor;
        $vendor->code        = $nextCode;
        $vendor->vendor_name = $request->vendor_name;
        $vendor->address     = $request->address;
        $vendor->email       = $request->email;
        $vendor->telp        = $request->telp;
        $vendor->fax         = $request->fax;
        $vendor->contact     = $request->contact;
        $vendor->pkp         = $request->pkp;
        $vendor->no_rekening = $request->no_rekening;
        $vendor->note        = $request->note;
        $vendor->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        $data = [
            'vendor' => $vendor,
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        $data = [
            'vendor' => $vendor,
        ];

        // dd($data);
        return view('pages.vendor.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {

        $rules = [
            'vendor_name' =>
            'required|max:255|unique:vendors,vendor_name,' . $vendor->id,

            'email' =>
            'email|max:255|nullable|unique:vendors,email,' . $vendor->id,

            'telp' => 'max:15|nullable',
            'fax' => 'numeric|nullable',
        ];

        $request->validate($rules);

        $vendor->vendor_name = $request->vendor_name;
        $vendor->address = $request->address;
        $vendor->email = $request->email;
        $vendor->telp = $request->telp;
        $vendor->fax = $request->fax;
        $vendor->contact = $request->contact;
        $vendor->pkp = $request->pkp;
        $vendor->no_rekening = $request->no_rekening;
        $vendor->note = $request->note;
        $vendor->save();

    }

    public function import(Request $request)
    {
        try {
            Excel::import(new VendorImport, $request->excel_file);

            $request->session()->flash('success', 'Data imported successfully.');

            activity('vendor')
                ->causedBy(auth()->user())
                ->event('vendor_imported')
                ->log('vendor_imported');
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
        $fileName = 'vendor_import.xlsx';
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
    public function destroy(Vendor $vendor, Request $request)
    {
        if ($vendor->delete()) {
            $request->session()->flash('success', 'Product data successfully deleted.');

            return redirect()->route('vendors.index');
        }
    }

    public function getVendorOptions($selectedId=null)
    {
        $options = '<option selected disabled>-- Select Vendor --</option>';

        foreach (Vendor::all() as $vendor) {
            $isSelected = ($vendor->id == $selectedId)
                            ? 'selected'
                            : '';

            $options .= "<option value='$vendor->id' $isSelected>"
                        ."$vendor->code | $vendor->vendor_name"
                        ."</option>";
        }

        return $options;
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
            Excel::import(new VendorImportUpdate, $file);

            return redirect()->back()->with('success', 'Vendor data updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('danger', 'Error updating Vendor data: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        $fromCode = $request->input('fromCode');
        $toCode = $request->input('toCode');

        return Excel::download(new VendorExport($fromCode, $toCode), 'vendors_template_update.xlsx');
    }
}
