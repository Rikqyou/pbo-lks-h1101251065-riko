<?php

namespace App\Controllers;

use App\Services\CategoryService;
use App\Core\Response;

class CategoryController
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $categories = $this->service->getAllCategories();
        
        // Mengembalikan data array langsung
        Response::json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'data'    => $categories
        ], 200);
    }

    public function show(int $id): void
    {
        $category = $this->service->getCategoryById($id);
        if (!$category) {
            Response::json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'data'    => $category
        ], 200);
    }

    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        
        if (empty($input['nama'])) {
            Response::json([
                'success' => false,
                'message' => 'Nama kategori wajib diisi'
            ], 400);
            return;
        }

        $category = $this->service->createCategory($input);

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $category
        ], 201);
    }

    public function update(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $updated = $this->service->updateCategory($id, $input);

        if (!$updated) {
            Response::json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori atau kategori tidak ditemukan'
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui'
        ], 200);
    }

    public function destroy(int $id): void
    {
        $deleted = $this->service->deleteCategory($id);

        if (!$deleted) {
            Response::json([
                'success' => false,
                'message' => 'Gagal menghapus kategori atau kategori tidak ditemukan'
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ], 200);
    }
}