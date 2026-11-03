<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Update user profile information, password, and avatar.
     */
    public function updateProfile(int $userId, array $data, ?UploadedFile $avatar = null): User
    {
        $user = $this->userRepository->findById($userId);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($avatar) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $avatar->store('avatars', 'public');
            $payload['profile_picture'] = $path;
        }

        $this->userRepository->update($userId, $payload);

        return $this->userRepository->findById($userId);
    }

    /**
     * Remove custom profile photo and reset to dynamic avatar.
     */
    public function removeAvatar(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        return $this->userRepository->update($userId, ['profile_picture' => null]);
    }
}
