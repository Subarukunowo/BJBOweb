<?php

namespace App\Models\Traits;

use App\Models\PersonalAccessToken;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApiTokens
{
    protected ?PersonalAccessToken $accessToken = null;

    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createToken(string $name, array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): object
    {
        $plainTextToken = bin2hex(random_bytes(32));

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return (object) [
            'accessToken' => $token,
            'plainTextToken' => $plainTextToken,
        ];
    }

    public function currentAccessToken(): ?PersonalAccessToken
    {
        return $this->accessToken;
    }

    public function withAccessToken(PersonalAccessToken $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
