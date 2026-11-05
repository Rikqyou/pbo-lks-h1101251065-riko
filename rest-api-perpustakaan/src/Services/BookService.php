<?php

namespace App\Services;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use Exception;

class BookService
{
    private BookRepositoryInterface $repository;

    public function __construct(BookRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(): array
    {
        $books = $this->repository->findAll();
        return array_map(fn(Book $b) => $b->toArray(), $books);
    }

    public function get(int $id): ?array
    {
        $book = $this->repository->findById($id);
        return $book ? $book->toArray() : null;
    }

    public function create(array $data): array
    {
        $this->validate($data);
        $book = $this->repository->create([
            'judul'   => trim($data['judul']),
            'penulis' => trim($data['penulis'] ?? ''),
            'tahun'   => (int)($data['tahun'] ?? 0),
            'stok'    => (int)($data['stok'] ?? 0),
        ]);
        return $book->toArray();
    }

    public function update(int $id, array $data): ?array
    {
        if (!$this->repository->findById($id)) {
            return null;
        }
        $this->validate($data);
        $book = $this->repository->update($id, [
            'judul'   => trim($data['judul']),
            'penulis' => trim($data['penulis'] ?? ''),
            'tahun'   => (int)($data['tahun'] ?? 0),
            'stok'    => (int)($data['stok'] ?? 0),
        ]);
        return $book ? $book->toArray() : null;
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function validate(array $data): void
    {
        if (empty($data['judul'])) {
            throw new Exception("Judul wajib diisi", 422);
        }
    }
}
