@extends('halaman')
@section('main')
<main class="main">
    <div style="text-align: center; margin-top: 1pt">
        <p style="font-weight: bold; text-decoration: underline">
            SURAT PENUNJUKAN PENYEDIA BARANG/JASA (SPPBJ) 
        </p>
    </div>
    <table style="margin-top: 20pt">
        <tr>
            <td>Nomor	:</td>
            <td>{{ $sppbj->sppbj_no }}</td>
            <td width="40%">{{$sppbj->sppbj_kota}} , {{ Carbon\Carbon::parse($sppbj->sppbj_tgl_buat)->isoFormat('D MMMM Y')}}</td>
        </tr>
        <tr>
            <td>Lampiran:</td>
            <td>{{ $sppbj->sppbj_lamp }}</td>
            <td width="40%"></td>
        </tr>
        <tr >
            <td>Perihal :</td>
            <td>Penunjukan Penyedia untuk Pelaksanaan {{$lelang->pkt_nama}}</td>
            <td width="40%"></td>
        </tr>
    </table>
    <div style="text-align: start; margin-top: 20pt">
        <p>Kepada Yth.</p>
        <p>{{$peserta->rkn_nama}}</p>
        <p>Di Tempat</p>
    </div>
    <div style="margin-top: 0.5cm">
        <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">
            Dengan ini kami beritahukan bahwa penawaran Saudara melalui aplikasi Sistem Pengadaan pada:
        </p>
        <table style=" width:50%;">
            <tr>
                <td width="50%">Kode Paket Pengadaan</td><td>:</td><td width="50%">{{$lelang->lls_id}}</td>
            </tr>
            <tr>
                <td width="50%">Nama Paket Pengadaan</td><td>:</td><td width="50%">{{$lelang->pkt_nama}}</td>
            </tr>
            <tr>
                <td width="50%">Nilai Penawaran</td><td>:</td><td width="50%">Rp. {{number_format($peserta->nev_harga, 0, ',', '.')}} ,00</td>
            </tr>
            <tr>
                <td width="50%">Nilai Terkoreksi</td><td>:</td><td width="50%">Rp. {{number_format($peserta->nev_harga_terkoreksi, 0, ',', '.')}} ,00</td>
            </tr>
            <tr>
                <td width="50%">Nilai Final</td><td>:</td><td width="50%">Rp. {{number_format($sppbj->harga_final, 0, ',', '.')}} ,00</td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 0.5cm">
         <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">
            Kami nyatakan diterima/disetujui.
        </p>
    </div>
    <div style="margin-top: 0.5cm">
        <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">Sebagai tindak lanjut dari Surat Penunjukan Penyedia Barang/Jasa (SPPBJ) ini Saudara diharuskan untuk
menyerahkan Jaminan Pelaksanaan (jika ada) dan menandatangani Surat Perjanjian paling lambat 14 (empat
belas) hari kerja setelah diterbitkannya SPPBJ</p>
    </div>
    <div style="margin-top: 0.5cm">
        <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">Kegagalan Saudara untuk menerima penunjukan ini, akan dikenakan sanksi sesuai ketentuan dalam Peraturan
Presiden No. 16 Tahun 2018 tentang Pengadaan Barang/Jasa Pemerintah.</p>
    </div>
</main>
@endsection