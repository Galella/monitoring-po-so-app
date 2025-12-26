<?php
try {
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    echo "Role 'super_admin' is ready.\n";

    $user = \App\Models\User::where('email', 'admin@example.com')->first();
    if($user) {
        $user->assignRole($role);
        echo "Role assigned to {$user->email}.\n";
    } else {
        echo "Admin user not found.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
