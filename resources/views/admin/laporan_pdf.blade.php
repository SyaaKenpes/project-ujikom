<!DOCTYPE html>
html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>Laporan Pengembalian Alat Lab</h2>
    <p style="text-align: center;">Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Tanggal Kembali</th>
                <th>Kondisi</th>
                <th>Denda</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatKembali as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                <td>{{ $item->tgl_kembali }}</td>
                <td>{{ $item->kondisi_kembali }}</td>
                <td>Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                <td>{{ $item->petugas->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>