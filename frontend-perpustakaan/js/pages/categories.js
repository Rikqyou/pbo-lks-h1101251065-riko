const CategoryPage = {
    async render() {
        const app = document.getElementById('app');
        app.innerHTML = `
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Kategori</h2>
                <button type="button" onclick="CategoryPage.showModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow cursor-pointer">
                    + Tambah Kategori
                </button>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="category-list" class="divide-y divide-gray-200">
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Form -->
            <div id="cat-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                    <h3 id="modal-title" class="text-xl font-bold mb-4 text-gray-800">Tambah Kategori</h3>
                    <form id="cat-form" onsubmit="CategoryPage.saveData(event)">
                        <input type="hidden" id="cat-id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                            <input type="text" id="cat-nama" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea id="cat-deskripsi" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none" rows="3"></textarea>
                        </div>
                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" onclick="CategoryPage.hideModal()" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 cursor-pointer">Batal</button>
                            <button type="submit" id="btn-save" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer font-semibold">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        await this.loadCategories();
    },

    async loadCategories() {
        try {
            const res = await ApiClient.get('/categories');
            const tbody = document.getElementById('category-list');
            
            // Deteksi otomatis berbagai kemungkinan bentuk response API
            let categories = [];
            if (Array.isArray(res)) {
                categories = res;
            } else if (res && Array.isArray(res.data)) {
                categories = res.data;
            } else if (res && res.data && Array.isArray(res.data.data)) {
                categories = res.data.data;
            }

            if (categories.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data kategori</td></tr>`;
                return;
            }

            tbody.innerHTML = categories.map(c => {
                const id = c.id || c.category_id || '';
                const nama = c.nama || c.nama_kategori || c.name || '';
                const deskripsi = c.deskripsi || c.description || '-';

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">${id}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">${nama}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">${deskripsi}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <button onclick="CategoryPage.deleteCategory(${id})" class="text-red-600 hover:text-red-900 cursor-pointer">Hapus</button>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error loadCategories:', error);
            const tbody = document.getElementById('category-list');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Gagal memuat data: ${error.message}</td></tr>`;
            }
        }
    },

    showModal() {
        document.getElementById('cat-id').value = '';
        document.getElementById('cat-nama').value = '';
        document.getElementById('cat-deskripsi').value = '';
        document.getElementById('cat-modal').classList.remove('hidden');
    },

    hideModal() {
        document.getElementById('cat-modal').classList.add('hidden');
    },

    async saveData(e) {
        if (e) e.preventDefault();
        
        const nama = document.getElementById('cat-nama').value;
        const deskripsi = document.getElementById('cat-deskripsi').value;

        if (!nama.trim()) {
            alert('Nama Kategori wajib diisi!');
            return;
        }

        const body = { nama, deskripsi };

        try {
            await ApiClient.post('/categories', body);
            alert('Kategori berhasil ditambahkan!');
            this.hideModal();
            await this.loadCategories();
        } catch (error) {
            alert('Gagal menyimpan data: ' + error.message);
        }
    },

    async deleteCategory(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus kategori ini?')) return;
        try {
            await ApiClient.delete(`/categories/${id}`);
            alert('Kategori berhasil dihapus!');
            await this.loadCategories();
        } catch (error) {
            alert('Gagal menghapus: ' + error.message);
        }
    }
};
