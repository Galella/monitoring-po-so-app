<?php
use Spatie\Permission\Models\Permission;

echo "--- Cleaning up Obsolete Permissions ---\n";

$deleted = Permission::where('name', 'LIKE', '%:%')->delete();

echo "Deleted {$deleted} obsolete permissions (Title:Case format).\n";

echo "\nRemaining Permissions:\n";
foreach (Permission::all() as $p) {
    echo "- {$p->name}\n";
}
