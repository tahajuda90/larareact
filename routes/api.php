<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\B_Usaha;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/hello', function () {
    return 'Hello World';
});

Route::get('coba/{brc_id}',[\App\Http\Controllers\Api\V1\BerAcaraController::class,'cetak']);

Route::get('/propinsi', function () {
    return Storage::disk('public')->get('propinsi.json');
});
Route::get('/kota/{id}', function ($id) {
    return Storage::disk('public')->get('regencies/'.$id.'.json');
});
Route::get('/jenis_ijin',function(){
    return Storage::disk('public')->get('jenis_ijin.json');
});
Route::get('template',function(){
    return Storage::disk('public')->download('template-rincian.xlsx');
});

Route::get('base_jadwal',[\App\Http\Controllers\Api\V1\LelUtilityController::class,'template_jadwal']);


//Route::post('auth/login', [App\Http\Controllers\Api\AuthPegawaiController::class,'login'])->name('auth/login');

//Route::post('penyedia/login',[App\Http\Controllers\Api\AuthRekananController::class,'login']);
//Route::get('penyedia/me',[App\Http\Controllers\Api\AuthRekananController::class,'user']);


Route::controller(App\Http\Controllers\Api\V1\ContentController::class)->group(function(){
    Route::post('v1/upload','upload');
    Route::get('v1/download','download');
    Route::get('v1/delete','delete');
    Route::get('v1/list','list');
});

//Route::get('v1/rup_integrasi',[App\Http\Controllers\Api\IntegrasiSirup::class,'sirup']);

Route::controller(\App\Http\Controllers\Api\AuthController::class)->group(function(){
    Route::post('auth/login','login');
    Route::get('auth/me','me')->middleware('jwt.verify');
    Route::get('auth/logout','logout');
});

Route::controller(App\Http\Controllers\Api\V1\ProfileController::class)->group(function(){
    Route::post('penyedia/ijin/{rkn_id}','ius_store');
    Route::get('penyedia/ijin/{ius_id}','ius');
    Route::put('penyedia/ijin/{ius_id}','ius_update');
    Route::get('penyedia/list_ijin/{rkn_id}','ius_list');
    
    Route::post('penyedia/akta/{rkn_id}','lhk_store');
    Route::get('penyedia/akta/{lhk_id}','lhk');
    Route::put('penyedia/akta/{lhk_id}','lhk_update');
    Route::get('penyedia/list_akta/{rkn_id}','lhk_list');
    
    Route::post('penyedia/manajer/{rkn_id}','mnj_store');
    Route::put('penyedia/manajer/{id_mnj}','mnj_update');
    Route::get('penyedia/manajer/{id_mnj}','mnj');
    Route::get('penyedia/list_manajer/{rkn_id}','mnj_list');
    
    Route::post('penyedia/pajak/{rkn_id}','pjk_store');
    Route::put('penyedia/pajak/{pjk_id}','pjk_update');
    Route::get('penyedia/pajak/{pjk_id}','pjk');
    Route::get('penyedia/list_pajak/{rkn_id}','pjk_list');
    
    Route::post('penyedia/pengalaman/{rkn_id}','pgl_store');
    Route::put('penyedia/pengalaman/{pen_id}','pgl_update');
    Route::get('penyedia/pengalaman/{pen_id}','pgl');
    Route::get('penyedia/list_pengalaman/{rkn_id}','pgl_list');
    
    Route::post('penyedia/peralatan/{rkn_id}','prl_store');
    Route::put('penyedia/peralatan/{id_prl}','prl_update');
    Route::get('penyedia/peralatan/{id_prl}','prl');
    Route::get('penyedia/list_peralatan/{rkn_id}','prl_list');
    
    Route::post('penyedia/staf/{rkn_id}','sta_store');
    Route::put('penyedia/staf/{stp_id}','sta_update');
    Route::get('penyedia/staf/{stp_id}','sta');
    Route::get('penyedia/list_staf/{rkn_id}','sta_list');
});



Route::controller(\App\Http\Controllers\Api\V1\PanitiaController::class)->group(function(){
    Route::get('v1/panitia','list_panitia');
    Route::get('v1/panitia/{pnt_id}','panitia');
    Route::post('v1/panitia','panitia_store');
    Route::put('v1/panitia/{pnt_id}','panitia_update');
    Route::put('v1/anggota/{pnt_id}','tambah_anggota');
//    Route::get('v1/anggota/','hapus_anggota');
//    Route::get('v1/list_anggota/{pnt_id}','list_anggota');
});

Route::post('v1/anggaran',[App\Http\Controllers\Api\V1\AnggaranController::class,'add_anggaran']);
Route::post('v1/tambahangg/{pkt_id}',[\App\Http\Controllers\Api\V1\PaketController::class,'tambah_ang']);
Route::post('v1/editangg/{pkt_id}',[\App\Http\Controllers\Api\V1\PaketController::class,'edit_ang']);

Route::controller(App\Http\Controllers\Api\V1\PaketController::class)->group(function(){
    Route::post('v1/InisiasiPaket','inisiasi_paket');
    Route::get('v1/list_paket','list_paket');
    Route::get('v1/paket/{pkt_id}','get_paket');
    Route::put('v1/paket/{pkt_id}','update_paketPPK');
    Route::get('v1/paket_pp/{pkt_id}','paket_pp');
    Route::get('v1/paket_panitia/{pkt_id}','paket_panitia');
});


Route::get('v1/b_usaha',function(){
    return response()->json(['data'=>B_Usaha::all()],201);
});

Route::group(['middleware'=>'jwt.verify'],function(){
    Route::get('me',[App\Http\Controllers\Api\AuthPegawaiController::class,'user']);
    Route::get('logout',[\App\Http\Controllers\Api\AuthPegawaiController::class,'logout'])->name('logout');    
    Route::post('v1/pegawai', [App\Http\Controllers\Api\V1\PegawaiController::class,'store'])->name('v1/pegawai');
    Route::patch('v1/pegawai/{peg_id}', [App\Http\Controllers\Api\V1\PegawaiController::class,'store_update']);
    Route::put('v1/pegawai/{peg_id}', [App\Http\Controllers\Api\V1\PegawaiController::class,'store_update']);
});


Route::get('v1/list_pegawai',[\App\Http\Controllers\Api\V1\PegawaiController::class,'list_user']);
Route::get('v1/list_pegawai/{usgrp}',[\App\Http\Controllers\Api\V1\PegawaiController::class,'list_userd']);
Route::get('v1/pegawai/{peg_id}',[App\Http\Controllers\Api\V1\PegawaiController::class,'pegawai'])->name('v1/pegawai');


Route::controller(App\Http\Controllers\Api\V1\RekananController::class)->group(function(){
    Route::get('v1/penyedia','list_rekanan');
    Route::get('v1/penyedia/{rkn_id}','rekanan');
    Route::post('v1/penyedia','store');
    Route::put('v1/penyedia/{rkn_id}','store_update');
});

Route::prefix('v1')->group(function () {
    Route::prefix('ADM')->group(function () {
        Route::controller(App\Http\Controllers\Api\V1\PegawaiController::class)->group(function () {
            Route::get('pegawai', 'list_user');
            Route::get('pegawai/{peg_id}', 'pegawai');
            Route::post('pegawai', 'store')->middleware('jwt.verify');
            Route::put('pegawai/{peg_id}', 'store_update')->middleware('jwt.verify');
        });
        Route::controller(App\Http\Controllers\Api\V1\RekananController::class)->group(function () {
            Route::get('penyedia', 'list_rekanan');
            Route::get('penyedia/{rkn_id}', 'rekanan');
            Route::put('penyedia/{rkn_id}', 'store_update')->middleware('jwt.verify');
        });
    });

    Route::prefix('KIPBJ')->group(function () {
        Route::get('list_pegawai/{usgrp}', [\App\Http\Controllers\Api\V1\PegawaiController::class, 'list_userd']);
        Route::controller(\App\Http\Controllers\Api\V1\PanitiaController::class)->group(function () {
            Route::get('panitia', 'list_panitia');
            Route::get('panitia/{pnt_id}', 'panitia');
            Route::post('panitia', 'panitia_store')->middleware('jwt.verify');
            Route::put('panitia/{pnt_id}', 'panitia_update')->middleware('jwt.verify');
            Route::put('anggota/{pnt_id}','tambah_anggota');
        });
        Route::controller(App\Http\Controllers\Api\V1\PaketController::class)->group(function () {
            Route::get('list_paket/{user_id}', 'list_paketKIPBJ');
            Route::get('paket/{pkt_id}', 'get_paket');
            Route::put('paket/{pkt_id}', 'update_paketPPK')->middleware('jwt.verify');
            Route::get('paket_pp/{pkt_id}','paket_pp');
            Route::get('paket_panitia/{pkt_id}','paket_panitia');
        });
        Route::controller(App\Http\Controllers\Api\V1\NonLelController::class)->group(function(){
            Route::get('InisiasiLelang/{pkt_id}','initiate_lelang')->middleware('jwt.verify');
            Route::get('lelang/{user_id}','list_lelangKIPBJ');
            Route::get('dokumen/{lls_id}','get_dokumen');
        });
        Route::controller(\App\Http\Controllers\Api\V1\EvaluasiController::class)->group(function(){
            Route::get('detail_lelang/{lls_id}','get_lelang');
        });
//        Route::get('InisiasiLelang/{pkt_id}',[\App\Http\Controllers\Api\V1\NonLelController::class,'initiate_lelang'])->middleware('jwt.verify');
    });

    Route::prefix('RKN')->group(function () {
        Route::controller(App\Http\Controllers\Api\V1\ProfileController::class)->group(function () {
            Route::post('ijin/{rkn_id}', 'ius_store')->middleware('jwt.verify');
            Route::get('ijin/{ius_id}', 'ius');
            Route::put('ijin/{ius_id}', 'ius_update')->middleware('jwt.verify');
            Route::get('list_ijin/{rkn_id}', 'ius_list');

            Route::post('akta/{rkn_id}', 'lhk_store')->middleware('jwt.verify');
            Route::get('akta/{lhk_id}', 'lhk');
            Route::put('akta/{lhk_id}', 'lhk_update')->middleware('jwt.verify');
            Route::get('list_akta/{rkn_id}', 'lhk_list');

            Route::post('manajer/{rkn_id}', 'mnj_store')->middleware('jwt.verify');
            Route::put('manajer/{id_mnj}', 'mnj_update')->middleware('jwt.verify');
            Route::get('manajer/{id_mnj}', 'mnj');
            Route::get('list_manajer/{rkn_id}', 'mnj_list');

            Route::post('pajak/{rkn_id}', 'pjk_store')->middleware('jwt.verify');
            Route::put('pajak/{pjk_id}', 'pjk_update')->middleware('jwt.verify');
            Route::get('pajak/{pjk_id}', 'pjk');
            Route::get('list_pajak/{rkn_id}', 'pjk_list');

            Route::post('pengalaman/{rkn_id}', 'pgl_store')->middleware('jwt.verify');
            Route::put('pengalaman/{pen_id}', 'pgl_update')->middleware('jwt.verify');
            Route::get('pengalaman/{pen_id}', 'pgl');
            Route::get('list_pengalaman/{rkn_id}', 'pgl_list');

            Route::post('peralatan/{rkn_id}', 'prl_store')->middleware('jwt.verify');
            Route::put('peralatan/{id_prl}', 'prl_update')->middleware('jwt.verify');
            Route::get('peralatan/{id_prl}', 'prl');
            Route::get('list_peralatan/{rkn_id}', 'prl_list');

            Route::post('staf/{rkn_id}', 'sta_store')->middleware('jwt.verify');
            Route::put('staf/{stp_id}', 'sta_update')->middleware('jwt.verify');
            Route::get('staf/{stp_id}', 'sta');
            Route::get('list_staf/{rkn_id}', 'sta_list');
        });
        Route::controller(App\Http\Controllers\Api\V1\RekananController::class)->group(function () {
            Route::get('penyedia/{rkn_id}', 'rekanan');
            Route::put('penyedia/{rkn_id}', 'store_update')->middleware('jwt.verify');
        });
        Route::controller(\App\Http\Controllers\Api\V1\NonLelController::class)->group(function(){
            Route::get('paket_baru/{user_id}','list_paketInbound');
            Route::get('paket/{lls_id}','get_lelang_penyedia');
            Route::get('lelang/{user_id}','list_lelang');            
//            Route::get('dokumen/{lls_id}','get_dokumen');
        });
        Route::get('dokumen/{lls_id}',[App\Http\Controllers\Api\V1\NonLelController::class,'get_dokumen']);
        Route::controller(App\Http\Controllers\Api\V1\PenawaranController::class)->group(function(){            
            Route::get('ikut_lelang/{lls_id}','init_penawaran')->middleware('jwt.verify');
            Route::get('penawaran/{lls_id}','get_gnrl_penawaran')->middleware('jwt.verify');
            Route::get('kualifikasi/{lls_id}','get_kualifikasi')->middleware('jwt.verify');
            Route::put('kualifikasi/{lls_id}','ins_kualifikasi')->middleware('jwt.verify');
            Route::get('template_harga/{lls_id}','extract_hps')->middleware('jwt.verify');
            Route::get('penawaran_kirim/{lls_id}','get_penawaran')->middleware('jwt.verify');
            Route::put('penawaran_kirim/{lls_id}','ins_penawaran')->middleware('jwt.verify');
        });
    });

    Route::prefix('PPK')->group(function () {
        Route::controller(\App\Http\Controllers\Api\V1\AnggaranController::class)->group(function(){
            Route::post('anggaran','add_anggaran')->middleware('jwt.verify');
        });
        Route::controller(App\Http\Controllers\Api\V1\PaketController::class)->group(function () {
            Route::post('InisiasiPaket', 'inisiasi_paket')->middleware('jwt.verify');
            Route::get('list_paket/{user_id}', 'list_paketPPK');
            Route::get('paket/{pkt_id}', 'get_paket');
            Route::put('paket/{pkt_id}', 'update_paketPPK');
            Route::post('tambahangg/{pkt_id}','tambah_ang');
            Route::post('editangg/{pkt_id}','edit_ang');
        });
        Route::controller(App\Http\Controllers\Api\V1\NonLelController::class)->group(function(){
            Route::get('lelang/{user_id}','list_lelangPPK');
            Route::get('dokumen/{lls_id}','get_dokumen');
        });
        Route::controller(\App\Http\Controllers\Api\V1\EvaluasiController::class)->group(function(){
            Route::get('detail_lelang/{lls_id}','get_lelang');
        });
        Route::controller(\App\Http\Controllers\Api\V1\EkontrakController::class)->group(function(){
            Route::get('base_kontrak/{lls_id}','ekontrak');
            Route::get('init_sppbj/{lls_id}','init_sppbj');
            Route::post('create_sppbj/{lls_id}','insert_sppbj')->middleware('jwt.verify');
            Route::get('sppbj/{sppbj_id}','get_sppbj');
            Route::get('print_sppbj/{sppbj_id}','cetak_sppbj');
            Route::put('sppbj/{sppbj_id}','update_sppbj')->middleware('jwt.verify');
            Route::get('kontrak/{kontrak_id}','get_kontrak');
            Route::get('print_kontrak/{kontrak_id}','cetak_kontrak');
            Route::put('kontrak/{kontrak_id}','update_kontrak')->middleware('jwt.verify');
            Route::get('spk/{spk_id}','get_spk');
            Route::get('print_spk/{spk_id}','cetak_spk');
            Route::put('spk/{spk_id}','update_spk')->middleware('jwt.verify');
        });
        Route::controller(App\Http\Controllers\Api\V1\PenilaianController::class)->group(function(){
            Route::get('nilai/{lls_id}','penilaian');
            Route::get('detail_nilai/{lls_id}/{ktr_id}','detail_penilaian');
            Route::put('penilaian','ins_penilaian')->middleware('jwt.verify');
        });
    });

    Route::prefix('PP')->group(function () {
        Route::controller(\App\Http\Controllers\Api\V1\NonLelController::class)->group(function (){
            Route::get('list_paketDown/{user_id}','list_paketDown');
            Route::get('list_paketUp/{user_id}','list_paketUp');
            Route::get('list_lelangDown/{user_id}','list_lelangDown');
            Route::get('list_lelangUp/{user_id}','list_lelangUp');
            Route::get('lelang/{lls_id}','get_lelang')->middleware('jwt.verify');
            Route::put('lelang/{lls_id}','update_pp')->middleware('jwt.verify');
//            Route::get('dokumen/{lls_id}','get_dokumen');
        });
        Route::get('dokumen/{lls_id}',[App\Http\Controllers\Api\V1\NonLelController::class,'get_dokumen']);
        Route::controller(App\Http\Controllers\Api\V1\LelUtilityController::class)->group(function(){            
            Route::get('jadwal/{lls_id}','lelang_jadwal');
            Route::get('kualifikasi/{lls_id}','check_kual');
            Route::get('penawaran/{lls_id}','check_pen');
            Route::get('penyedia/{lls_id}','get_penyedia');
            Route::get('tambah_peserta/{lls_id}','tambah_peserta')->middleware('jwt.verify');
            Route::put('chk_kualifikasi/{dll_id}','update_chk_kual');
            Route::put('chk_penawaran/{dll_id}','update_chk_pen');
        });
        Route::controller(\App\Http\Controllers\Api\V1\EvaluasiController::class)->group(function(){
            Route::get('eval_lelang/{lls_id}','get_lelang');
            Route::get('pen_kual/{psr_id}','get_kualifikasi');
            Route::get('pen_penawaran/{psr_id}','get_penawaran');
            Route::get('dok_eval/{psr_id}','base_eval');
            Route::put('do_evaluasi/{psr_id}','do_eval')->middleware('jwt.verify');
        });
        Route::controller(\App\Http\Controllers\Api\V1\VerifController::class)->group(function(){
            Route::get('verifikasi/{psr_id}','VerifData');
            Route::get('do_verif/{peg_id}','DoVerifikasi');
        });
        Route::controller(\App\Http\Controllers\Api\V1\BerAcaraController::class)->group(function(){
            Route::put('berita/{lls_id}','createBerita')->middleware('jwt.verify');
            Route::get('berita/{brc_id}','cetak');
        });
    });
});
