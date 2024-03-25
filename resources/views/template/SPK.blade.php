@extends('halaman')
@section('main')
<main class="main">
    <div style="text-align: center; margin-top: 1pt">
        <p style="font-weight: bold; text-decoration: underline">
            SURAT PERINTAH KERJA (SPK)
        </p>
        <p style="font-weight: bold;" >Kontrak {{$lelang->kontrak}}</p>
        <p style="margin-top: 5pt">Paket Pekerjaan {{$lelang->jenis}} :</p>
        <p>{{ strtoupper($lelang->pkt_nama)}}</p>
        <p>Nomor : {{$spk->spk_no}}</p>
    </div>
    <div style="margin-top: 25pt">
        <p style="text-align: justify">Yang bertanda tangan di bawah ini :</p>
        <table>
            <tr>
                <td>Nama </td><td>:</td><td> {{$spk->nama_ppk_kontrak}}</td>
            </tr>
            <tr>
                <td>NIP </td><td>:</td><td> {{$spk->nip_ppk_kontrak}}</td>
            </tr>
            <tr>
                <td>Jabatan </td><td>:</td><td> {{$spk->jabatan_ppk_kontrak}}</td>
            </tr>
            <tr>
                <td>Berkedudukan di </td><td>:</td><td> RSUD Gambiran Kota Kediri </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 10pt">
        <p style="text-align: justify">Selanjutnya disebut sebagai Pejabat Pembuat Komitmen berdasarkan Surat Keputusan No. {{$spk->no_sk_ppk_kontrak}}, bersama dengan Surat Perjanjian {{$lelang->pkt_nama}} nomor {{$spk->spk_no}} tanggal {{ Carbon\Carbon::parse($spk->spk_tgl)->isoFormat('D MMMM Y')}} memerintahkan :</p>
        <table>
            <tr>
                <td>Nama </td><td>:</td><td> {{$spk->spk_wakil_penyedia}}</td>
            </tr>
            <tr>
                <td>Jabatan </td><td>:</td><td> {{$spk->spk_jabatan_wakil}}</td>
            </tr>
            <tr>
                <td>Berkedudukan di </td><td>:</td><td> {{$rekanan->rkn_alamat}}</td>
            </tr>
            <tr>
                <td>Akta Notaris Nomor </td><td>:</td><td> {{$rekanan->akta['lhkp_no']}}</td>
            </tr>
            <tr>
                <td>Tanggal </td><td>:</td><td> {{ Carbon\Carbon::parse($rekanan->akta['lhkp_tanggal'])->isoFormat('D MMMM Y')}}</td>
            </tr>
            <tr>
                <td>Notaris </td><td>:</td><td> {{$rekanan->akta['lhkp_notaris']}}</td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 10pt">
        <p style="text-align: justify">selanjutnya disebut sebagai Penyedia Barang;untuk segera memulai pelaksanaan pekerjaan dengan
memperhatikan ketentuan-ketentuan sebagai berikut:</p>
        <table>
            <tr style="vertical-align: top;">
                <td>1. </td><td>Harga Kontrak : <p style="text-align: justify" ><b>Rp. {{number_format($spk->spk_nilai, 0, ',', '.')}} ,- ({{ucwords($spk->spk_nilai_abc)}} Rupiah)</b> termasuk Pajak Pertambahan Nilai (PPN)</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>2. </td><td>Tanggal Mulai Pekerjaan : {{ Carbon\Carbon::parse($spk->spk_content['tgl_brng_diterima'])->isoFormat('D MMMM Y')}}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>3. </td><td>Syarat-syarat pekerjaan : sesuai dengan persyaratan dan ketentuan Kontrak ;</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>4. </td><td>Waktu Penyelesaian : <p>selama {{ $spk->spk_content['waktu_penyelesaian'] }} dan pekerjaan harus sudah selesai pada tanggal {{ Carbon\Carbon::parse($spk->spk_content['tgl_pekerjaan_selesai'])->isoFormat('D MMMM Y')}}</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>5. </td><td>Pembayaran : <p style="text-align: justify" >Pembayaran untuk kontrak ini dilakukan ke {{$spk->spk_nama_bank}} rekening nomor : {{$spk->spk_norekening}} atas nama Penyedia : {{$rekanan->rkn_nama}};</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>6. </td><td>Denda : <p style="text-align: justify" >Terhadap setiap hari keterlambatan pelaksanaan/penyelesaian pekerjaan Penyedia akan
dikenakan Denda Keterlambatan sebesar 1/1000 (satu per seribu) dari Nilai Kontrak atau bagian tertentu
dari Nilai Kontrak sebelum PPN sesuai dengan Syarat-Syarat Umum Kontrak.</p></td>
            </tr>
        </table>
    </div>
</main>
@endsection