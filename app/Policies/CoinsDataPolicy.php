<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CoinsData;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoinsDataPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_coins_data');
    }

    public function view(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('view_coins_data');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_coins_data');
    }

    public function update(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('update_coins_data');
    }

    public function delete(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('delete_coins_data');
    }

    public function restore(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('restore_coins_data');
    }

    public function forceDelete(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('force_delete_coins_data');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_coins_data');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_coins_data');
    }

    public function replicate(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('replicate_coins_data');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_coins_data');
    }

    public function import(AuthUser $authUser, CoinsData $coinsData): bool
    {
        return $authUser->can('import_coins_data');
    }

}