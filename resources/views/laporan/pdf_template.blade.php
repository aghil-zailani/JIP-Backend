<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inspeksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #333; text-transform: uppercase; }
        .table-info { width: 100%; border-collapse: collapse; }
        .table-info td { padding: 8px; border: 1px solid #eee; }
        .label { font-weight: bold; color: #555; width: 30%; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">{{ $mobil->nama_mobil }} {{ $mobil->tipe_mobil }}</div>
        <div>{{ $mobil->tahun_mobil }} • {{ $mobil->cc }} CC • {{ $mobil->transmisi }}</div>
        <p>Inspektor: <strong>Agung</strong> | Tanggal: {{ date('d M Y') }}</p>
    </div>

    <h3>Informasi Kendaraan</h3>
    <table class="table-info">
        <tr>
            <td class="label">Nomor Polisi</td>
            <td>{{ $mobil->nopol }}</td>
            <td class="label">PKB</td>
            <td>Rp. {{ number_format($stnk->pkb ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Rangka</td>
            <td>{{ $stnk->nomor_rangka ?? '-' }}</td>
            <td class="label">Pajak 1 Tahun</td>
            <td>{{ $stnk->pajak_1_tahun ?? '-' }}</td>
        </tr>
    </table>

    <h3>Titik Inspeksi Mesin</h3>
    </body>
</html>