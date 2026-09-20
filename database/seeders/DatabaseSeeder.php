<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BankSeeder::class,
            BankAccountSeeder::class,
            ChannelSeeder::class,
            CitySeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            CustomerSeeder::class,
            OutcomeGroupSeeder::class,
            OutcomeTypeSeeder::class,
            PermissionSeeder::class,
            ProvinceSeeder::class,
            RoleSeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
            WarehouseSeeder::class,
            Main_CategorySeeder::class,
            Sub_CategorySeeder::class,
            BrandSeeder::class,
            ProductTypeSeeder::class,
            SupplierSeeder::class,
            PackagingSeeder::class,
            // ProductSeeder::class,
            SpecificationSeeder::class,
            WebsiteSeeder::class,
        ]);
    }
}
