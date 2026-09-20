<?php

namespace Tests\Feature;

use App\Http\Controllers\InquiryController;
use App\Http\Controllers\JobListController;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class InquiryCustomerLinkingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('telp')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('name');
            $table->string('customer_category')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('country_code')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('channel_id')->nullable();
            $table->unsignedBigInteger('website_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->string('platform')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('channel_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->double('amount')->default(0);
            $table->double('est_profit')->default(0);
            $table->timestamps();
        });

        Schema::create('main_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('main_category_name')->nullable();
            $table->timestamps();
        });
    }

    public function test_store_inquiry_with_existing_customer_sets_customer_id_and_name(): void
    {
        $user = $this->createUser();
        $customerId = $this->createCustomer('Existing Customer', '100');

        $this->actingAs($user);

        app(InquiryController::class)->store(new Request([
            'date' => '13/05/2026',
            'channel' => 1,
            'website' => null,
            'customer_category' => 'exis_customer',
            'customer_name' => $customerId,
            'name' => 'Should Be Ignored',
            'country' => 9,
            'city' => 'Jakarta',
            'phone' => '08123',
            'email' => 'existing@example.com',
            'note' => 'note',
            'destination' => 10,
            'platform' => 'unknown',
        ]));

        $inquiry = Inquiry::query()->firstOrFail();

        $this->assertSame($customerId, (int) $inquiry->customer_id);
        $this->assertSame('Existing Customer', $inquiry->name);
    }

    public function test_store_inquiry_with_new_customer_creates_customer_and_links_inquiry(): void
    {
        $user = $this->createUser();

        $this->actingAs($user);

        app(InquiryController::class)->store(new Request([
            'date' => '13/05/2026',
            'channel' => 1,
            'website' => null,
            'customer_category' => 'new_customer',
            'name' => 'Brand New Customer',
            'country' => 11,
            'city' => 'Bandung',
            'phone' => '08999',
            'email' => 'new@example.com',
            'note' => 'note',
            'destination' => 12,
            'platform' => 'unknown',
        ]));

        $inquiry = Inquiry::query()->firstOrFail();

        $this->assertNotNull($inquiry->customer_id);
        $this->assertSame('Brand New Customer', $inquiry->name);
        $this->assertDatabaseHas('customers', [
            'id' => $inquiry->customer_id,
            'name' => 'Brand New Customer',
            'email' => 'new@example.com',
            'telp' => '08999',
            'country_id' => 11,
        ]);
    }

    public function test_update_inquiry_keeps_name_synced_from_customer_relation(): void
    {
        $customerId = $this->createCustomer('Master Customer Name', '101');
        $inquiryId = DB::table('inquiries')->insertGetId([
            'date' => '2026-05-13',
            'name' => 'Outdated Name',
            'customer_category' => 'exis_customer',
            'customer_id' => $customerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $inquiry = Inquiry::findOrFail($inquiryId);

        app(InquiryController::class)->update(new Request([
            'date' => '13/05/2026',
            'channel' => 2,
            'website' => null,
            'name' => 'User Edited Name',
            'country' => 5,
            'city' => 'Surabaya',
            'phone' => '08000',
            'email' => 'sync@example.com',
            'note' => 'updated',
            'destination' => 15,
            'platform' => 'unknown',
            'status' => 'onprogress',
        ]), $inquiry);

        $this->assertSame('Master Customer Name', $inquiry->fresh()->name);
    }

    public function test_store_inquiry_job_uses_linked_customer_without_creating_duplicate_customer(): void
    {
        $customerId = $this->createCustomer('Linked Customer', '102');
        $inquiryId = DB::table('inquiries')->insertGetId([
            'date' => '2026-05-13',
            'name' => 'Linked Customer',
            'customer_category' => 'exis_customer',
            'customer_id' => $customerId,
            'destination_id' => 21,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $job = new Job();
        $job->code = 'JOB-001';
        $job->source = 'inquiry';
        $job->employee_id = 1;
        $job->currency_id = 1;

        app(JobListController::class)->storeInquiryJob($job, $inquiryId);

        $this->assertSame(1, DB::table('customers')->count());
        $this->assertSame($customerId, (int) Job::query()->firstOrFail()->customer_id);
        $this->assertSame($customerId, (int) Inquiry::findOrFail($inquiryId)->customer_id);
    }

    public function test_store_inquiry_job_creates_customer_for_legacy_new_customer_inquiry(): void
    {
        $inquiryId = DB::table('inquiries')->insertGetId([
            'date' => '2026-05-13',
            'name' => 'Legacy New Customer',
            'customer_category' => 'new_customer',
            'customer_id' => null,
            'country_code' => 31,
            'phone' => '08777',
            'email' => 'legacy@example.com',
            'destination_id' => 22,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $job = new Job();
        $job->code = 'JOB-002';
        $job->source = 'inquiry';
        $job->employee_id = 1;
        $job->currency_id = 1;

        app(JobListController::class)->storeInquiryJob($job, $inquiryId);

        $inquiry = Inquiry::findOrFail($inquiryId);

        $this->assertNotNull($inquiry->customer_id);
        $this->assertSame($inquiry->customer_id, Job::query()->firstOrFail()->customer_id);
        $this->assertDatabaseHas('customers', [
            'id' => $inquiry->customer_id,
            'name' => 'Legacy New Customer',
            'email' => 'legacy@example.com',
            'telp' => '08777',
            'country_id' => 31,
        ]);
    }

    private function createUser(): User
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($userId);
    }

    private function createCustomer(string $name, string $code): int
    {
        return DB::table('customers')->insertGetId([
            'name' => $name,
            'code' => $code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
