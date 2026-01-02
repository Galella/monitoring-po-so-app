<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CmData;
use Illuminate\Auth\Access\HandlesAuthorization;

class CmDataPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_cm_data');
    }

    public function view(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('view_cm_data');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_cm_data');
    }

    public function update(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('update_cm_data');
    }

    public function delete(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('delete_cm_data');
    }

    public function restore(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('restore_cm_data');
    }

    public function forceDelete(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('force_delete_cm_data');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_cm_data');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_cm_data');
    }

    public function replicate(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('replicate_cm_data');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_cm_data');
    }

    public function import(AuthUser $authUser, CmData $cmData): bool
    {
        return $authUser->can('import_cm_data');
    }

}