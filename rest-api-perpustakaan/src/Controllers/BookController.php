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

    public function index(Request $request): void
    {
        $books = $this->bookService->list();
        Response::json([
            'success' => true,
            'message' => 'Daftar buku',
            'data' => $books
        ]);
    }

    public function store(Request $request): void
    {
        $data = $request->getBody();
        $validator = new Validator();

        $rules = [
            'judul' => 'required',
            'penulis' => 'required',
            'tahun' => 'required|numeric',
            'stok' => 'required|numeric'
        ];

        if (!$validator->validate($data, $rules)) {
            Response::json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->getErrors()
            ], 422);
        }

        $book = $this->bookService->create($data);
        Response::json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }
}
