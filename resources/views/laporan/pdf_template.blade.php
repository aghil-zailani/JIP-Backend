<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inspeksi Kendaraan</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        
        /* HEADER INSTANSI */
        .header-instansi { width: 100%; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; }
        .header-instansi td { vertical-align: middle; }
        .instansi-logo { width: 120px; max-height: 50px; }
        .laporan-title { text-align: right; font-size: 14px; font-weight: bold; color: #444; }

        /* KENDARAAN INFO (Kiri Foto, Kanan Teks) */
        .car-info-table { width: 100%; margin-bottom: 30px; }
        .car-info-table td { vertical-align: top; }
        .car-photo-container { width: 150px; padding-right: 20px; }
        .car-photo { width: 100%; border-radius: 4px; }
        .car-title { font-size: 16px; font-weight: bold; color: #1a237e; text-transform: uppercase; margin-bottom: 5px; }
        .car-specs { font-size: 12px; color: #666; margin-bottom: 15px; }
        .inspector-info p { margin: 3px 0; color: #555; }
        
        /* TABEL SPESIFIKASI STNK */
        .spec-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 10px; }
        .spec-table td { padding: 8px 10px; border: 1px solid #eee; }
        .spec-table td:nth-child(odd) { width: 25%; color: #777; }
        .spec-table td:nth-child(even) { width: 25%; font-weight: bold; }

        /* SECTION BLUE HEADER (Mirip Ringkasan Inspeksi Moladin) */
        .section-header { background-color: #0d1b54; color: #fff; padding: 10px 15px; font-weight: bold; font-size: 12px; margin-bottom: 15px; border-radius: 4px 4px 0 0; }
        .section-header .right { float: right; font-weight: normal; }

        /* RINGKASAN SCORE */
        .score-box { text-align: center; padding: 15px; border-bottom: 1px solid #eee; margin-bottom: 20px;}
        .score-box table { width: 100%; text-align: center; }
        .icon-normal { color: #2ecc71; font-size: 16px; font-weight: bold; }
        .icon-alert { color: #e67e22; font-size: 16px; font-weight: bold; }

        /* LIST TITIK INSPEKSI */
        .item-row { width: 100%; border-bottom: 1px solid #f9f9f9; padding: 10px 0; }
        .item-name { width: 60%; display: inline-block; font-weight: bold; color: #444; }
        .item-status { width: 45%; display: inline-block; text-align: right; }
        .foto-grid { margin-top: 10px; }
        .foto-grid img { width: 80px; height: 80px; object-fit: cover; margin-right: 5px; border-radius: 4px; }
        
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    <table class="header-instansi">
        <tr>
            <td>
                @if($instansi && $instansi->logo_instansi)
                    <img src="{{ storage_path('app/public/' . $instansi->logo_instansi) }}" class="instansi-logo" alt="Logo">
                @else
                    <h2 style="margin:0; color:#0d1b54;">{{ $instansi->nama_instansi ?? 'Sistem Inspeksi' }}</h2>
                @endif
            </td>
            <td class="laporan-title">Laporan Hasil Inspeksi</td>
        </tr>
    </table>

    <table class="car-info-table">
        <tr>
            <td class="car-photo-container">
                @if($foto_depan)
                    @php                        
                        $clean_path_depan = str_replace('/storage/', '', $foto_depan);
                    @endphp
                    <img src="{{ storage_path('app/public/' . $clean_path_depan) }}" class="car-photo">
                @else
                    <div style="width:150px; height:120px; background:#f0f0f0; text-align:center; padding-top:40px; color:#ccc;">
                        Foto Tidak Tersedia
                    </div>
                @endif
            </td>
            <td>
                <div class="car-title">{{ $mobil->nama_mobil ?? 'KENDARAAN' }}</div>
                <div class="car-specs">
                    {{ $mobil->tahun_mobil ?? '-' }} • {{ $informasi_umum->kapasitas_mesin ?? '-' }} CC • {{ $informasi_umum->transmisi ?? '-' }} • {{ $informasi_umum->bahan_bakar ?? '-' }}
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
        <!-- <tr>
            <td>Model</td>
            <td>{{ $informasi_umum->model ?? '-' }}</td>
        </tr> -->
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
            <td>{{ $informasi_umum->jarak_tempuh ?? '-' }}</td>
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

    <div class="section-header">
        Ringkasan Inspeksi
        <span class="right">Total Titik Inspeksi: {{ $total_titik }}</span>
    </div>
    
    <div class="score-box">
        <table>
            <tr>
                <td>
                    <div class="icon-normal">Normal</div>
                    <div style="font-weight:bold; font-size:14px; margin-top:5px;">{{ $titik_normal }} titik</div>
                </td>
                <td>
                    <div class="icon-alert">! Tidak Normal</div>
                    <div style="font-weight:bold; font-size:14px; margin-top:5px;">{{ $titik_tidak_normal }} titik</div>
                </td>
            </tr>
        </table>
    </div>

    @foreach($hasil_inspeksi as $kategori => $items)
        <div class="page-break"></div>

        <div class="section-header">
            {{ $kategori }}
            <span class="right">Titik Inspeksi: {{ count($items) }}</span>
        </div>

        @foreach($items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->itemInspeksi->nama_item }}</div>
                <div class="item-status">
                    @if($item->status_kondisi == 'normal')
                        <span style="color:#2ecc71; font-weight:bold;">Normal</span>
                    @else
                        <span style="color:#e74c3c; font-weight:bold;">! Tidak Normal</span>
                    @endif
                </div>

                @if($item->foto_utama)
                    <div class="foto-grid">
                        <img src="{{ public_path($item->foto_utama) }}">
                    </div>
                @endif
                
                @if($item->catatan)
                    <div style="margin-top:5px; color:#e67e22; font-style:italic;">Catatan: {{ $item->catatan }}</div>
                @endif
            </div>
        @endforeach
    @endforeach

</body>
</html>