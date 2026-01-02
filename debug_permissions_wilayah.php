<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "--- Debugging User Wilayah Permissions ---\n";

// 1. Check Role permissions
$roleName = 'user_wilayah';
$permName = 'export_monitoring_po';

$role = Role::where('name', $roleName)->first();
if ($role) {
    echo "Role '$roleName' exists.\n";
    echo "  Has '$permName': " . ($role->hasPermissionTo($permName) ? 'YES' : 'NO') . "\n";
} else {
    echo "Role '$roleName' NOT FOUND.\n";
}

// 2. Check a User with this role (Assuming one exists or we just check the role)
$user = User::role($roleName)->first();

if ($user) {
    echo "Testing with User: " . $user->email . " (ID: " . $user->id . ")\n";
    echo "  User has role '$roleName': " . ($user->hasRole($roleName) ? 'YES' : 'NO') . "\n";
    echo "  \$user->can('$permName'): " . ($user->can($permName) ? 'YES' : 'NO') . "\n";
} else {
    echo "No user found with role '$roleName'.\n";
}
