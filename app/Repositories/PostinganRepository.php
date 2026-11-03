<?php

namespace App\Repositories;

use App\Models\Postingan;
use Illuminate\Database\Eloquent\Collection;

class PostinganRepository
{
    protected array $defaultColumns = [
        'id', 'name', 'price', 'tipe_penawaran', 'category', 'kondisi',
        'description', 'image', 'lokasi', 'user_id', 'status', 'is_sold',
        'views_count', 'is_auto_blocked', 'created_at',
    ];

    public function getApproved(): Collection
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with([
                'user:id,name,profile_picture',
                'ulasan' => function ($q) {
                    $q->select(['id', 'postingan_id', 'user_id', 'rating', 'komentar', 'created_at'])
                        ->with(['user:id,name,profile_picture']);
                },
            ])
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->latest('id')
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with(['user:id,name,profile_picture'])
            ->where('user_id', $userId)
            ->where('is_auto_blocked', false)
            ->latest('id')
            ->get();
    }

    public function findById(int $id): Postingan
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with([
                'user:id,name,profile_picture',
                'ulasan' => function ($q) {
                    $q->select(['id', 'postingan_id', 'user_id', 'rating', 'komentar', 'created_at'])
                        ->with(['user:id,name,profile_picture']);
                },
            ])
            ->findOrFail($id);
    }

    public function search(string $keyword): Collection
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with(['user:id,name,profile_picture'])
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%');
            })
            ->latest('id')
            ->get();
    }

    public function getByCategory(string $category): Collection
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with(['user:id,name,profile_picture'])
            ->where('category', $category)
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->latest('id')
            ->get();
    }

    public function getRelated(string $category, int $excludeId, int $limit = 4): Collection
    {
        return Postingan::query()
            ->select($this->defaultColumns)
            ->with(['user:id,name,profile_picture'])
            ->where('category', $category)
            ->where('id', '!=', $excludeId)
            ->where('status', 'disetujui')
            ->where('is_auto_blocked', false)
            ->latest('id')
            ->take($limit)
            ->get();
    }

    public function create(array $data): Postingan
    {
        return Postingan::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $postingan = Postingan::findOrFail($id);

        return $postingan->update($data);
    }

    public function delete(int $id): bool
    {
        $postingan = Postingan::findOrFail($id);

        return (bool) $postingan->delete();
    }
}
