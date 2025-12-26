<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Truck;
use Illuminate\Auth\Access\HandlesAuthorization;

class TruckPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Truck');
    }

    public function view(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('View:Truck');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Truck');
    }

    public function update(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Update:Truck');
    }

    public function delete(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Delete:Truck');
    }

    public function restore(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Restore:Truck');
    }

    public function forceDelete(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('ForceDelete:Truck');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Truck');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Truck');
    }

    public function replicate(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Replicate:Truck');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Truck');
    }

}