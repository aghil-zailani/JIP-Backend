<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inspeksi Kendaraan</title>
    <style>
        @page { margin: 30px 40px; }
        /* Background body dibikin abu-abu super muda agar Card putihnya menonjol */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; background-color: #fafafa; }
        
        /* HEADER INSTANSI */
        .header-instansi { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 20px; background: #fff; }
        .header-instansi td { vertical-align: middle; }
        .instansi-logo { width: 120px; max-height: 70px; }
        .laporan-title { text-align: right; font-size: 16px; font-weight: bold; color: #444; }

        /* KENDARAAN INFO */
        .car-info-table { width: 100%; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; }
        .car-info-table td { vertical-align: top; }
        .car-photo-container { width: 160px; padding-right: 20px; }
        .car-photo { width: 100%; border-radius: 6px; border: 1px solid #eee; }
        .car-title { font-size: 18px; font-weight: bold; color: #0d1b54; text-transform: uppercase; margin-bottom: 5px; }
        .car-specs { font-size: 13px; color: #666; margin-bottom: 15px; }
        .inspector-info p { margin: 3px 0; color: #555; }
        
        /* TABEL SPESIFIKASI STNK */
        .spec-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; background: #fff; }
        .spec-table td { padding: 10px; border: 1px solid #eee; }
        .spec-table td:nth-child(odd) { width: 25%; color: #777; }
        .spec-table td:nth-child(even) { width: 25%; font-weight: bold; }

        /* RINGKASAN SCORE CARD */
        .score-card { background: #fff; border: 1px solid #eee; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
        .score-header { background-color: #0d1b54; color: #fff; padding: 12px 15px; font-weight: bold; font-size: 14px; }
        .score-header .right { float: right; font-weight: normal; }
        .score-box { text-align: center; padding: 20px; }
        .score-box table { width: 100%; text-align: center; }
        .icon-normal { color: #2ecc71; font-size: 16px; font-weight: bold; }
        .icon-alert { color: #e74c3c; font-size: 16px; font-weight: bold; }

        /* KARTU KATEGORI INSPEKSI (Rahasia Header Berulang) */
        .category-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; background: #fff; border-radius: 8px; border: 1px solid #eee; }
        /* Aturan agar Thead (Header Biru) berulang jika tabel terpotong halaman */
        .category-table thead { display: table-header-group; }
        /* Aturan agar baris tidak terpotong di tengah-tengah foto */
        .category-table tr { page-break-inside: avoid; } 
        
        .category-table th { background-color: #0d1b54; color: #fff; padding: 12px 15px; font-size: 14px; text-align: left; }
        .category-table td { border-bottom: 1px solid #f9f9f9; padding: 15px; vertical-align: top; }
        .category-table tr:last-child td { border-bottom: none; }

        .item-title { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 12px; display: block; }
        .item-status { font-size: 13px; font-weight: bold; text-align: right; }
        
        /* FOTO UTAMA */
        .foto-grid { margin-top: 10px; width: 100%; }
        .foto-grid img { width: 140px; height: 140px; margin-bottom: 10px; object-fit: cover; margin-right: 10px; border-radius: 6px; border: 1px solid #ddd; }

        /* FOTO KERUSAKAN */
        .foto-kerusakan-wrapper {
            margin-top: 12px;
            padding: 10px 12px;
            background-color: #fff5f5;
            border: 2px solid #e74c3c;
            border-radius: 6px;
            page-break-inside: avoid;
        }
        .foto-kerusakan-label {
            display: block;
            font-size: 11px;
            font-weight: bold;
            color: #c0392b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            padding: 3px 8px;
            background-color: #e74c3c;
            color: #ffffff;
            border-radius: 4px;
            width: fit-content;
        }
        .foto-kerusakan-wrapper img { width: 130px; height: 130px; object-fit: cover; margin-right: 8px; margin-bottom: 6px; border-radius: 5px; border: 2px solid #e74c3c; }
        
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    <table class="header-instansi">
        <tr>
            <td>
                @if($instansi && $instansi->logo_instansi)
                    @php $clean_logo = str_replace('/storage/', '', $instansi->logo_instansi); @endphp
                    <img src="{{ storage_path('app/public/' . $clean_logo) }}" class="instansi-logo" alt="Logo">
                @else
                    <h2 style="margin:0; color:#0d1b54;">{{ $instansi->nama_instansi ?? 'Sistem Inspeksi' }}</h2>
                @endif
            </td>
            <td class="laporan-title">Laporan Hasil Inspeksi</td>
        </tr>
    </table>

    <table class="car-info-table">
        <tr>                        
            <td>
                <div class="car-title">{{ $mobil->nama_mobil ?? 'KENDARAAN' }}</div>
                <div class="car-specs">
                    {{ $mobil->tahun_mobil ?? '-' }} - {{ $informasi_umum->kapasitas_mesin ?? '-' }} CC - {{ $informasi_umum->transmisi ?? '-' }} - {{ $informasi_umum->bahan_bakar ?? '-' }}
                </div>                
                <div class="inspector-info">
                    <p>Inspektor:</p>
                    <p style="font-weight:bold; color:#333; margin-bottom:15px;">{{ $inspektor }}</p>
                    <p>{{ $tanggal }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="spec-table">
        <tr>
            <td>Nomor Polisi</td>
            <td>{{ $informasi_umum->nomor_polisi ?? '-' }}</td>
            <td>PKB</td>
            <td>Rp. {{ number_format($stnk->pkb ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tahun Pembuatan</td>
            <td>{{ $mobil->tahun_mobil ?? '-' }}</td>
            <td colspan="2" rowspan="7"></td>
        </tr>
        <tr>
            <td>Merek</td>
            <td>{{ $mobil->nama_mobil ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tipe</td>
            <td>{{ $informasi_umum->tipe_mobil ?? '-' }}</td>
        </tr>
        <tr>
            <td>Warna</td>
            <td>{{ $informasi_umum->warna_mobil ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Rangka</td>
            <td>{{ $stnk->nomor_rangka ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor Mesin</td>
            <td>{{ $stnk->nomor_mesin ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jarak Tempuh</td>
            <td>{{ $informasi_umum->jarak_tempuh ?? '-' }} KM</td>
        </tr>
        <tr>
            <td>Pajak 1 Tahun</td>
            <td>{{ $stnk->pajak_1_tahun ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pajak 5 Tahun</td>
            <td>{{ $stnk->pajak_5_tahun ?? '-' }}</td>
        </tr>
        <tr>
            <td>Biaya Pajak per Tahun</td>
            <td>Rp. {{ number_format($stnk->pkb ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="score-card">
        <div class="score-header">
            Ringkasan Inspeksi
            <span class="right">Total Titik Inspeksi: {{ $total_titik }}</span>
        </div>
        <div class="score-box">
            <table>
                <tr>
                    <td>
                        <div class="icon-normal">Normal</div>
                        <div style="font-weight:bold; font-size:16px; margin-top:8px; color:#444;">{{ $titik_normal }} titik</div>
                    </td>
                    <td>
                        <div class="icon-alert">Tidak Normal</div>
                        <div style="font-weight:bold; font-size:16px; margin-top:8px; color:#444;">{{ $titik_tidak_normal }} titik</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @foreach($hasil_inspeksi as $kategori => $items)
        <div class="page-break"></div>

        <table class="category-table">
            <thead>
                <tr>
                    <th>{{ $kategori }}</th>
                    <th style="text-align: right; font-weight: normal;">Titik Inspeksi: {{ count($items) }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td style="width: 65%;">
                            <span class="item-title">{{ $item->itemInspeksi->nama_item }}</span>                            
                            @if($item->foto_utama)
                                @php $clean_path_item = str_replace('/storage/', '', $item->foto_utama); @endphp
                                <div class="foto-grid">
                                    @if($item->foto_utama)
                                        @php $clean_path_utama = str_replace('/storage/', '', $item->foto_utama); @endphp
                                        <img src="{{ storage_path('app/public/' . $clean_path_utama) }}">
                                    @endif
                                </div>

                                @if(isset($item->fotoKerusakans) && count($item->fotoKerusakans) > 0)
                                    <div class="foto-kerusakan-wrapper">
                                        <span class="foto-kerusakan-label">Foto Kerusakan - {{ $item->itemInspeksi->nama_item }}</span>
                                        @foreach($item->fotoKerusakans as $foto)
                                            @php $clean_path_tambahan = str_replace('/storage/', '', $foto->path_foto); @endphp
                                            <img src="{{ storage_path('app/public/' . $clean_path_tambahan) }}">
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                            
                            @if($item->catatan)
                                <div style="margin-top:8px; color:#e74c3c; font-style:italic; font-weight:bold;">
                                    Catatan: <span style="font-weight:normal; color:#444;">{{ $item->catatan }}</span>
                                </div>
                            @endif
                        </td>
                        <td style="width: 35%;" class="item-status">
                            @if($item->status_kondisi == 'normal')
                                <span style="color:#2ecc71;">Normal</span>
                            @else
                                <span style="color:#e74c3c;">{{ $item->status_kondisi }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>