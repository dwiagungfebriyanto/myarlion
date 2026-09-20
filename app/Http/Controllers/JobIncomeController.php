<?php

namespace App\Http\Controllers;

use App\DataTables\JobIncomesDataTable;
use App\Http\Requests\StoreJobIncomeRequest;
use App\Http\Requests\UpdateJobIncomeRequest;
use App\Models\BankAccount;
use App\Models\Job;
use App\Models\JobIncome;
use Illuminate\Http\Request;

class JobIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobId = request('job');
        $job   = Job::findOrFail($jobId);

        $data = [
            'job'          => $job,
            'percent_out'  => percentOutstanding($job->amount, $job->currentOutstanding()),
            'bankAccounts' => BankAccount::orderBy('bank_id')->get(),
        ];

        return app(JobIncomesDataTable::class, ['job_id' => $jobId])
            ->render('pages.job.edit.tabs.income', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobIncomeRequest $request, Job $job)
    {
        $convertionRate = $request->convertion_rate ?? 1;
        $outstanding    = $job->amount - $job->jobIncomes->sum('payment');

        $data = [
            'bank_account_id' => $request->bank_account,
            'date'            => $request->datetime,
            'currency_id'     => $job->currency_id,
            'payment'         => $request->nominal,
            'nominal'         => $request->nominal * $convertionRate,
            'outstanding'     => $outstanding - $request->nominal,
            'to_idr'          => $convertionRate,
        ];

        if ($job->jobIncomes()->create($data)) {
            $request->session()->flash('success', 'New Job Income data successfully added.');
        }

        return redirect(route('job_income.index', ['job' => $job->id]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobIncomeRequest $request, JobIncome $jobIncome)
    {
        $nominal        = $request->nominal;
        $convertionRate = $request->convertion_rate ?? 1;

        $jobIncome->bank_account_id = $request->bank_account;
        $jobIncome->date            = $request->datetime;
        $jobIncome->payment         = $nominal;
        $jobIncome->nominal         = $nominal * $convertionRate;
        $jobIncome->to_idr          = $convertionRate;

        if ($jobIncome->save()) {
            $this->recountOutstandings($jobIncome->job);

            $request->session()->flash('success', 'Job Income data successfully updated.');
        }

        return redirect(route('job_income.index', $jobIncome->job));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, JobIncome $jobIncome)
    {
        if ($jobIncome->delete()) {
            $this->recountOutstandings($jobIncome->job);

            $request->session()->flash('success', 'Job Income data successfully deleted.');
        }

        return redirect(route('job_income.index', $jobIncome->job));
    }

    private function recountOutstandings(Job $job)
    {
        $outstanding = $job->amount;

        foreach ($job->jobIncomes as $jobIncome) {
            $recountOutstanding = $outstanding - $jobIncome->payment;

            $jobIncome->outstanding = $recountOutstanding;
            $jobIncome->save();

            // setelah disimpan, maka update nilai outstanding
            // dengan hasil perhitungan terakhir
            $outstanding = $recountOutstanding;
        }
    }

    public function changeStatusPayment(Job $job)
    {
        $oldStatus = $job->status_payment;

        $status = ($job->statusPaymentIsOpen()) ? 'closed' : 'open';

        $job->status_payment = $status;
        $job->save();

        activity('job_income')
            ->causedBy(auth()->user())
            ->performedOn($job)
            ->withProperties([
                'old' => $oldStatus,
                'status_payment' => $status,
            ])
            ->event('payment_status_changed')
            ->log('payment_status_changed');

        return redirect(route('job_income.index', $job));
    }
}
