@extends('halaman')
@section('main')
            <main class="main">
                <div style="text-align: center; margin-top: 1pt">
                    <p style="font-weight: bold; text-decoration: underline">
                        BERITA ACARA EVALUASI PENAWARAN
                    </p>
                    <p>Nomor : {{ $brc->brt_no }}</p>
                    <p>Tanggal : {{ Carbon\Carbon::parse($brc->brt_tgl_evaluasi)->isoFormat('D MMMM Y')}}</p>
                </div>
                <table>
                    <tr>
                        <td style="width: 4cm; vertical-align: top">Kode Pengadaan</td>
                        <td style="vertical-align: top">:</td>
                        <td style="padding-left: 2px">{{$lel->lls_id}}</td>
                    </tr>
                    <tr>
                        <td style="width: 4cm; vertical-align: top">Nama Paket</td>
                        <td style="vertical-align: top">:</td>
                        <td style="padding-left: 2px">{{$lel->pkt_nama}}</td>
                    </tr>
                    <tr>
                        <td style="width: 4cm; vertical-align: top">Pagu Anggaran</td>
                        <td style="vertical-align: top">:</td>
                        <td style="padding-left: 2px">
                            Rp. {{number_format($lel->pkt_pagu, 0, ',', '.')}} ,-
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 4cm; vertical-align: top">Nilai Total HPS</td>
                        <td style="vertical-align: top">:</td>
                        <td style="padding-left: 2px">
                            Rp. {{number_format($lel->pkt_hps, 0, ',', '.')}} ,-
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 4cm; vertical-align: top">Jenis Pengadaan</td>
                        <td style="vertical-align: top">:</td>
                        <td style="padding-left: 2px">
                            {{$lel->jenis}}
                            <div style="width: 12cm; padding: 10px 0">
                                <div style="border-bottom: 4px double #000"></div>
                                <div style="border-bottom: 4px double #000"></div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 0.5cm">
                    <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">
                        Pada hari ini
                        <span style="font-weight: bold">
                            {{ Carbon\Carbon::parse($brc->brt_tgl_evaluasi)->isoFormat('D MMMM Y')}}
                        </span>
                        telah dibuat Berita Acara Evaluasi Penawaran untuk pengadaan paket {{$lel->pkt_nama}} dengan hasil
                        sebagaimana berikut:
                    </p>
                </div>
                <div style="margin-top: 0.2cm">
                    <p style="line-height: 1.5; font-size: 11pt">
                        1. Evaluasi Administrasi
                    </p>
                </div>
                <table style="border-collapse: collapse; width:100%;">
                    <tr>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            NO
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Nama Penyedia
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Hasil Evaluasi
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Keterangan
                        </th>
                    </tr>
                    <tbody>
                        <tr>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                1
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                {{$peserta->rkn_nama}}
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                @if ($nilai['administrasi']->nev_lulus == 1)
                                SESUAI
                                @else
                                TIDAK SESUAI
                                @endif
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                    @if ($nilai['administrasi']->nev_lulus == 1)
                                LULUS
                                @else
                                TIDAK LULUS
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 0.2cm">
                    <p style="line-height: 1.5; font-size: 11pt">
                        2. Evaluasi Teknis
                    </p>
                </div>
                <table style="border-collapse: collapse; width:100%;">
                    <tr>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            NO
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Nama Penyedia
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Hasil Evaluasi
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Keterangan
                        </th>
                    </tr>
                    <tbody>
                        <tr>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                1
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                {{$peserta->rkn_nama}}
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                @if ($nilai['teknis']->nev_lulus == 1)
                                SESUAI
                                @else
                                TIDAK SESUAI
                                @endif
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                    @if ($nilai['teknis']->nev_lulus == 1)
                                LULUS
                                @else
                                TIDAK LULUS
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 0.2cm">
                    <p style="line-height: 1.5; font-size: 11pt">
                        3. Evaluasi Harga
                    </p>
                </div>
                <table style="border-collapse: collapse; width:100%;">
                    <tr>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            NO
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Nama Penyedia
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Penawaran
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Hasil Evaluasi
                        </th>
                        <th
                            style="
                            border: 1px solid #0e0e0e;
                            text-align: center;
                            padding: 8px;
                            font-weight: normal;
                            font-weight: bold;
                            "
                            >
                            Keterangan
                        </th>
                    </tr>
                    <tbody>
                        <tr>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                1
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                {{$peserta->rkn_nama}}
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                {{number_format($nilai['harga']->nev_harga, 0, ',', '.')}}
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                @if ($nilai['harga']->nev_lulus == 1)
                                SESUAI
                                @else
                                TIDAK SESUAI
                                @endif
                            </td>
                            <td
                                style="
                                border: 1px solid #0e0e0e;
                                text-align: center;
                                padding: 0 8px;
                                vertical-align: top;
                                "
                                >
                                    @if ($nilai['harga']->nev_lulus == 1)
                                LULUS
                                @else
                                TIDAK LULUS
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top: 0.3cm">
                    <p style="line-height: 1.5; font-size: 11pt">
                        Keterangan Tambahan Lain:
                    </p>
                    <p style="text-indent: 50px; font-size: 11pt">
                        {{ $brc->brt_info }}
                    </p>
                </div>
                <div style="margin-top: 0.5cm">
                    <p style="text-indent: 50px; line-height: 1.5; font-size: 11pt">
                        Demikian Berita Acara ini dibuat dalam rangka secukupnya untuk dapat dipergunakan sebagaimana mestinya.
                    </p>
                </div>
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: center;"> 
                            <div>
                                <p style="line-height: 1.5">Pejabat Pembuat Komitmen (PPK)</p>
                                <p style="line-height: 1.5">RSUD Gambiran Kota Kediri</p>
                                <div style=" margin-top: 50%" >
                                    <p style="font-weight: bold; text-decoration: underline">
                                        YUDI SANTOSA, STP, MM
                                    </p>
                                    <p>NIP. 19761215 200112 1 003</p>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center">
                            <div>
                                <p style="line-height: 1.5">Pokja Pemilihan</p>
                                <p style="line-height: 1.5">RSUD Gambiran Kota Kediri</p>
                                <div style="display: flex; ">
                                    <p>1.</p>
                                    <div style="text-align: left; ">
                                        <p style="font-weight: bold; text-decoration: underline">
                                            Okky Saktiyawan,S.E.,M.Si
                                        </p>
                                        <p>NIP. 19831028 200901 1 006</p>
                                    </div>
                                    <p>1. ………..…</p>
                                </div>
                                <div style="display: flex; ">
                                    <p>2.</p>
                                    <div style="text-align: left; ">
                                        <p style="font-weight: bold; text-decoration: underline">
                                            Maqbul Dian Muhammad S.T.
                                        </p>
                                        <p>NIP. 19780908 2001001 1 001</p>
                                    </div>
                                    <p>2. ………..…</p>
                                </div>
                                <div style="display: flex; ">
                                    <p>3.</p>
                                    <div style="text-align: left;">
                                        <p style="font-weight: bold; text-decoration: underline">
                                            Endik Joko Noritanto, A.Md.PK
                                        </p>
                                        <p>NIP. 19790720 201101 1 007</p>
                                    </div>
                                    <p>3. ………..…</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </main>
@endsection