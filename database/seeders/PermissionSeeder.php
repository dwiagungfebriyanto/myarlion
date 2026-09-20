<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moduls = [
            # Account
            'account',
            'role',
            'permission',
            # Product
            'product',
            'main category',
            'sub category',
            'brand',
            'product type',
            ## Inventory
            'inventory in',
            'inventory out',
            'inventory lost',
            'inventory stock',
            'inventory mutation',
            # Supplier
            'supplier',
            # Vendor
            'vendor',
            # Inquiry
            'inquiry',
            'inquiry_item',
            # Customer
            'customer',
            # Job
            'job',
            # Accounting
            'accounting',
            'PO asset',
            'PO stock',
            'cost',
            'income statement',
            # Warehouse
            'warehouse',
        ];

        foreach ($moduls as $modul) {
            Permission::insert([
                [
                    'name'       => "view $modul",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "add $modul",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "edit $modul",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "delete $modul",
                    'guard_name' => 'web',
                ],
            ]);
        }

        Artisan::call('cache:clear');
    }
}
