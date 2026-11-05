<?php

namespace App\Controllers;

use App\Services\CategoryService;
use App\Core\Response;

class CategoryController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): void
    {
        $categories = $this->categoryService->getAllCategories();
        Response::json([
            'success' => true,
            'message' => 'Daftar kategori',
            'data'    => $categories
        ], 200);
    }

    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($input['nama'])) {
            Response::json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => ['nama' => 'Nama kategori tidak boleh kosong']
            ], 422);
            return;
        }

        $category = $this->categoryService->createCategory($input);
        Response::json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $category
        ], 201);
    }

    public function update(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($input['nama'])) {
            Response::json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => ['nama' => 'Nama kategori tidak boleh kosong']
            ], 422);
            return;
        }

        $updated = $this->categoryService->updateCategory($id, $input);
        if ($updated) {
            Response::json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui'
            ], 200);
        } else {
            Response::json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }

    public function destroy(int $id): void
    {
        $deleted = $this->categoryService->deleteCategory($id);
        if ($deleted) {
            Response::json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ], 200);
        } else {
            Response::json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }
}