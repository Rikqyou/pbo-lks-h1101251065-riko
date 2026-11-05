const DashboardPage = {
    async render() {
        const app = document.getElementById('app');
        app.innerHTML = `
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-blue-600 text-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Buku</h3>
                    <p id="total-buku" class="text-4xl font-bold">0</p>
                </div>
                <div class="bg-amber-500 text-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Kategori</h3>
                    <p id="total-kategori" class="text-4xl font-bold">0</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">5 Buku Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                            </tr>
                        </thead>
                        <tbody id="latest-books" class="divide-y divide-gray-200">
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        await this.loadData();
    },

    async loadData() {
        try {
            const [booksRes, catRes] = await Promise.all([
                ApiClient.get('/books'),
                ApiClient.get('/categories')
            ]);

            // Ekstraksi array data buku & kategori yang fleksibel
            let books = Array.isArray(booksRes) ? booksRes : (booksRes.data || []);
            let categories = Array.isArray(catRes) ? catRes : (catRes.data || []);

            document.getElementById('total-buku').innerText = books.length;
            document.getElementById('total-kategori').innerText = categories.length;

            const tbody = document.getElementById('latest-books');
            const latest = books.slice(0, 5);

            if (latest.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada buku</td></tr>`;
                return;
            }

            tbody.innerHTML = latest.map(b => {
                const judul = b.judul || b.title || '';
                const penulis = b.penulis || b.author || '';
                const tahun = b.tahun_terbit || b.tahun || b.year || '-';
                const stok = b.stok || b.stock || 0;

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">${judul}</td>
                        <td class="px-6 py-4 text-gray-500">${penulis}</td>
                        <td class="px-6 py-4 text-gray-500">${tahun}</td>
                        <td class="px-6 py-4 text-gray-500">${stok}</td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error(error);
            const tbody = document.getElementById('latest-books');
            if (tbody) tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Gagal memuat data</td></tr>`;
        }
    }
};
