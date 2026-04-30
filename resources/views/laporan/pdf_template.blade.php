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
        .car-title { font-size: 18px; font-weight: bold; color: #0d1b54; text-transform: uppercase; margin-bottom: 5px; }
        .car-specs { font-size: 13px; color: #666; margin-bottom: 15px; }
        .inspector-info p { margin: 3px 0; color: #555; }
        
        /* TABEL SPESIFIKASI STNK */
        .spec-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 11px; background: #fff; }
        .spec-table td { padding: 10px; border: 1px solid #eee; }
        .spec-table td:nth-child(odd) { width: 25%; color: #777; }
        .spec-table td:nth-child(even) { width: 25%; font-weight: bold; }

        /* SCORE CARD */
        .score-card { background: #fff; border: 1px solid #eee; border-radius: 8px; margin-bottom: 30px; overflow: hidden; }
        .score-header { background-color: #0d1b54; color: #fff; padding: 12px 15px; font-weight: bold; font-size: 14px; }
        .score-header .right { float: right; font-weight: normal; }

        /* RINGKASAN SCORE BOX */
        .score-box { text-align: center; padding: 15px 20px; }
        .score-box table { width: 100%; text-align: center; }
        .icon-normal { color: #2ecc71; font-size: 15px; font-weight: bold; }
        .icon-alert  { color: #e74c3c; font-size: 15px; font-weight: bold; }

        /* RINGKASAN ITEM RUSAK */
        .rusak-alert {
            background-color: #fff8f0;
            border: 1px solid #f0a500;
            border-radius: 6px;
            padding: 10px 14px;
            margin: 10px 15px 15px 15px;
        }
        .rusak-alert-title {
            font-weight: bold;
            color: #c0392b;
            font-size: 12px;
            margin-bottom: 8px;
            display: block;
        }
        .rusak-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .rusak-table th {
            background-color: #f5e6d0;
            color: #7a3b00;
            padding: 6px 10px;
            text-align: left;
            font-size: 11px;
        }
        .rusak-table td { padding: 5px 10px; border-bottom: 1px solid #faebd7; color: #444; }
        .rusak-table tr:last-child td { border-bottom: none; }
        .badge-perlu { background:#F5D627; color:#333; border-radius:3px; padding:1px 6px; font-size:10px; font-weight:bold; }
        .badge-rusak { background:#e74c3c; color:#fff; border-radius:3px; padding:1px 6px; font-size:10px; font-weight:bold; }

        /* KARTU KATEGORI INSPEKSI */
        .category-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; background: #fff; border-radius: 8px; border: 1px solid #eee; }
        .category-table thead { display: table-header-group; }
        .category-table th { background-color: #0d1b54; color: #fff; padding: 12px 15px; font-size: 14px; text-align: left; }
        .category-table td { border-bottom: 1px solid #f0f0f0; padding: 15px; vertical-align: top; }
        .category-table tr:last-child td { border-bottom: none; }

        /* STATISTIK PER KATEGORI */
        .kat-stat-row { padding: 8px 15px 12px 15px; background-color: #f7f9ff; border-bottom: 1px solid #e8eaf0; }
        .kat-stat-table { width: 100%; text-align: center; }
        .kat-stat-table td { padding: 5px 10px; }
        .kat-stat-label { font-size: 11px; color: #777; margin-top: 2px; }
        .kat-stat-number { font-size: 15px; font-weight: bold; color: #333; }

        /* ITEM DETAIL */
        .item-header { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .item-header-name { vertical-align: middle; font-size: 15px; font-weight: bold; color: #333; }
        .item-header-badge { vertical-align: middle; text-align: right; white-space: nowrap; width: 1%; }
        .status-badge-normal  { display:inline-block; background:#e8f8ef; color:#27ae60; border:1px solid #27ae60; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold; }
        .status-badge-perlu   { display:inline-block; background:#fffbe6; color:#b8860b; border:1px solid #F5D627; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold; }
        .status-badge-lainnya { display:inline-block; background:#fdf2f2; color:#c0392b; border:1px solid #e74c3c; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold; }

        /* FOTO UTAMA - table grid */
        .foto-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-top: 8px; margin-bottom: 4px; }
        .foto-table td { vertical-align: top; }
        .foto-table img { width: 100%; height: 150px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; display: block; }

        /* FOTO KERUSAKAN - table grid */
        .kerusakan-table { width: 100%; border-collapse: separate; border-spacing: 6px; }
        .kerusakan-table td { vertical-align: top; }
        .kerusakan-table img { width: 100%; height: 150px; object-fit: cover; border-radius: 4px; border: 1px solid #e74c3c; display: block; }

        .foto-kerusakan-wrapper {
            border: 1px solid #e74c3c;
            background-color: #fdf2f2;
            padding: 10px 12px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .foto-kerusakan-label {
            font-weight: bold;
            color: #c0392b;
            font-size: 12px;
            margin-bottom: 6px;
            border-bottom: 1px dashed #fadbd8;
            padding-bottom: 5px;
            display: block;
        }

        /* FOTO DOKUMEN (STNK / BPKB) */
        .foto-dokumen-wrapper {            
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            display: block;
        }
        .foto-dokumen-label {
            font-weight: bold;            
            font-size: 12px;
            margin-bottom: 10px;            
            padding-bottom: 6px;
            display: block;
        }
        .foto-dokumen-grid { width: 100%; display: block; }
        .foto-dokumen-grid img {
            width: 200px;
            height: 140px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 8px;
            border-radius: 4px;            
            display: inline-block;
            vertical-align: top;
        }
        .foto-dokumen-empty {
            color: #aaa;
            font-style: italic;
            font-size: 11px;
            padding: 8px 0 14px;
        }

        /* CATATAN ITEM */
        .item-catatan { margin-top: 8px; color: #555; font-style: italic; font-size: 11px; }

        /* KESIMPULAN BOX di Ringkasan */
        .kesimpulan-box {
            background-color: #eaf4fb;
            border: 1px solid #aed6f1;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 12px 15px 15px 15px;
        }
        .kesimpulan-title {
            font-weight: bold;
            color: #1a5276;
            font-size: 12px;
            margin-bottom: 6px;
            display: block;
        }
        .kesimpulan-text {
            color: #2c3e50;
            font-size: 11px;
            line-height: 1.6;
        }

        /* SECTION CATATAN AKHIR */
        .catatan-section {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-top: 20px;
            margin-bottom: 30px;
            overflow: hidden;
            page-break-before: auto;
        }
        .catatan-header {
            background-color: #0d1b54;
            color: #fff;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
        }
        .catatan-body {
            padding: 20px;
            min-height: 80px;
        }
        .catatan-signature {
            margin-top: 40px;
            text-align: right;
            color: #555;
            font-size: 12px;
        }
        .catatan-signature .ttd-name {
            font-weight: bold;
            color: #0d1b54;
            font-size: 13px;
            border-top: 1px solid #aaa;
            display: inline-block;
            padding-top: 5px;
            min-width: 160px;
            text-align: center;
        }

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

    <div class="score-card">
        <div class="score-header">Informasi Mobil</div>
    </div>

    <table class="spec-table">
        <tr>
            <td>Nomor Polisi</td>
            <td>{{ $informasi_umum->nomor_polisi ?? '-' }}</td>            
            <td>Transmisi</td>
            <td>{{ $informasi_umum->transmisi ?? '-' }}</td>  
        </tr>        
        <tr>
            <td>Bahan Bakar</td>
            <td>{{ $informasi_umum->bahan_bakar ?? '-' }}</td>     
            <td>Jarak Tempuh</td>
            <td>{{ $informasi_umum->jarak_tempuh ?? '-' }} KM</td>       
        </tr>         
        <tr>
            <td>Tahun Pembuatan</td>
            <td>{{ $mobil->tahun_mobil ?? '-' }}</td>            
            <td>Merek</td>
            <td>{{ $mobil->nama_mobil ?? '-' }}</td>
        </tr>        
        <tr>
            <td>Tipe</td>
            <td>{{ $informasi_umum->tipe_mobil ?? '-' }}</td>
            <td>Warna</td>
            <td>{{ $informasi_umum->warna_mobil ?? '-' }}</td>
        </tr>         
        <tr>
            <td>Kapasitas   </td>
            <td>{{ $informasi_umum->kapasitas_mesin ?? '-' }} KM</td>
        </tr>                          
    </table>

    <div class="score-card">
        <div class="score-header">Dokumen Kendaraan</div>
    </div>

    <table class="spec-table">
        <tr>            
            <td>PKB</td>
            <td>Rp. {{ number_format($stnk->pkb ?? 0, 0, ',', '.') }}</td>
            <td>Pajak 1 Tahun</td>
            <td>{{ $stnk->pajak_1_tahun ?? '-' }}</td>   
        </tr>        
        <tr>
            <td>Pajak 5 Tahun</td>
            <td>{{ $stnk->pajak_5_tahun ?? '-' }}</td>
            <td>Nomor Rangka</td>
            <td>{{ $stnk->nomor_rangka ?? '-' }}</td>
        </tr>        
        <tr>
            <td>Nomor Mesin</td>
            <td>{{ $stnk->nomor_mesin ?? '-' }}</td>
            <td>Biaya Pajak per Tahun</td>
            <td>Rp. {{ number_format($stnk->pkb ?? 0, 0, ',', '.') }}</td>
        </tr>        
        <tr>
            <td>Nama Pemilik</td>
            <td>{{ $bpkb->nama_pemilik ?? '-' }}</td>
            <td>Nomor BPKB</td>
            <td>{{ $bpkb->nomor_bpkb ?? '-' }}</td>
        </tr>          
        <tr>
            <td>Kepemilikan Mobil</td>
            <td>{{ $bpkb->kepemilikan_mobil ?? '-' }}</td>
            <td>SPH</td>
            @if ($bpkb->sph == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $bpkb->sph }}</td>
            @endif
        </tr>         
        <tr>
            <td>Benang Pembatas</td>
            @if ($bpkb->benang_pembatas == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $bpkb->benang_pembatas }}</td>
            @endif                    
            <td>Hologram Polri</td>
            @if ($bpkb->hologram_polri == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $bpkb->hologram_polri }}</td>
            @endif        
        </tr>            
        <tr>
            <td>Faktur</td>
            @if ($bpkb->faktur == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $bpkb->faktur }}</td>
            @endif        
            <td>Form A</td>
            @if ($bpkb->form_a == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $bpkb->form_a }}</td>
            @endif        
        </tr>               
        <tr>
            <td>Buku Service</td>
            @if ($dokumen_lain->buku_service == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $dokumen_lain->buku_service }}</td>
            @endif        
            <td>Buku Manual</td>
            @if ($dokumen_lain->buku_manual == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $dokumen_lain->buku_manual }}</td>
            @endif    
        </tr>          
        <tr>
            <td>Cek Logo Scanner</td>
            @if ($dokumen_lain->cek_logo_scanner == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $dokumen_lain->cek_logo_scanner }}</td>
            @endif    
            <td>KIR</td>
            @if ($dokumen_lain->kir == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $dokumen_lain->kir }}</td>
            @endif    
        </tr>          
        <tr>
            <td>Samsat Online</td>
            @if ($dokumen_lain->samsat_online == 'tidak_ada')
                <td>Tidak Ada</td>
            @else
                <td>{{ $dokumen_lain->samsat_online }}</td>
            @endif    
        </tr>  

    </table>

    <div class="page-break"></div>
    <div class="score-card">
        <div class="score-header">Lampiran Dokumen STNK & BPKB</div>
    </div>

    {{-- Foto STNK --}}
    @if(!empty($stnk->foto_stnk))
        <div class="foto-dokumen-wrapper">
            <span class="foto-dokumen-label">Foto STNK</span>
            <div class="foto-dokumen-grid">
                @php $clean_stnk = str_replace('/storage/', '', $stnk->foto_stnk); @endphp
                <img src="{{ storage_path('app/public/' . $clean_stnk) }}" alt="Foto STNK">
            </div>
        </div>
    @else
        <div class="foto-dokumen-empty">Foto STNK belum tersedia.</div>
    @endif

    {{-- Foto BPKB --}}
    @php
        $fotoBpkbList = array_filter([
            $bpkb->foto_bpkb_1 ?? null,
            $bpkb->foto_bpkb_2 ?? null,
            $bpkb->foto_bpkb_3 ?? null,
            $bpkb->foto_bpkb_4 ?? null,
        ]);
    @endphp
    @if(count($fotoBpkbList) > 0)
        <div class="foto-dokumen-wrapper">
            <span class="foto-dokumen-label">Foto BPKB</span>
            <div class="foto-dokumen-grid">
                @foreach($fotoBpkbList as $fotoBpkb)
                    @php $clean_bpkb = str_replace('/storage/', '', $fotoBpkb); @endphp
                    <img src="{{ storage_path('app/public/' . $clean_bpkb) }}" alt="Foto BPKB">
                @endforeach
            </div>
        </div>
    @else
        <div class="foto-dokumen-empty">Foto BPKB belum tersedia.</div>
    @endif

    <div class="page-break"></div>
    <div class="score-card">
        <div class="score-header">
            Ringkasan Inspeksi
            <span class="right">Total Titik Inspeksi: {{ $total_titik }}</span>
        </div>
        {{-- Satu baris: Kondisi Banjir | Kondisi Tabrak | Normal | Tidak Normal --}}
        <div style="padding: 14px 20px; border-bottom: 1px solid #eee;">
            <table style="width:100%; text-align:center; border-collapse:collapse;">
                <tr>
                    <td style="width:25%; padding: 8px; border-right: 1px solid #eee;">
                        <div style="font-size:10px; color:#999; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Kondisi Banjir</div>
                        @if(strtolower($kondisi_banjir ?? '') === 'bebas_banjir' || strtolower($kondisi_banjir ?? '') === 'bebas banjir')
                            <span style="display:inline-block; background:#e8f8ef; color:#27ae60; border:1px solid #27ae60; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold;">Bebas Banjir</span>
                        @elseif(!empty($kondisi_banjir))
                            <span style="display:inline-block; background:#fdf2f2; color:#c0392b; border:1px solid #e74c3c; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold;">{{ ucwords(str_replace('_', ' ', $kondisi_banjir)) }}</span>
                        @else
                            <span style="color:#aaa; font-size:11px;">-</span>
                        @endif
                    </td>
                    <td style="width:25%; padding: 8px; border-right: 1px solid #eee;">
                        <div style="font-size:10px; color:#999; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Kondisi Tabrak</div>
                        @if(strtolower($kondisi_tabrak ?? '') === 'bebas_tabrak' || strtolower($kondisi_tabrak ?? '') === 'bebas tabrak')
                            <span style="display:inline-block; background:#e8f8ef; color:#27ae60; border:1px solid #27ae60; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold;">Bebas Tabrak</span>
                        @elseif(!empty($kondisi_tabrak))
                            <span style="display:inline-block; background:#fdf2f2; color:#c0392b; border:1px solid #e74c3c; border-radius:4px; padding:2px 10px; font-size:11px; font-weight:bold;">{{ ucwords(str_replace('_', ' ', $kondisi_tabrak)) }}</span>
                        @else
                            <span style="color:#aaa; font-size:11px;">-</span>
                        @endif
                    </td>
                    <td style="width:25%; padding: 8px; border-right: 1px solid #eee;">
                        <div style="font-size:10px; color:#999; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Normal</div>
                        <div style="font-weight:bold; font-size:20px; color:#27ae60;">{{ $titik_normal }}</div>
                        <div style="font-size:10px; color:#aaa;">titik</div>
                    </td>
                    <td style="width:25%; padding: 8px;">
                        <div style="font-size:10px; color:#999; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Tidak Normal</div>
                        <div style="font-weight:bold; font-size:20px; color:#e74c3c;">{{ $titik_tidak_normal }}</div>
                        <div style="font-size:10px; color:#aaa;">titik</div>
                    </td>
                </tr>
            </table>
        </div>

        @if($titik_tidak_normal > 0 && count($item_rusak) > 0)
            <div class="rusak-alert">
                <span class="rusak-alert-title">Berikut adalah bagian yang dinilai <u>tidak normal</u>:</span>
                <table class="rusak-table">
                    <tr>
                        <th style="width:35%;">Kategori</th>
                        <th style="width:45%;">Item Inspeksi</th>
                        <th style="width:20%; text-align:center;">Status</th>
                    </tr>
                    @foreach($item_rusak as $rusak)
                        <tr>
                            <td>{{ $rusak['kategori'] }}</td>
                            <td>{{ $rusak['nama_item'] }}</td>
                            <td style="text-align:center;">
                                @if($rusak['status'] === 'perlu_perbaikan')
                                    <span class="badge-perlu">Perlu Perbaikan</span>
                                @else
                                    <span class="badge-rusak">{{ ucfirst(str_replace('_', ' ', $rusak['status'])) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div style="padding: 10px 20px 15px; color:#27ae60; font-weight:bold; font-size:12px;">
                Semua titik inspeksi dalam kondisi normal.
            </div>
        @endif
        
        @if(!empty($catatan_tambahan))
            <div class="kesimpulan-box">
                <span class="kesimpulan-title">Kesimpulan Keseluruhan Inspeksi</span>
                <div class="kesimpulan-text">{{ $catatan_tambahan }}</div>
            </div>
        @endif
    </div>
    
    @foreach($hasil_inspeksi as $kategori => $items)
        <div class="page-break"></div>

        @php
            $stat = $statistik_per_kategori[$kategori] ?? ['normal' => 0, 'tidak_normal' => 0];
        @endphp

        <table class="category-table">
            <thead>
                <tr>
                    <th>{{ $kategori }}</th>
                    <th style="text-align: right; font-weight: normal; white-space: nowrap; width: 1%;">Titik Inspeksi: {{ count($items) }}</th>
                </tr>
            </thead>            
            <tr>
                <td colspan="2" style="padding:0; border-bottom: 1px solid #e0e4f0; background-color:#f7f9ff;">
                    <table class="kat-stat-table">
                        <tr>
                            <td>
                                <div class="icon-normal" style="font-size:13px;">Normal</div>
                                <div class="kat-stat-number" style="color:#27ae60;">{{ $stat['normal'] }}</div>
                                <div class="kat-stat-label">titik</div>
                            </td>
                            <td>
                                <div class="icon-alert" style="font-size:13px;">Tidak Normal</div>
                                <div class="kat-stat-number" style="color:#e74c3c;">{{ $stat['tidak_normal'] }}</div>
                                <div class="kat-stat-label">titik</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td style="font-size:14px; font-weight:bold; color:#333; vertical-align:middle; padding:12px 15px 4px 15px;">
                            {{ $item->itemInspeksi->nama_item }}
                        </td>
                        <td style="text-align:right; vertical-align:middle; white-space:nowrap; padding:12px 15px 4px 15px; width:1%;">
                            @if($item->status_kondisi === 'normal')
                                <span class="status-badge-normal">Normal</span>
                            @elseif($item->status_kondisi === 'perlu_perbaikan')
                                <span class="status-badge-perlu">Perlu Perbaikan</span>
                            @else
                                <span class="status-badge-lainnya">{{ ucfirst(str_replace('_', ' ', $item->status_kondisi)) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:4px 15px 15px 15px; vertical-align:top;">
                            @if(!empty($item->foto_utama) && is_array($item->foto_utama))
                                @php $fotoChunks = array_chunk($item->foto_utama, 3); @endphp
                                @foreach($fotoChunks as $chunk)
                                    <table class="foto-table">
                                        <tr>
                                            @foreach($chunk as $fotoPath)
                                                @php $clean_path_utama = str_replace('/storage/', '', $fotoPath); @endphp
                                                <td style="width:33.33%;">
                                                    <img src="{{ storage_path('app/public/' . $clean_path_utama) }}" alt="Foto Utama">
                                                </td>
                                            @endforeach
                                            {{-- Isi sisa kolom kosong agar rata --}}
                                            @for($p = count($chunk); $p < 3; $p++)
                                                <td style="width:33.33%;"></td>
                                            @endfor
                                        </tr>
                                    </table>
                                @endforeach
                            @endif

                            @if(isset($item->fotoKerusakans) && count($item->fotoKerusakans) > 0)
                                <div class="foto-kerusakan-wrapper">
                                    <span class="foto-kerusakan-label">
                                        Foto Kerusakan - {{ $item->itemInspeksi->nama_item }}
                                    </span>
                                    @php $rusakChunks = array_chunk($item->fotoKerusakans->all(), 3); @endphp
                                    @foreach($rusakChunks as $chunk)
                                        <table class="kerusakan-table">
                                            <tr>
                                                @foreach($chunk as $foto)
                                                    @php $clean_path_tambahan = str_replace('/storage/', '', $foto->path_foto); @endphp
                                                    <td style="width:33.33%;">
                                                        <img src="{{ storage_path('app/public/' . $clean_path_tambahan) }}" alt="Foto Kerusakan">
                                                    </td>
                                                @endforeach
                                                @for($q = count($chunk); $q < 3; $q++)
                                                    <td style="width:33.33%;"></td>
                                                @endfor
                                            </tr>
                                        </table>
                                    @endforeach
                                </div>
                            @endif

                            @if($item->catatan)
                                <div class="item-catatan">
                                    <span style="color:#e74c3c; font-weight:bold;">Catatan:</span>
                                    {{ $item->catatan }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
    <div class="catatan-section">
        <div class="catatan-header">Catatan</div>
        <div class="catatan-body">
            <p style="color:#333; line-height:1.7; margin:0;">
                <li>Laporan inspeksi ini merupakan hasil pemeriksaan kendaraan sesuai dengan kondisi apa adanya. Jika customer telah membaca dokumen ini, maka dinyatakan telah menyetujui hasil report ini.</li>
                <li>Hasil inspeksi ini valid berdasarkan kondisi kendaraan yang dicek oleh inspektor pada tanggal inspeksi dengan menggunakan alat inspeksi lengkap dan sehubungan yang dicek adalah mobil bekas maka apabila ditemukan atau terjadi kerusakan pada kendaraan bukan tanggung jawab inspektor atau diluar tanggung jawab dari {{ $instansi->nama_instansi ?? $inspektor }}.</li>                
            </p>
            <div class="catatan-signature">
                <div>{{ $tanggal }}</div>
                <br><br><br>
                <div class="ttd-name">
                    {{ $instansi->nama_instansi ?? $inspektor }}
                </div>
            </div>
        </div>
    </div>

</body>
</html>
