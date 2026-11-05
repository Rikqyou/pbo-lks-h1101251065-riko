<?php

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    private CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCategories(): array
    {
        return $this->repository->findAll();
    }

    public function getCategoryById(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function createCategory(array $data): array
    {
        return $this->repository->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->repository->delete($id);
    }
}