<?php

use App\Models\User;
use App\Models\CmData;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "--- Debugging User Area Permissions ---\n";

// 1. Check Role permissions
$roleName = 'user_area';
$permName = 'import_cm_data';

$role = Role::where('name', $roleName)->first();
if ($role) {
    echo "Role '$roleName' exists.\n";
    echo "  Has '$permName': " . ($role->hasPermissionTo($permName) ? 'YES' : 'NO') . "\n";
} else {
    echo "Role '$roleName' NOT FOUND.\n";
}

// 2. Check a User with this role
$user = User::role($roleName)->first();

if (!$user) {
    echo "No user found with role '$roleName'. creating temp user...\n";
    // Create one if needed, but usually one exists
} else {
    echo "Testing with User: " . $user->email . " (ID: " . $user->id . ")\n";
    echo "  User has role '$roleName': " . ($user->hasRole($roleName) ? 'YES' : 'NO') . "\n";
    echo "  \$user->can('$permName'): " . ($user->can($permName) ? 'YES' : 'NO') . "\n";
}
