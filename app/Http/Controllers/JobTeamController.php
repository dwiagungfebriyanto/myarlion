<?php

namespace App\Http\Controllers;

use App\DataTables\JobTeamDataTable;
use App\Http\Requests\StoreJobTeamRequest;
use App\Http\Requests\UpdateJobTeamRequest;
use App\Models\Job;
use App\Models\JobCommission;
use App\Models\JobTeam;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JobTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Job $job)
    {
        $data['job']             = $job;
        $data['marketingOption'] = $this->marketingOption($job);

        $dataTable = app(JobTeamDataTable::class, ['_jobID' => $job->id]);

        return $dataTable->render('pages.job.edit.tabs.team', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobTeamRequest $request, Job $job)
    {
        $request->validated();

        $data = [
            'user_id'        => $request->employee,
            'job_percentage' => $request->percentage,
        ];

        if ($job->teams()->create($data)) {
            session()->flash('success', 'Job team successfully added.');
        } else {
            session()->flash('danger', 'Something went wrong. Please try again.');
        }

        return redirect(route('job.team.index', $job));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobTeamRequest $request, Job $job, JobTeam $team)
    {
        $request->validated();

        $team->job_percentage = $request->percentage;
        $team->save();

        session()->flash('success', 'Job team successfully updated.');

        return redirect(route('job.team.index', $job));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job, JobTeam $team)
    {
        JobCommission::where('job_id', $job->id)
                        ->where('user_id', $team->user_id)
                        ->delete();

        $team->delete();

        return redirect(route('job.team.index', $job))->with('success', 'Job team successfully deleted.');
    }

    public function marketingOption(Job $job, $selected=null) : string
    {
        $exceptIdArray = [];

        foreach ($job->teams as $team) {
            if ($selected === null) {
                $exceptIdArray[] = $team->user_id;
            } else if ($selected !== null && $selected != $team->user_id) {
                $exceptIdArray[] = $team->user_id;
            }
        }

        $marketings = User::marketing()->whereNotIn('id', $exceptIdArray);

        return selectGenerate('Employee', $marketings, 'id', 'name', $selected);
    }
}
