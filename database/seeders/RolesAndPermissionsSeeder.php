<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view_clients', 'create_clients', 'update_clients', 'delete_clients',
            'view_events', 'create_events', 'update_events', 'delete_events',
            'view_tickets', 'create_tickets', 'update_tickets', 'delete_tickets',
            'view_orders', 'update_orders',
            'scan_tickets', 'validate_wristbands'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions

        // Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        // Super Admin gets all permissions implicitly via Gate::before or we can assign all

        // Client Admin
        $clientAdmin = Role::firstOrCreate(['name' => 'client_admin', 'guard_name' => 'web']);
        $clientAdmin->givePermissionTo([
            'view_events', 'create_events', 'update_events', 
            'view_tickets', 'update_tickets',
            'view_orders'
        ]);

        // Wristband Exchange Officer
        $exchangeOfficer = Role::firstOrCreate(['name' => 'wristband_exchange_officer', 'guard_name' => 'web']);
        $exchangeOfficer->givePermissionTo(['scan_tickets', 'view_tickets']);

        // Wristband Validator
        $validator = Role::firstOrCreate(['name' => 'wristband_validator', 'guard_name' => 'web']);
        $validator->givePermissionTo(['validate_wristbands']);
        
        // Panel User (optional, for filament access if separate)
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);
    }
}
