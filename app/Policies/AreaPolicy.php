<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Area;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_area');
    }

    public function view(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('view_area');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_area');
    }

    public function update(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('update_area');
    }

    public function delete(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('delete_area');
    }

    public function restore(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('restore_area');
    }

    public function forceDelete(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('force_delete_area');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_area');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_area');
    }

    public function replicate(AuthUser $authUser, Area $area): bool
    {
        return $authUser->can('replicate_area');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_area');
    }

}