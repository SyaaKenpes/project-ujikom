<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Panel Petugas Lab</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h3 class="mb-3">Daftar Pengajuan & Transaksi Peminjaman</h3>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>

                        <tr>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Status</th>
                            <th>Alat yang Dipinjam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $item)
                            <tr>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->tgl_pinjam }}</td>
                                <td>{{ $item->tgl_kembali_plan }}</td>
                                <td>
                                    <span
                                        class="badge
                                        @if ($item->status == 'diajukan') bg-warning text-dark
                                        @elseif($item->status == 'dipinjam') bg-primary
                                        @elseif($item->status == 'selesai') bg-success
                                        @else bg-danger @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($item->detailPinjams as $detail)
                                            <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @if ($item->status == 'diajukan')
                                        <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                        </form>
                                    @elseif($item->status == 'dipinjam')
                                        <!-- Form Sederhana Proses Pengembalian -->
                                        <form action="{{ route('petugas.pengembalian.proses', $item->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="kondisi_kembali" value="Baik">
                                            <input type="hidden" name="denda" value="0">
                                            <button type="submit" class="btn btn-sm btn-warning"
                                                onclick="return confirm('Proses pengembalian alat ini?')">
                                                Terima Kembali
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>
