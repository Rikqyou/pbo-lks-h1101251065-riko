<?php

namespace App\Repositories;

use App\Contracts\CategoryRepositoryInterface;
use PDO;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare("INSERT INTO categories (nama, deskripsi) VALUES (:nama, :deskripsi)");
        $stmt->execute([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null
        ]);

        $data['id'] = (int) $this->db->lastInsertId();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET nama = :nama, deskripsi = :deskripsi WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}