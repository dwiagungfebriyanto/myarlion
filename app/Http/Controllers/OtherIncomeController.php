<?php

namespace App\Http\Controllers;

use App\DataTables\OtherIncomeDataTable;
use App\Http\Requests\OtherIncome\StoreOtherIncomeRequest;
use App\Http\Requests\OtherIncome\UpdateOtherIncomeRequest;
use App\Models\BankAccount;
use App\Models\OtherIncome;
use App\Models\OtherIncomeCategory;
use App\Models\OutcomeType;
use App\Models\Supplier;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OtherIncomeController extends Controller
{
    private $bankAccounts, $categories, $costCategories, $suppliers, $vendors;

    public function __construct()
    {
        $this->bankAccounts   = BankAccount::all();
        $this->categories     = OtherIncomeCategory::all();
        $this->costCategories = OutcomeType::where('id', '!=', 208001)->get();
        $this->suppliers      = Supplier::with('mainCategory')->get();
        $this->vendors        = Vendor::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OtherIncomeDataTable $datatable): View|JsonResponse
    {
        $data['bankAccounts']   = $this->bankAccounts;
        $data['categories']     = $this->categories;
        $data['costCategories'] = $this->costCategories;
        $data['suppliers']      = $this->suppliers;
        $data['vendors']        = $this->vendors;

        return $datatable->render('pages.other_income.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOtherIncomeRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        # Prepare data
        $data = [
            'date'                     => $validated['date'],
            'bank_account_id'          => $validated['bank_account'],
            'other_income_category_id' => $validated['category'],
            'outcome_type_id'          => $validated['cost_category'],
            'code'                     => $validated['code'],
            'code_type'                => $validated['code_type'],
            'amount'                   => $validated['amount'],
            'description'              => $validated['description'],
        ];

        # get recipient
        $recipient_type = $validated['recipient_type'];
        $recipient_id   = $validated['recipient'];

        $recipient = ($recipient_type === 'supplier') 
            ? Supplier::find($recipient_id)
            : Vendor::find($recipient_id);

        # store other income
        $recipient->otherIncomes()->create($data);
        $otherIncome = OtherIncome::latest()->first();

        
        if (session()->has('pending_return_stock') && $request->ajax()) {
            $pendingReturn = session('pending_return_stock');
            $pendingReturn['other_income_id'] = $otherIncome->id;
            session(['pending_return_stock' => $pendingReturn]);
        }

        return redirect()->back()->with('success', 'Other Income stored.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OtherIncome  $otherIncome
     * @return \Illuminate\Contracts\View\View
     */
    function edit(OtherIncome $otherIncome) : View
    {
        $data['otherIncome']    = $otherIncome;
        $data['bankAccounts']   = $this->bankAccounts;
        $data['categories']     = $this->categories;
        $data['costCategories'] = $this->costCategories;
        $data['suppliers']      = $this->suppliers;
        $data['vendors']        = $this->vendors;

        return view('pages.other_income.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOtherIncomeRequest $request, OtherIncome $otherIncome): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $recipientBaseClass = ($validated['recipient_type'] === 'supplier') 
                ? Supplier::class 
                : Vendor::class;

            $otherIncome->update([
                'date'                     => $validated['date'],
                'bank_account_id'          => $validated['bank_account'],
                'other_income_category_id' => $validated['category'],
                'outcome_type_id'          => $validated['cost_category'],
                'code'                     => $validated['code'],
                'code_type'                => $validated['code_type'],
                'amount'                   => $validated['amount'],
                'description'              => $validated['description'],
                'recipient_type'           => $recipientBaseClass,
                'recipient_id'             => $validated['recipient'],
            ]);

            return redirect()->back()->with('success', 'Other Income updated.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update Other Income: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OtherIncome $otherIncome): RedirectResponse
    {
        $otherIncome->delete();

        return redirect()->back()->with('success', 'Other Income deleted.');
    }
}
