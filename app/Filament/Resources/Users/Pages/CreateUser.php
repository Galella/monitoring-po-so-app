<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = static::getModel()::create($data);

        if (!empty($roles)) {
            $user->assignRole($roles);
        }

        return $user;
    }
}