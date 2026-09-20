<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use App\Services\Dashboard\DashboardAggregator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardNetCostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('activitylog.enabled', false);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createTables();
    }

    public function test_profit_summary_uses_net_cost_by_transaction_month(): void
    {
        $year = (int) date('Y');

        $this->seedJobs($year);
        $this->seedMonthlyCosts($year);

        $profitRows = collect(app(DashboardAggregator::class)->profitSummary($year))
            ->keyBy('month');

        $this->assertEquals(150.0, $profitRows[1]['cost']);
        $this->assertEquals(500.0, $profitRows[1]['est_profit']);
        $this->assertEquals(350.0, $profitRows[1]['est_net_profit']);
        $this->assertEquals(300.0, $profitRows[1]['actual_profit']);
        $this->assertEquals(150.0, $profitRows[1]['net_actual_profit']);

        $this->assertEquals(30.0, $profitRows[2]['cost']);
        $this->assertEquals(400.0, $profitRows[2]['est_profit']);
        $this->assertEquals(370.0, $profitRows[2]['est_net_profit']);
        $this->assertEquals(200.0, $profitRows[2]['actual_profit']);
        $this->assertEquals(170.0, $profitRows[2]['net_actual_profit']);
    }

    public function test_cost_category_uses_other_income_per_type_and_clamps_negative_groups_to_zero(): void
    {
        $month = date('Y-m');

        $this->seedCostCategories();
        $this->seedCategoryCosts($month);

        $aggregated = app(DashboardAggregator::class)->costCategory($month);

        $this->assertEquals([
            ['group' => 'Group A', 'amount' => 30.0],
            ['group' => 'Group B', 'amount' => 0.0],
        ], $aggregated);

        $response = $this->callDashboardController('getCostCategory', ['month' => $month]);

        $this->assertEquals([
            ['Group A', 30.0],
            ['Group B', 0.0],
        ], $response->getData(true));
    }

    public function test_dashboard_controller_and_aggregator_use_the_same_net_cost_values(): void
    {
        $year = (int) date('Y');
        $month = date('Y-m');

        $this->seedJobs($year);
        $this->seedMonthlyCosts($year);
        $this->seedCostCategories();
        $this->seedCategoryCosts($month);

        $view = $this->callDashboardController('index');
        $profitCharts = $view->getData()['profit_charts'];
        $profitRows = collect(app(DashboardAggregator::class)->profitSummary($year))
            ->keyBy('month');

        $this->assertEquals($profitRows[1]['cost'], $profitCharts[1]['cost']);
        $this->assertEquals($profitRows[1]['est_net_profit'], $profitCharts[1]['est_net_profit']);
        $this->assertEquals($profitRows[1]['net_actual_profit'], $profitCharts[1]['net_actual_profit']);
        $this->assertEquals($profitRows[2]['cost'], $profitCharts[2]['cost']);
        $this->assertEquals($profitRows[2]['est_net_profit'], $profitCharts[2]['est_net_profit']);
        $this->assertEquals($profitRows[2]['net_actual_profit'], $profitCharts[2]['net_actual_profit']);

        $costCategoryResponse = $this->callDashboardController('getCostCategory', ['month' => $month]);
        $costCategoryFromController = collect($costCategoryResponse->getData(true))
            ->map(fn (array $row) => ['group' => $row[0], 'amount' => $row[1]])
            ->values()
            ->all();

        $this->assertEquals(
            app(DashboardAggregator::class)->costCategory($month),
            $costCategoryFromController
        );
    }

    private function callDashboardController(string $method, array $query = [])
    {
        $request = Request::create('/', 'GET', $query);

        $this->app->instance('request', $request);
        $this->app->instance(Request::class, $request);

        return $this->app->call([app(DashboardController::class), $method]);
    }

    private function createTables(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('source');
            $table->unsignedBigInteger('customer_id')->default(0);
            $table->unsignedBigInteger('currency_id')->default(0);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('employee_id')->default(0);
            $table->unsignedBigInteger('channel_id')->nullable();
            $table->double('amount')->default(0);
            $table->double('kurs_amount')->default(0);
            $table->double('sisa')->default(0);
            $table->double('refund')->default(0);
            $table->double('est_profit')->default(0);
            $table->double('total_expenses')->nullable();
            $table->double('gross_profit')->default(0);
            $table->double('net_profit')->default(0);
            $table->date('period_job')->nullable();
            $table->date('date')->nullable();
            $table->string('status_payment')->default('open');
            $table->string('status')->default('open');
            $table->date('closing_date')->nullable();
            $table->boolean('status_konversi')->default(true);
            $table->double('sales_commission')->default(0);
            $table->timestamps();
        });

        Schema::create('outcome_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('outcome_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outcome_group_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('outcome_cheques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('code');
            $table->string('code_type')->default('manual');
            $table->unsignedBigInteger('outcome_type_id');
            $table->unsignedBigInteger('po_stock_id')->nullable();
            $table->double('amount')->default(0);
            $table->string('recipient_type')->nullable();
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->text('note')->nullable();
            $table->date('date');
            $table->string('receipt')->nullable();
            $table->timestamps();
        });

        Schema::create('other_incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('other_income_category_id')->default(1);
            $table->string('code')->nullable();
            $table->string('code_type')->nullable();
            $table->unsignedBigInteger('outcome_type_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->default(1);
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description');
            $table->string('recipient_type')->nullable();
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('role_id');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('signature')->nullable();
            $table->rememberToken();
            $table->boolean('is_former_employee')->default(false);
            $table->timestamps();
        });

        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('web_domain');
            $table->timestamps();
        });
    }

    private function seedJobs(int $year): void
    {
        DB::table('jobs')->insert([
            [
                'id' => 1,
                'code' => 'JOB-001',
                'source' => 'reguler',
                'customer_id' => 1,
                'currency_id' => 1,
                'employee_id' => 1,
                'est_profit' => 500,
                'gross_profit' => 0,
                'net_profit' => 300,
                'period_job' => sprintf('%d-01-15', $year),
                'date' => sprintf('%d-01-10', $year),
                'status_payment' => 'closed',
                'status' => 'closed',
                'created_at' => sprintf('%d-01-05 10:00:00', $year),
                'updated_at' => sprintf('%d-01-05 10:00:00', $year),
            ],
            [
                'id' => 2,
                'code' => 'JOB-002',
                'source' => 'reguler',
                'customer_id' => 1,
                'currency_id' => 1,
                'employee_id' => 1,
                'est_profit' => 400,
                'gross_profit' => 0,
                'net_profit' => 200,
                'period_job' => sprintf('%d-02-20', $year),
                'date' => sprintf('%d-02-18', $year),
                'status_payment' => 'closed',
                'status' => 'closed',
                'created_at' => sprintf('%d-02-08 10:00:00', $year),
                'updated_at' => sprintf('%d-02-08 10:00:00', $year),
            ],
        ]);
    }

    private function seedMonthlyCosts(int $year): void
    {
        DB::table('outcome_cheques')->insert([
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'amount' => 200,
                'date' => sprintf('%d-01-11', $year),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'amount' => 50,
                'date' => sprintf('%d-02-11', $year),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 2,
                'amount' => 60,
                'date' => sprintf('%d-02-12', $year),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('other_incomes')->insert([
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'bank_account_id' => 1,
                'date' => sprintf('%d-01-20', $year),
                'amount' => 50,
                'description' => 'Jan reduction',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'bank_account_id' => 1,
                'date' => sprintf('%d-02-21', $year),
                'amount' => 80,
                'description' => 'Feb reduction',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'other_income_category_id' => 1,
                'code' => 'OTHER',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'bank_account_id' => 1,
                'date' => sprintf('%d-01-22', $year),
                'amount' => 999,
                'description' => 'Ignored because non CF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedCostCategories(): void
    {
        DB::table('outcome_groups')->insert([
            ['id' => 1, 'name' => 'Group A', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Group B', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('outcome_types')->insert([
            ['id' => 1, 'outcome_group_id' => 1, 'name' => 'Type A1', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'outcome_group_id' => 1, 'name' => 'Type A2', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'outcome_group_id' => 2, 'name' => 'Type B1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedCategoryCosts(string $month): void
    {
        DB::table('outcome_cheques')->insert([
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'amount' => 100,
                'date' => "$month-05",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 2,
                'amount' => 60,
                'date' => "$month-06",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 3,
                'amount' => 40,
                'date' => "$month-07",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('other_incomes')->insert([
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 1,
                'bank_account_id' => 1,
                'date' => "$month-10",
                'amount' => 30,
                'description' => 'Reduce type A1 only',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 2,
                'bank_account_id' => 1,
                'date' => "$month-11",
                'amount' => 100,
                'description' => 'Reduce type A2 only',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => 3,
                'bank_account_id' => 1,
                'date' => "$month-12",
                'amount' => 60,
                'description' => 'Over reduce group B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'other_income_category_id' => 1,
                'code' => 'CF',
                'code_type' => 'manual',
                'outcome_type_id' => null,
                'bank_account_id' => 1,
                'date' => "$month-13",
                'amount' => 500,
                'description' => 'Ignored because no cost category',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
