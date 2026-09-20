<?php

namespace App\Http\Controllers;

use App\DataTables\SupplierDataTable;
use App\Imports\SupplierImport;
use App\Imports\SupplierImportUpdate;
use App\Exports\SupplierExport;
use App\Models\Main_Category;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;



class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierDataTable $dataTable)
    {
        $data['mainCategories'] = Main_Category::all();

        return $dataTable->render('pages.supplier.index', $data);
    }

    public function fetchMainCategory(Request $request)
    {
        $newId = Supplier::where('main_category_id', $request->main_category)
            ->get()
            ->max('code');

        $newCode      = intval($newId) + 1;
        $supplierCode = str_pad($newCode, 3, '0', STR_PAD_LEFT);

        $data = ['supplier_code' => $supplierCode];

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Supplier::where('main_category_id', $request->main_category_id)->count() == 999) {
            return response()->json(['success' => false, 'message' => 'You have reached the maximum number of supplier.']);
        }

        $rules = [
            'main_category' => 'required',
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name,NULL,id,main_category_id,' . $request->main_category,
            'email'         => 'email|max:255|string|nullable|unique:suppliers,email,NULL,id,main_category_id,' . $request->main_category,
            'telp'          => 'max:15|nullable',
            'fax'           => 'numeric|nullable',
        ];

        $request->validate($rules);

        $supplier                   = new Supplier;
        $last_code                  = Supplier::where('main_category_id', $request->main_category)->max('code');
        $new_code                   = intval($last_code) + 1;
        $supplier->main_category_id = $request->main_category;
        $supplier->code             = str_pad($new_code, 3, '0', STR_PAD_LEFT);
        $supplier->supplier_name    = $request->supplier_name;
        $supplier->address          = $request->address;
        $supplier->email            = $request->email;
        $supplier->telp             = $request->telp;
        $supplier->fax              = $request->fax;
        $supplier->contact          = $request->contact;
        $supplier->pkp              = $request->pkp;
        $supplier->no_rekening      = $request->no_rekening;
        $supplier->note             = $request->note;
        $supplier->save();
    }


    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $data = [
            'supplier' => $supplier,
            'main_category' => Main_Category::where('id', $supplier->main_category_id)->first(),
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $data = [
            'supplier' => $supplier,
            'main_category' => Main_Category::findOrfail($supplier->main_category_id),
        ];

        return view('pages.supplier.modals.edit-modal', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // $rules = [
        //     'supplier_name' => 'required|unique:suppliers,supplier_name|string|max:255',
        //     'email' => 'email|max:255|string|unique:suppliers,email|nullable',
        //     'telp' => 'digits_between:10,13|numeric|nullable',
        //     'fax' => 'numeric|nullable',
        // ];

        $rules = [
            // 'main_category' => 'required',
            'supplier_name' =>
            'required|max:255|unique:suppliers,supplier_name,' . $supplier->id . ',id,main_category_id,' . $supplier->main_category_id,
            // 'supplier_name' => 'required|max:255',
            'email' =>
            'email|max:255|nullable|unique:suppliers,email,' . $supplier->id . ',id,main_category_id,' . $supplier->main_category_id,
            // 'email' => 'email|max:255|nullable',
            'telp' => 'max:15|nullable',
            'fax' => 'numeric|nullable',
        ];

        $request->validate($rules);

        // $supplier->main_category_id = $request->main_category;
        $supplier->supplier_name = $request->supplier_name;
        $supplier->address = $request->address;
        $supplier->email = $request->email;
        $supplier->telp = $request->telp;
        $supplier->fax = $request->fax;
        $supplier->contact = $request->contact;
        $supplier->pkp = $request->pkp;
        $supplier->no_rekening = $request->no_rekening;
        $supplier->note = $request->note;
        $supplier->save();
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new SupplierImport, $request->excel_file);

            activity('supplier')
                ->causedBy(auth()->user())
                ->event('supplier_imported')
                ->log('supplier_imported');
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
        $fileName = 'supplier_import.xlsx';
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
    public function destroy(Supplier $supplier, Request $request)
    {
        if ($supplier->delete()) {
            $request->session()->flash('success', 'Product data successfully deleted.');

            return redirect()->route('supplier.index');
        }
    }

    public function getSupplierOptions($selectedId = null, $filterByMainCategory = null)
    {
        $options = '<option selected disabled>-- Select Supplier --</option>';

        $suppliers = (is_null($filterByMainCategory))
            ? Supplier::all()
            : Supplier::where('main_category_id', '=', $filterByMainCategory)->get();

        foreach ($suppliers as $supplier) {
            $isSelected = ($supplier->id == $selectedId)
                ? 'selected'
                : '';

            $options .= "<option value='$supplier->id' $isSelected "
                . "data-code='$supplier->code'>"
                . $supplier->mainCategory->main_category_name . " | $supplier->code | $supplier->supplier_name"
                . "</option>";
        }

        return $options;
    }

    public function getApiOptions(Request $request): JsonResponse
    {
        $selectedSupplierId = $request->selected;
        $mainCategoryId     = $request->main_category_id;

        $suppliers = Supplier::query()
            ->when(!is_null($mainCategoryId), fn($query) => $query->where('main_category_id', $mainCategoryId))
            ->orderBy('code')
            ->get();

        $options = '<option selected disabled>-- Select supplier --</option>';

        foreach ($suppliers as $supplier) {
            $isSelected = ((string) $supplier->id === (string) $selectedSupplierId)
                ? 'selected'
                : '';

            $supplierCode = e($supplier->code);
            $supplierName = e($supplier->supplier_name);

            $options .= "<option value='$supplier->id' $isSelected>$supplierCode | $supplierName</option>";
        }

        return response()->json(['options' => $options]);
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
            Excel::import(new SupplierImportUpdate, $file);

            return redirect()->back()->with('success', 'Supplier data updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('danger', 'Error updating supplier data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $fromCode = $request->input('fromCode');
        $toCode = $request->input('toCode');

        return Excel::download(new SupplierExport($fromCode, $toCode), 'suppliers_template_update.xlsx');
    }


}
