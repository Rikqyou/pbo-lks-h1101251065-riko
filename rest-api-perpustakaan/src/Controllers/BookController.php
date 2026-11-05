<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Services\BookService;

class BookController
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(?Request $request = null): void
    {
        $books = $this->bookService->list();
        Response::success($books, 'Daftar buku berhasil diambil');
    }

   public function show(Request $request, int $id): void
   {
    $book = $this->bookService->getById($id);
    if (!$book) {
        Response::error("Buku dengan ID {$id} tidak ditemukan", 404);
    }
    Response::success($book, 'Detail buku berhasil diambil');
   }

    public function store(Request $request): void
    {
        $data = $request->getBody();
        $validator = new Validator();

        $rules = [
            'judul'   => 'required',
            'penulis' => 'required',
            'tahun'   => 'required|numeric',
            'stok'    => 'required|numeric'
        ];

        if (!$validator->validate($data, $rules)) {
            Response::error('Validasi gagal', 422, $validator->getErrors());
        }

        $book = $this->bookService->create($data);
        Response::success($book, 'Buku berhasil ditambahkan', 201);
    }

    public function update(Request $request, int $id): void
    {
        $existing = $this->bookService->getById($id);
        if (!$existing) {
            Response::error('Buku tidak ditemukan', 404);
        }

        $data = $request->getBody();
        $validator = new Validator();
        $rules = [
            'judul'   => 'required',
            'penulis' => 'required',
            'tahun'   => 'required|numeric',
            'stok'    => 'required|numeric'
        ];

        if (!$validator->validate($data, $rules)) {
            Response::error('Validasi gagal', 422, $validator->getErrors());
        }

        $this->bookService->update($id, $data);
        Response::success(null, 'Buku berhasil diperbarui');
    }

    public function destroy(Request $request, int $id): void
    {
        $existing = $this->bookService->getById($id);
        if (!$existing) {
            Response::error('Buku tidak ditemukan', 404);
        }

        $this->bookService->delete($id);
        Response::success(null, 'Buku berhasil dihapus');
    }
}