<?php

namespace App\Http\Controllers;

use App\DataTables\CostDataTable;
use App\Http\Requests\StoreCostRequest;
use App\Http\Requests\UpdateCostRequest;
use App\Imports\CostImport;
use App\Models\BankAccount;
use App\Models\OutcomeCheque;
use App\Models\OutcomeGroup;
use App\Models\OutcomeType;
use App\Models\Supplier;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AccountingCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CostDataTable $costDataTable)
    {
        $data['pageTitle']    = 'Cost';
        $data['categories']   = OutcomeType::all();
        $data['bankAccounts'] = BankAccount::orderBy('bank_id')->get();
        $data['suppliers']    = Supplier::with('mainCategory')->get();
        $data['vendors']      = Vendor::all();

        return $costDataTable->render('pages.cost.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCostRequest $request)
    {
        $request->validated();

        if ($file1 = $request->file('receipt_file_1')) {
            $file1Name      = Str::slug(Str::random(10) . $file1->getClientOriginalName()) . '.' . $file1->getClientOriginalExtension();
            $directoryFile1 = $file1->move('uploads/receipts', $file1Name);
        }

        if ($file2 = $request->file('receipt_file_2')) {
            $file2Name      = Str::slug(Str::random(10) . $file2->getClientOriginalName()) . '.' . $file2->getClientOriginalExtension();
            $directoryFile2 = $file2->move('uploads/receipts', $file2Name);
        }

        $poStockId = (($request->code_type === 'job') && ($request->category == 213001))
            ? $request->po_stock
            : null;

        $cost                  = new OutcomeCheque;
        $cost->bank_account_id = $request->bank_account;
        $cost->code            = $request->code;
        $cost->code_type       = $request->code_type;
        $cost->outcome_type_id = $request->category;
        $cost->po_stock_id     = $poStockId;
        $cost->amount          = $request->amount;
        $cost->recipient_type  = $request->recipient_type;
        $cost->recipient_id    = $request->recipient;
        $cost->note            = $request->note;
        $cost->date            = $request->date;
        $cost->receipt_file_1  = ($file1) ? $directoryFile1 : null;
        $cost->receipt_file_2  = ($file2) ? $directoryFile2 : null;

        if (!$cost->save()) {
            File::delete([$directoryFile1, $directoryFile2]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $cost) : JsonResponse
    {
        try {
            $cost = OutcomeCheque::with('outcomeType', 'job')->find($cost);
            $cost->amount = currencyFormat($cost->amount);

            $data['cost']             = $cost;
            $data['outcomeGroup']     = OutcomeGroup::find($cost->outcomeType->outcome_group_id);
            $data['recipientLabel']   = $cost->recipientLabel();
            $data['bankAccountLabel'] = $cost->bankAccount->bankAccountLabel();
            $data['poStockLabel']     = $cost->poStock ? $cost->poStock->poStockFormat() : null;

            $response = APIresponse(true, 'success', $data);

            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json(APIresponse(false, $th->getMessage()));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutcomeCheque $cost): View
    {
        $data['pageTitle']    = 'Edit Cost';
        $data['cost']         = $cost;
        $data['bankAccounts'] = BankAccount::orderBy('bank_id')->get();
        $data['categories']   = OutcomeType::all();
        $data['codeOptions']  = costCodeOptions($cost->code, $cost->code_type);

        return view('pages.cost.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCostRequest $request, OutcomeCheque $cost)
    {
        $request->validated();

        $oldFile1 = $cost->receipt_file_1;
        $oldFile2 = $cost->receipt_file_2;

        if ($newFile1 = $request->file('receipt_file_1')) {
            $newFile1Name  = Str::slug(Str::random(10) . $newFile1->getClientOriginalName()) . '.' . $newFile1->getClientOriginalExtension();
            $newDirectory1 = $newFile1->move('uploads/receipts', $newFile1Name);
        }

        if ($newFile2 = $request->file('receipt_file_2')) {
            $newFile2Name  = Str::slug(Str::random(10) . $newFile2->getClientOriginalName()) . '.' . $newFile2->getClientOriginalExtension();
            $newDirectory2 = $newFile2->move('uploads/receipts', $newFile2Name);
        }

        $poStockId = (($request->code_type === 'job') && ($request->category == 213001))
            ? $request->po_stock
            : null;

        $cost->bank_account_id = $request->bank_account;
        $cost->code            = $request->code;
        $cost->code_type       = $request->code_type;
        $cost->outcome_type_id = $request->category;
        $cost->po_stock_id     = $poStockId;
        $cost->amount          = $request->amount;
        $cost->recipient_type  = $request->recipient_type;
        $cost->recipient_id    = $request->recipient;
        $cost->note            = $request->note;
        $cost->date            = $request->date;
        $cost->receipt_file_1  = ($newFile1) ? $newDirectory1 : $oldFile1;
        $cost->receipt_file_2  = ($newFile2) ? $newDirectory2 : $oldFile2;
        $cost->save();

        if ($newFile1) {
            File::delete($oldFile1);
        }

        if ($newFile2) {
            File::delete($oldFile2);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, OutcomeCheque $cost)
    {
        $receiptFiles = [$cost->receipt_file_1, $cost->receipt_file_2];

        if ($cost->delete()) {
            File::delete($receiptFiles);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new CostImport, $request->excel_file);

            activity('cost')
                ->causedBy(auth()->user())
                ->event('cost_imported')
                ->log('cost_imported');
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
        $filePath = public_path('import_template/cost_import.xlsx');

        if (File::exists($filePath)) {
            return response()->download($filePath, 'cost_import.xlsx');
        } else {
            return back()->with('danger', 'File not found.');
        }
    }
}
