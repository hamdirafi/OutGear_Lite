<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outgear Lite - Mini Rental System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-8">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Outgear Lite</h1>
                <p class="text-blue-200 text-sm">System Deployment Testing Environment (Docker + Nginx + MariaDB)</p>
            </div>
            <span class="bg-green-500 text-xs px-3 py-1 rounded-full font-semibold">Production Mode</span>
        </div>

        <!-- Alert Session Success -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert Validation Errors -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- 1. Tambah Data Barang (Master) -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold mb-4 border-b pb-2">1. Tambah Data Barang (Master)</h2>
                <form action="{{ route('items.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Nama Alat Outdoor</label>
                        <input type="text" name="name" required class="w-full border p-2 rounded mt-1" placeholder="Contoh: Tenda Dome 4P">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Tarif Sewa per Hari (Rp)</label>
                        <input type="number" name="rental_rate_per_day" required class="w-full border p-2 rounded mt-1" placeholder="50000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Stok Barang</label>
                        <input type="number" name="stock" required class="w-full border p-2 rounded mt-1" placeholder="5">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition font-semibold">Simpan Barang</button>
                </form>
            </div>

            <!-- 2. Buat Transaksi Sewa -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-bold mb-4 border-b pb-2">2. Buat Transaksi Sewa (Uji Queue Worker)</h2>
                <form action="{{ route('rentals.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Pilih Barang</label>
                        <select name="item_id" required class="w-full border p-2 rounded mt-1">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ $item->stock < 1 ? 'disabled' : '' }}>
                                    {{ $item->name }} (Stok: {{ $item->stock }}) - Rp {{ number_format($item->rental_rate_per_day) }}/hari
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nama Penyewa</label>
                        <input type="text" name="customer_name" required class="w-full border p-2 rounded mt-1" placeholder="Nama Pelanggan">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Lama Sewa (Hari)</label>
                        <input type="number" name="days" min="1" required class="w-full border p-2 rounded mt-1" placeholder="2">
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold">Proses Transaksi Sewa</button>
                </form>
            </div>
        </div>

        <!-- 3. Daftar Transaksi Aktif & Riwayat -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4 border-b pb-2">Daftar Transaksi Aktif</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-b">
                            <th class="p-3 text-sm font-semibold">ID</th>
                            <th class="p-3 text-sm font-semibold">Penyewa</th>
                            <th class="p-3 text-sm font-semibold">Barang</th>
                            <th class="p-3 text-sm font-semibold">Lama Sewa</th>
                            <th class="p-3 text-sm font-semibold">Total Biaya</th>
                            <th class="p-3 text-sm font-semibold">Status</th>
                            <th class="p-3 text-sm font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">#{{ $rental->id }}</td>
                                <td class="p-3 font-medium">{{ $rental->customer_name }}</td>
                                <td class="p-3">{{ $rental->item->name ?? '-' }}</td>
                                <td class="p-3">{{ $rental->days }} Hari</td>
                                <td class="p-3">Rp {{ number_format($rental->total_price) }}</td>
                                <td class="p-3">
                                    @if(strtolower($rental->status) === 'active')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded font-semibold">
                                            ACTIVE
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded font-semibold">
                                            RETURNED
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if(strtolower($rental->status) === 'active')
                                        <form action="{{ route('rentals.return', $rental->id) }}" method="POST" onsubmit="return confirm('Kembalikan barang ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded transition font-medium">
                                                Kembalikan Barang
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500">Belum ada transaksi sewa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>