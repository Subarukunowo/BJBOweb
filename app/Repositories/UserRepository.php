<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::latest()->get();
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function setBlockStatus(int $id, bool $status): bool
    {
        $user = $this->findById($id);
        $user->is_blocked = $status;

        return $user->save();
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);

        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        return (bool) $user->delete();
    }
}
