<?php

namespace App\Http\Controllers;

use App\DataTables\JobEditAmountApprovalDataTable;
use App\Http\Requests\EditAmountApproval\EditAmountRequest;
use App\Models\Job;
use App\Models\JobEditAmountApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobEditAmountApprovalController extends Controller
{
    public function index(JobEditAmountApprovalDataTable $datatable)
    {
        $data['title'] = 'Job - Request Edit Amount';

        return $datatable->render('pages.job_edit_amount_approval.index', $data);
    }

    public function store(EditAmountRequest $request, Job $job) : JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($job, $request, $validated) {
                $lockedJob = Job::query()
                    ->whereKey($job->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedJob->waitingEditAmountRequest()->exists()) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => 'There is already a waiting request for this job. Please wait for approval first.',
                    ], 422));
                }

                $lockedJob->editAmountRequests()->create([
                    'old_amount'     => $lockedJob->amount,
                    'request_amount' => $validated['request_amount'],
                    'note'           => $validated['note'],
                    'requester_id'   => $request->requester_id,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Request has been sent. Please wait for approval',
            ]);
        } catch (HttpResponseException $exception) {
            return $exception->getResponse();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    
    public function changeStatus(Request $request, JobEditAmountApproval $editRequest) : RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,waiting',
        ]);

        try {
            DB::transaction(function () use ($editRequest, $validated) {
                $approval = JobEditAmountApproval::query()
                    ->whereKey($editRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $approval->status      = $validated['status'];
                $approval->reviewer_id = auth()->id();
                $approval->save();

                if ($approval->status !== 'approved') {
                    return;
                }

                $job = Job::query()
                    ->whereKey($approval->job_id)
                    ->lockForUpdate()
                    ->first();

                if (!$job) {
                    throw new \RuntimeException('Related job for this edit amount request was not found.');
                }

                $job->amount = $approval->request_amount;
                $job->save();
            });

            return redirect()->route('job.edit_amount_approval.index')->with('success', 'Request status has been updated.');
        } catch (\Throwable $th) {
            Log::error('Failed to change job edit amount approval status.', [
                'edit_request_id' => $editRequest->id,
                'job_id' => $editRequest->job_id,
                'requested_status' => $validated['status'],
                'reviewer_id' => auth()->id(),
                'exception' => $th,
            ]);

            return redirect()
                ->route('job.edit_amount_approval.index')
                ->with('error', $th->getMessage() ?: 'Failed to update request status.');
        }
    }
}
