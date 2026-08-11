<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        foreach (['sales_rep', 'stock_admin', 'approval_admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    
        // quick test users
        $sales = User::firstOrCreate(['email' => 'sales@test.com'], ['name' => 'Sales Rep', 'password' => bcrypt('password')]);
        $sales->assignRole('sales_rep');
    
        $stockAdmin = User::firstOrCreate(['email' => 'stock@test.com'], ['name' => 'Stock Admin', 'password' => bcrypt('password')]);
        $stockAdmin->assignRole('stock_admin');
    
        $approvalAdmin = User::firstOrCreate(['email' => 'approve@test.com'], ['name' => 'Approval Admin', 'password' => bcrypt('password')]);
        $approvalAdmin->assignRole('approval_admin');
    }
}
