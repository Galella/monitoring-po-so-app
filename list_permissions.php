<?php
use Spatie\Permission\Models\Permission;

echo "--- Permission List ---\n";
echo "ID | Name | Guard\n";
echo "---|------|------\n";
foreach (Permission::all() as $p) {
    echo "{$p->id} | {$p->name} | {$p->guard_name}\n";
}
echo "-----------------------\n";
