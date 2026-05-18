<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $hr      = Department::create(['name' => 'HR']);
        $it      = Department::create(['name' => 'IT']);
        $finance = Department::create(['name' => 'Finance']);

        $manager    = Role::create(['name' => 'Manager']);
        $staff      = Role::create(['name' => 'Staff']);
        $supervisor = Role::create(['name' => 'Supervisor']);

        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $departments = [$hr, $it, $finance];
        $roles       = [$manager, $staff, $supervisor];

        $user1 = User::create([
            'name'          => 'User One',
            'email'         => 'user1@test.com',
            'password'      => Hash::make('password'),
            'department_id' => $hr->id,
            'role_id'       => $staff->id,
            'is_admin'      => false,
        ]);

        $user2 = User::create([
            'name'          => 'User Two',
            'email'         => 'user2@test.com',
            'password'      => Hash::make('password'),
            'department_id' => $it->id,
            'role_id'       => $manager->id,
            'is_admin'      => false,
        ]);

        $user3 = User::create([
            'name'          => 'User Three',
            'email'         => 'user3@test.com',
            'password'      => Hash::make('password'),
            'department_id' => $finance->id,
            'role_id'       => $supervisor->id,
            'is_admin'      => false,
        ]);

        $catEnterprise   = Category::create(['name' => 'Enterprise', 'description' => 'Enterprise applications']);
        $catHR           = Category::create(['name' => 'HR', 'description' => 'Human Resources applications']);
        $catFinance      = Category::create(['name' => 'Finance', 'description' => 'Finance and Accounting applications']);
        $catSupport      = Category::create(['name' => 'Support', 'description' => 'IT and Support applications']);
        $catProductivity = Category::create(['name' => 'Productivity', 'description' => 'Productivity and Collaboration']);
        $catLogistics    = Category::create(['name' => 'Logistics', 'description' => 'Supply chain and logistics']);
        $catSales        = Category::create(['name' => 'Sales', 'description' => 'Sales and CRM']);
        $catAnalytics    = Category::create(['name' => 'Analytics', 'description' => 'Data and Analytics']);
        $catIT           = Category::create(['name' => 'IT', 'description' => 'IT Infrastructure']);

        $apps = Application::insert([
            [
                'name'        => 'ERP System',
                'url'         => 'https://erp.internal',
                'icon'        => 'pi pi-server',
                'category_id' => $catEnterprise->id,
                'description' => 'Core enterprise resource planning system.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'HRIS',
                'url'         => 'https://hris.internal',
                'icon'        => 'pi pi-users',
                'category_id' => $catHR->id,
                'description' => 'Human Resource Information System.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Payroll',
                'url'         => 'https://payroll.internal',
                'icon'        => 'pi pi-wallet',
                'category_id' => $catFinance->id,
                'description' => 'Employee payroll processing and management.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Support Desk',
                'url'         => 'https://support.internal',
                'icon'        => 'pi pi-headphones',
                'category_id' => $catSupport->id,
                'description' => 'Internal IT helpdesk and ticketing system.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Project Management',
                'url'         => 'https://pm.internal',
                'icon'        => 'pi pi-calendar',
                'category_id' => $catProductivity->id,
                'description' => 'Track and manage projects across teams.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Document Management',
                'url'         => 'https://dms.internal',
                'icon'        => 'pi pi-file',
                'category_id' => $catProductivity->id,
                'description' => 'Centralized document storage and version control.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Inventory Management',
                'url'         => 'https://inventory.internal',
                'icon'        => 'pi pi-box',
                'category_id' => $catLogistics->id,
                'description' => 'Real-time inventory tracking and control.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'CRM',
                'url'         => 'https://crm.internal',
                'icon'        => 'pi pi-briefcase',
                'category_id' => $catSales->id,
                'description' => 'Customer relationship management platform.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Business Intelligence',
                'url'         => 'https://bi.internal',
                'icon'        => 'pi pi-chart-bar',
                'category_id' => $catAnalytics->id,
                'description' => 'Data analytics and reporting dashboard.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Asset Management',
                'url'         => 'https://assets.internal',
                'icon'        => 'pi pi-desktop',
                'category_id' => $catIT->id,
                'description' => 'Company asset tracking and lifecycle management.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        $hris         = Application::where('name', 'HRIS')->first();
        $erp          = Application::where('name', 'ERP System')->first();
        $supportDesk  = Application::where('name', 'Support Desk')->first();

        $hris->departments()->attach($hr->id);

        $erp->roles()->attach($manager->id);

        $supportDesk->users()->attach($user1->id);
    }
}
