<?php

use App\Models\User;
use App\Models\CoinsData;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "--- Debugging Permissions ---\n";

// 1. Check if Permission exists
$permName = 'view_any_coins_data';
$perm = Permission::where('name', $permName)->first();
echo "Permission '$permName' exists: " . ($perm ? 'YES' : 'NO') . "\n";

// 2. Check Role permissions
$roleName = 'user_wilayah';
$role = Role::where('name', $roleName)->first();
if ($role) {
    echo "Role '$roleName' exists. Permissions:\n";
    $rolePerms = $role->permissions->pluck('name')->toArray();
    echo "  Has '$permName': " . (in_array($permName, $rolePerms) ? 'YES' : 'NO') . "\n";
} else {
    echo "Role '$roleName' NOT FOUND.\n";
}

// 3. Check a User with this role
$user = User::role($roleName)->first();

if (!$user) {
    echo "No user found with role '$roleName'. creating temp user...\n";
    $user = User::factory()->create();
    $user->assignRole($roleName);
}

echo "Testing with User: " . $user->email . " (ID: " . $user->id . ")\n";
echo "  User has role '$roleName': " . ($user->hasRole($roleName) ? 'YES' : 'NO') . "\n";
echo "  \$user->can('$permName'): " . ($user->can($permName) ? 'YES' : 'NO') . "\n";

// 4. Check Policy Gate
echo "  Gate::allows('viewAny', CoinsData::class): " . (Gate::forUser($user)->allows('viewAny', CoinsData::class) ? 'YES' : 'NO') . "\n";

// Also check via Policy explicitly if possible (harder without instance sometimes, but class check works for viewAny)
$policy = Gate::getPolicyFor(CoinsData::class);
echo "  Policy for CoinsData: " . ($policy ? get_class($policy) : 'NONE') . "\n";
