<?php

namespace App\Repositories;

use App\Contracts\BookRepositoryInterface;
use PDO;

class MySQLBookRepository implements BookRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM books");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare("INSERT INTO books (judul, penulis, tahun, stok) VALUES (:judul, :penulis, :tahun, :stok)");
        $stmt->execute([
            'judul'   => $data['judul'],
            'penulis' => $data['penulis'],
            'tahun'   => $data['tahun'],
            'stok'    => $data['stok']
        ]);

        $data['id'] = (int) $this->db->lastInsertId();
        return $data;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE books SET judul = :judul, penulis = :penulis, tahun = :tahun, stok = :stok WHERE id = :id");
        return $stmt->execute([
            'id'      => $id,
            'judul'   => $data['judul'],
            'penulis' => $data['penulis'],
            'tahun'   => $data['tahun'],
            'stok'    => $data['stok']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
