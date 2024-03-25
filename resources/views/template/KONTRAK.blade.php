@extends('halaman')
@section('main')
<main class="main">
    <div style="text-align: center; margin-top: 1pt">
        <p style="font-weight: bold; text-decoration: underline">
            SURAT PERJANJIAN
        </p>
        <p style="font-weight: bold;" >Untuk Melaksanakan {{$lelang->pkt_nama}}</p>
        <p style="font-weight: bold">Nomor : {{$kontrak->kontrak_no}}</p>
    </div>
    <div style="margin-top: 25pt">
        <p>SURAT PERJANJIAN ini berikut semua lampirannya (selanjutnya disebut "Kontrak") dibuat dan ditandatangani di Kota {{$kontrak->kontrak_kota}}
pada hari {{ Carbon\Carbon::parse($kontrak->kontrak_tanggal)->isoFormat('dddd') }} tanggal {{ Carbon\Carbon::parse($kontrak->kontrak_tanggal)->isoFormat('D') }} bulan {{ Carbon\Carbon::parse($kontrak->kontrak_tanggal)->isoFormat('MMMM') }} tahun {{ Carbon\Carbon::parse($kontrak->kontrak_tanggal)->isoFormat('Y') }} antara:</p>
    </div>
    <div style="margin-top: 5pt">
        <table>
            <tr style="vertical-align: top;"><td>1.</td><td><p style="text-align: justify;">{{$kontrak->nama_ppk_kontrak}} selaku Pejabat Pembuat Kontrak, yang bertindak untuk dan atas nama <b>RSUD GAMBIRAN KOTA KEDIRI</b>, yang
berkedudukan di {{$sppbj->alamat_satker}}, berdasarkan Surat Keputusan {{$kontrak->no_sk_ppk_kontrak}} selanjutnya disebut <b>"Pejabat Penandatangan Kontrak"</b> dan</p></td></tr>
            <tr style="vertical-align: top;"><td>2.</td><td><p style="text-align: justify;">{{$kontrak->kontrak_wakil_penyedia}}, {{$kontrak->kontrak_jabatan_wakil}}, yang bertindak untuk dan atas nama {{$rekanan->rkn_nama}}, yang berkedudukan di {{$rekanan->rkn_alamat}}, berdasarkan Akta Pendirian/Anggaran Dasar No. {{$rekanan->akta['lhkp_no']}} tanggal {{ Carbon\Carbon::parse($rekanan->akta['lhkp_tanggal'])->isoFormat('D MMMM Y')}} , selanjutnya disebut <b>"penyedia"<b></p></td></tr>
        </table>
    </div>
    <div style="margin-top: 10pt">
        <p>Para Pihak menerangkan terlebih dahulu bahwa :</p>
        <table>
            <tr style="vertical-align: top;">
                <td>a.</td><td><p style="text-align: justify">Telah diadakan proses pemilihan penyedia yang telah sesuai dengan Dokumen Pemilihan</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>b.</td><td><p style="text-align: justify">Pejabat Penandatangan Kontrak telah menunjuk Penyedia melalui Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)
nomor {{$sppbj->sppbj_no}}, tanggal {{ Carbon\Carbon::parse($sppbj->sppbj_tgl_buat)->isoFormat('D')}} bulan {{ Carbon\Carbon::parse($sppbj->sppbj_tgl_buat)->isoFormat('MMMM')}} tahun {{ Carbon\Carbon::parse($sppbj->sppbj_tgl_buat)->isoFormat('Y')}}, untuk melaksanakan Pekerjaan sebagaimana
diterangkan dalam Syarat-Syarat Umum Kontrak</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>c.</td><td><p style="text-align: justify">Penyedia telah menyatakan kepada Pejabat Penandatangan Kontrak, memenuhi persyaratan kualifikasi, memiliki
keahlian profesional, personal, dan sumber daya teknis, serta telah menyetujui untuk menyediakan Jasa Konsultansi
sesuai dengan persyaratan dan ketentuan dalam Kontrak ini.</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>d.</td><td><p style="text-align: justify">Pejabat Penandatangan Kontrak dan Penyedia menyatakan memiliki kewenangan untuk menandatangani Kontrak ini,
dan mengikat pihak yang diwakili</p></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>e.</td><td><p style="text-align: justify">Pejabat Penandatangan Kontrak dan Penyedia mengakui dan menyatakan bahwa sehubungan dengan
                        penandatanganan Kontrak ini masing-masing pihak:</p>
                    <table>
                        <tr style="vertical-align: top;">
                            <td>1.</td><td><p>telah dan senantiasa diberikan kesempatan untuk didampingi oleh advokat;</p></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>2.</td><td><p>menandatangani Kontrak ini setelah meneliti secara patut;</p></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>3.</td><td><p>telah membaca dan memahami secara penuh ketentuan Kontrak ini;</p></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>4.</td><td><p>telah mendapatkan kesempatan yang memadai untuk memeriksa dan mengkonfirmasikan semua ketentuan dalam
                                    Kontrak ini beserta semua fakta dan kondisi yang terkait.</p></td>
                        </tr>                        
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 10pt">
        <p style="text-align: justify">
            MAKA OLEH KARENA ITU, Pejabat Penandatangan Kontrak dan Penyedia dengan ini bersepakat dan menyetujui hal-hal
sebagai berikut:
        </p>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 1</p>
            <p>Istilah dan Ungkapan</p>
        </div>
        <div style="margin-top: 5pt"><p style="text-align: justify">Peristilahan dan ungkapan dalam Surat Perjanjian ini memiliki arti dan makna yang sama seperti yang tercantum dalam
lampiran Kontrak ini.</p></div>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 2</p>
            <p>Ruang Lingkup Pekerjaan</p>
        </div>
        <div style="margin-top: 5pt">{!! $kontrak->kontrak_lingkup_pekerjaan !!}</div>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 3</p>
            <p>Jenis dan Nilai Kontrak</p>
        </div>
        <div style="margin-top: 5pt">
            <table>
                <tr style="vertical-align: top;">
                    <td>1.</td><td><p>Pengadaan {{$lelang->jenis}} ini menggunakan Jenis Kontrak {{$lelang->kontrak}}</p></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>2.</td><td><p>Nilai Kontrak termasuk Pajak Pertambahan Nilai(PPN) adalah sebesar Rp {{number_format($kontrak->kontrak_nilai, 0, ',', '.')}} ,- </p><p>({{ucwords($kontrak->kontrak_nilai_abc)}} Rupiah)</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 4</p>
            <p>Dokumen Kontrak</p>
        </div>
        <div style="margin-top: 5pt">
            <table>
                <tr style="vertical-align: top;">
                    <td>1.</td><td><p style="text-align: justify">dokumen-dokumen berikut merupakan kesatuan dan bagian yang tidak terpisahkan dari Kontrak ini:</p>
                        <table>
                            <tr style="vertical-align: top;">
                                <td>a.</td><td><p>adendum/perubahan Kontrak (apabila ada);</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>b.</td><td><p>Kontrak;</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>c.</td><td><p>syarat-syarat khusus kontrak;</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>d.</td><td><p>syarat-syarat umum kontrak;</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>e.</td><td><p>Dokumen Penawaran;</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>f.</td><td><p>Spesifikasi teknis;</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>g.</td><td><p>gambar-gambar (apabila ada);</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>h.</td><td><p>daftar kuantitas dan harga (apabila ada);</p></td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>i.</td><td><p>dokumen lainnya seperti: jaminan-jaminan, SPPBJ, BAHP, BAPP.</p></td>
                            </tr>                            
                        </table>
                    </td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>2.</td><td><p style="text-align: justify">Dokumen Kontrak dibuat untuk saling menjelaskan satu sama lain, dan jika terjadi pertentangan antara ketentuan dalam
suatu dokumen dengan ketentuan dalam dokumen yang lain maka yang berlaku adalah ketentuan dalam dokumen yang
lebih tinggi berdasarkan urutan hierarki pada ayat(1) di atas;</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 5</p>
            <p>Hak dan Kewajiban</p>
        </div>
        <div style="margin-top: 5pt"><p style="text-align: justify">Hak dan kewajiban timbal-balik Pejabatan Penandatangan Kontrak dan Penyedia dinyatakan dalam Syarat-Syarat Umum
Kontrak (SSUK) dan Syarat-Syarat Khusus Kontrak (SSKK).</p></div>
    </div>
    <div style="margin-top: 10pt">
        <div style="text-align: center;">
            <p>Pasal 6</p>
            <p>Masa Berlaku Kontrak</p>
        </div>
        <div style="margin-top: 5pt"><p style="text-align: justify">Masa berlaku Kontrak ini terhitung sejak tanggal penandatanganan Kontrak sampai dengan selesainya pekerjaan dan
terpenuhinya seluruh hak dan kewajiban Para Pihak sebagaimana diatur dalam SSUK dan SSKK.</p>
            <p style="text-align: justify; margin-top: 5pt">DENGAN DEMIKIAN, Pejabat Penandatangan Kontrak dan Penyedia telah bersepakat untuk menandatangani Kontrak
menandatangani Kontrak ini pada tanggal tersebut di atas dan melaksanakan Kontrak sesuai dengan ketentuan peraturan
perundang-undangan di Republik Indonesia dan dibuat dalam 2 (dua) rangkap, masing-masing dibubuhi dengan materai,
mempunyai kekuatan hukum yang sama dan mengikat bagi Para Pihak, rangkap yang lain dapat diperbanyak sesuai
kebutuhan tanpa dibubuhi materai.</p>
        </div>
    </div>
</main>
@endsection