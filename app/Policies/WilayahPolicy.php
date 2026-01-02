<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Wilayah;
use Illuminate\Auth\Access\HandlesAuthorization;

class WilayahPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_wilayah');
    }

    public function view(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('view_wilayah');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_wilayah');
    }

    public function update(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('update_wilayah');
    }

    public function delete(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('delete_wilayah');
    }

    public function restore(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('restore_wilayah');
    }

    public function forceDelete(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('force_delete_wilayah');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_wilayah');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_wilayah');
    }

    public function replicate(AuthUser $authUser, Wilayah $wilayah): bool
    {
        return $authUser->can('replicate_wilayah');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_wilayah');
    }

}