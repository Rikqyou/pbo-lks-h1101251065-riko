<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\BookService;
use Exception;

class BookController
{
    private BookService $service;

    public function __construct(BookService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $books = $this->service->list();
        Response::json([
            'success' => true,
            'message' => 'Daftar buku',
            'data'    => $books
        ]);
    }

    public function show(Request $request, int $id): void
    {
        $book = $this->service->get($id);
        if (!$book) {
            Response::json([
                'success' => false,
                'message' => "Buku dengan ID {$id} tidak ditemukan"
            ], 404);
        }

        Response::json([
            'success' => true,
            'message' => 'Detail buku',
            'data'    => $book
        ]);
    }

    public function store(Request $request): void
    {
        try {
            $book = $this->service->create($request->getBody());
            Response::json([
                'success' => true,
                'message' => 'Buku berhasil dibuat',
                'data'    => $book
            ], 201);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): void
    {
        try {
            $book = $this->service->update($id, $request->getBody());
            if (!$book) {
                Response::json([
                    'success' => false,
                    'message' => "Buku dengan ID {$id} tidak ditemukan"
                ], 404);
            }

            Response::json([
                'success' => true,
                'message' => 'Buku berhasil diperbarui',
                'data'    => $book
            ]);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function destroy(Request $request, int $id): void
    {
        $success = $this->service->delete($id);
        if (!$success) {
            Response::json([
                'success' => false,
                'message' => "Buku dengan ID {$id} tidak ditemukan"
            ], 404);
        }

        Response::json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
