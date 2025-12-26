<?php
$u = \App\Models\User::where('email', 'admin@example.com')->first();
echo "User: " . ($u ? $u->email : "Not Found") . PHP_EOL;
if ($u) {
    echo "Roles: " . $u->getRoleNames() . PHP_EOL;
    foreach ($u->roles as $role) {
        echo "Role: " . $role->name . PHP_EOL;
        echo "Permissions: " . $role->permissions->pluck('name') . PHP_EOL;
    }
} else {
    echo "Admin user not found." . PHP_EOL;
}
echo "Total Roles: " . \Spatie\Permission\Models\Role::count() . PHP_EOL;
echo "Total Permissions: " . \Spatie\Permission\Models\Permission::count() . PHP_EOL;
