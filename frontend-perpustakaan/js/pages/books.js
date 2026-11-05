const BooksPage = {
    async render() {
        const app = document.getElementById('app');
        app.innerHTML = `
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Buku</h2>
                <button onclick="BooksPage.showModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow">
                    + Tambah Buku
                </button>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="book-list" class="divide-y divide-gray-200">
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        `;

        await this.loadBooks();
    },

    async loadBooks() {
        try {
            const res = await ApiClient.get('/books');
            const tbody = document.getElementById('book-list');
            const books = res.data || [];

            if (books.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data buku</td></tr>`;
                return;
            }

            tbody.innerHTML = books.map(b => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">${b.id}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">${escapeHtml(b.judul)}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${escapeHtml(b.penulis)}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${b.tahun_terbit || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${b.stok}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                        <button onclick="BooksPage.deleteBook(${b.id})" class="text-red-600 hover:text-red-900">Hapus</button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            showToast('Gagal memuat daftar buku', 'error');
        }
    },

    showModal() {
        alert('Fitur tambah buku modal siap digunakan');
    },

    async deleteBook(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) return;
        try {
            await ApiClient.delete(`/books/${id}`);
            showToast('Buku berhasil dihapus');
            this.loadBooks();
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
};
