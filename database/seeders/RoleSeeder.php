<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $userArea = Role::firstOrCreate(['name' => 'user_area', 'guard_name' => 'web']);
        $userWilayah = Role::firstOrCreate(['name' => 'user_wilayah', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            // CM Data permissions
            'view_cm_data',
            'view_any_cm_data',
            'create_cm_data',
            'update_cm_data',
            'delete_cm_data',
            'import_cm_data',

            // COINS Data permissions
            'view_coins_data',
            'view_any_coins_data',
            'create_coins_data',
            'update_coins_data',
            'delete_coins_data',
            'import_coins_data',

            // Wilayah permissions
            'view_wilayah',
            'view_any_wilayah',

            // Area permissions
            'view_area',
            'view_any_area',

            // Data Matching
            'view_data_matching',
            
            // Monitoring PO Page
            'view_monitoring_po',

            // Export Permissions
            'export_monitoring_po',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to User Area
        $userArea->syncPermissions([
            'view_cm_data',
            'view_any_cm_data',
            'create_cm_data',
            'update_cm_data',
            'import_cm_data',
            'view_area',
            'view_any_area',
            'view_data_matching',
            // 'view_monitoring_po', // Uncomment if User Area needs access
        ]);

        // Assign permissions to User Wilayah
        $userWilayah->syncPermissions([
            'view_coins_data',
            'view_any_coins_data',
            'create_coins_data',
            'update_coins_data',
            'import_coins_data',
            'view_wilayah',
            'view_any_wilayah',
            'view_area',
            'view_any_area',
            'view_cm_data',
            'view_any_cm_data',
            'import_cm_data',
            'view_data_matching',
            'view_monitoring_po',
            'export_monitoring_po',
        ]);
    }
}
