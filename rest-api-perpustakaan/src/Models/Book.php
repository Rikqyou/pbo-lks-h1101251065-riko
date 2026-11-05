<?php

namespace App\Models;

class Book
{
    private ?int $id;
    private string $judul;
    private string $penulis;
    private int $tahun;
    private int $stok;

    public function __construct(?int $id, string $judul, string $penulis, int $tahun, int $stok)
    {
        $this->id = $id;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun = $tahun;
        $this->stok = $stok;
    }

    public function getId(): ?int { return $this->id; }
    public function getJudul(): string { return $this->judul; }
    public function getPenulis(): string { return $this->penulis; }
    public function getTahun(): int { return $this->tahun; }
    public function getStok(): int { return $this->stok; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'tahun' => $this->tahun,
            'stok' => $this->stok,
        ];
    }
}
