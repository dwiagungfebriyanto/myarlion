<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Job;
use App\Models\JobEditAmountApproval;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JobEditAmountApprovalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_approved_request_updates_related_job_amount(): void
    {
        $reviewer = $this->createUser();
        $job = $this->createJob();
        $approval = $this->createApproval($job->id, 250000);

        $response = $this
            ->actingAs($reviewer)
            ->put(route('edit_amount_approval.change_status', $approval), [
                'status' => 'approved',
            ]);

        $response
            ->assertRedirect(route('job.edit_amount_approval.index'))
            ->assertSessionHas('success', 'Request status has been updated.');

        $approval->refresh();
        $job->refresh();

        $this->assertSame('approved', $approval->status);
        $this->assertSame($reviewer->id, $approval->reviewer_id);
        $this->assertSame(250000.0, (float) $job->amount);
    }

    public function test_rejected_request_keeps_related_job_amount_unchanged(): void
    {
        $reviewer = $this->createUser();
        $job = $this->createJob(amount: 100000);
        $approval = $this->createApproval($job->id, 250000);

        $response = $this
            ->actingAs($reviewer)
            ->put(route('edit_amount_approval.change_status', $approval), [
                'status' => 'rejected',
            ]);

        $response
            ->assertRedirect(route('job.edit_amount_approval.index'))
            ->assertSessionHas('success', 'Request status has been updated.');

        $approval->refresh();
        $job->refresh();

        $this->assertSame('rejected', $approval->status);
        $this->assertSame($reviewer->id, $approval->reviewer_id);
        $this->assertSame(100000.0, (float) $job->amount);
    }

    public function test_approval_rolls_back_when_related_job_is_missing(): void
    {
        $reviewer = $this->createUser();
        $approval = $this->createApproval(999999999, 250000);

        $response = $this
            ->actingAs($reviewer)
            ->put(route('edit_amount_approval.change_status', $approval), [
                'status' => 'approved',
            ]);

        $response
            ->assertRedirect(route('job.edit_amount_approval.index'))
            ->assertSessionHas('error', 'Related job for this edit amount request was not found.');

        $approval->refresh();

        $this->assertSame('waiting', $approval->status);
        $this->assertNull($approval->reviewer_id);
    }

    public function test_invalid_status_is_rejected_by_validation(): void
    {
        $reviewer = $this->createUser();
        $job = $this->createJob();
        $approval = $this->createApproval($job->id, 250000);

        $response = $this
            ->from(route('job.edit_amount_approval.index'))
            ->actingAs($reviewer)
            ->put(route('edit_amount_approval.change_status', $approval), [
                'status' => 'done',
            ]);

        $response
            ->assertRedirect(route('job.edit_amount_approval.index'))
            ->assertSessionHasErrors('status');

        $approval->refresh();
        $job->refresh();

        $this->assertSame('waiting', $approval->status);
        $this->assertNull($approval->reviewer_id);
        $this->assertSame(100000.0, (float) $job->amount);
    }

    protected function createUser(): User
    {
        return User::factory()->create([
            'name' => 'Reviewer User',
            'position' => 'Supervisor',
            'role_id' => '1',
            'username' => fake()->unique()->userName(),
        ]);
    }

    protected function createJob(float $amount = 100000): Job
    {
        $customer = Customer::factory()->create([
            'id' => fake()->unique()->numberBetween(1000, 9999),
            'code' => 'CUST-' . fake()->unique()->numberBetween(1000, 9999),
        ]);

        $country = Country::create([
            'id' => fake()->unique()->numberBetween(1000, 9999),
            'country_code' => strtoupper(fake()->unique()->lexify('??')),
            'country_name' => fake()->country(),
        ]);

        $currency = Currency::create([
            'id' => fake()->unique()->numberBetween(1000, 9999),
            'currency_code' => strtoupper(fake()->unique()->lexify('???')),
            'currency_name' => fake()->currencyCode(),
        ]);

        $channel = Channel::create([
            'channel_name' => 'Direct ' . fake()->unique()->word(),
        ]);

        $marketing = User::factory()->create([
            'name' => 'Marketing User',
            'position' => 'Marketing',
            'role_id' => '3',
            'username' => fake()->unique()->userName(),
        ]);

        $job = new Job();
        $job->id = fake()->unique()->numberBetween(100000, 999999);
        $job->code = 'JOB-' . fake()->unique()->numberBetween(1000, 9999);
        $job->source = 'reguler';
        $job->customer_id = $customer->id;
        $job->currency_id = $currency->id;
        $job->country_id = $country->id;
        $job->employee_id = $marketing->id;
        $job->channel_id = $channel->id;
        $job->amount = $amount;
        $job->est_profit = 10000;
        $job->status = 'open';
        $job->status_payment = 'open';
        $job->save();

        return $job;
    }

    protected function createApproval(int $jobId, float $requestAmount): JobEditAmountApproval
    {
        return JobEditAmountApproval::create([
            'job_id' => $jobId,
            'old_amount' => 100000,
            'request_amount' => $requestAmount,
            'note' => 'Need approval to update amount',
            'status' => 'waiting',
        ]);
    }
}
