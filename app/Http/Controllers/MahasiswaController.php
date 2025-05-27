<?php

namespace App\Http\Controllers;

use App\Models\DraftMakanan;
use App\Models\Makanan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    //
    public function getProfil(Request $request)
    {
        $auth_key = $request->auth_key;
        $user = User::where('auth_key', $auth_key)->first();

        return response()->json($user);
    }

    public function getKantin(Request $request)
    {
        $kantins = User::where('enable', 1)->where('type', 2)->get();

        return response()->json($kantins);
    }

    public function getMakananKantin(Request $request)
    {
        $mahasiswa = User::where('auth_key', $request->auth_key)->first();
        $kantin = User::find($request->kantin_id);
        $makanans = Makanan::where('kantin_id', $request->kantin_id)->where('enabled', 1)->where('is_ready', 1)->get();
        $draft_makanans = DraftMakanan::where('kantin_id', $request->kantin_id)->where('mahasiswa_id', $mahasiswa->id)->get();

        return response()->json(['makanans' => $makanans, 'draft_makanans' => $draft_makanans, 'kantin' => $kantin]);
    }
    public function getMakananKantinRandomGet(Request $request)
    {
        $makanans = Makanan::where('enabled', 1)->where('is_ready', 1)->get();

        return response()->json(['makanans' => $makanans]);
    }

    public function getMakananKantinCatatan(Request $request)
    {
        $mahasiswa = User::where('auth_key', $request->auth_key)->first();
        $draft_makanans = DraftMakanan::where('makanan_id', $request->makanan_id)->where('mahasiswa_id', $mahasiswa->id)->get();
        if($request->isMethod('post')) {
            $draft_makanans[0]->catatan = $request->catatan;
            $draft_makanans[0]->save();
        }

        return response()->json(['draft_makanans' => $draft_makanans]);
    }

    public function saveDraftMakanan(Request $request)
    {
        Log::info($request->all());
        $mahasiswa = User::where('auth_key', $request->auth_key)->first();
        $makanan = Makanan::find($request->makanan_id);
        $draft_makanan = DraftMakanan::where('mahasiswa_id', $mahasiswa->id)->where('makanan_id', $makanan->id)->first();
        if($draft_makanan) {
            if($request->operator == '+') {
                $draft_makanan->qty += 1;
            } else if($request->operator == '-') {
                $draft_makanan->qty -= 1;
            }
            $draft_makanan->save();

            if($draft_makanan->qty == 0) $draft_makanan->delete();
        } else {
            $draft_makanan = new DraftMakanan();
            $draft_makanan->kantin_id = $makanan->kantin_id;
            $draft_makanan->mahasiswa_id = $mahasiswa->id;
            $draft_makanan->makanan_id = $makanan->id;
            $draft_makanan->qty = 1;
            $draft_makanan->save();
        }

        return response()->json(['message' => 'Draft berhasil disimpan!']);
    }

    public function checkout(Request $request)
    {
        $mahasiswa = User::where('auth_key', $request->auth_key)->first();
        $kantin = User::find($request->kantin_id);
        $draft_makanans = DraftMakanan::where('kantin_id', $request->kantin_id)->where('mahasiswa_id', $mahasiswa->id)->get();
        $latest_order = Transaksi::whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime('today')), date('Y-m-d 23:59:59', strtotime('today'))])->orderBy('created_at', 'desc')->first();

        if($latest_order){
            $lastest_number = substr($latest_order->order_no, -3);
            $lastest_number++;
            $next_no = str_pad($lastest_number, 3, '00', STR_PAD_LEFT);
        } else {
            $next_no = '001';
        }
        $new_no = date('Ymd') . $kantin->kantin_id . $next_no;

        $total = 0;
        $makanans = Makanan::whereIn('id', $draft_makanans->pluck('makanan_id')->toArray())->get();
        foreach($draft_makanans as $draft_makanan) {
            $total += Makanan::where('id', $draft_makanan->makanan_id)->first()->price * $draft_makanan->qty;
        }

        $transaksi = new Transaksi();
        $transaksi->order_no = $new_no;
        $transaksi->metode = $request->metode;
        $transaksi->total = $total;
        $transaksi->kantin_id = $request->kantin_id;
        $transaksi->mahasiswa_id = $mahasiswa->id;
        $transaksi->status = 'pending';
        if($request->file('payment_proof')) {
            $file_name = $mahasiswa->npm . '_' . $new_no . '.' . $request->payment_proof->extension();
            $request->payment_proof->move(public_path('/payment'), $file_name);
            $transaksi->file_path = 'payment/' . $file_name;
        }
        $transaksi->save();

        foreach ($draft_makanans as $draft_makanan) {
            $transaksi_detail = new TransaksiDetail();
            $transaksi_detail->transaksi_id = $transaksi->id;
            $transaksi_detail->makanan_id = $draft_makanan->makanan_id;
            $transaksi_detail->qty = $draft_makanan->qty;
            $transaksi_detail->catatan = $draft_makanan->catatan;
            $transaksi_detail->price = Makanan::where('id', $draft_makanan->makanan_id)->first()->price;
            $transaksi_detail->total = Makanan::where('id', $draft_makanan->makanan_id)->first()->price * $draft_makanan->qty;
            $transaksi_detail->save();
            $draft_makanan->delete();
        }

        $transaksi = Transaksi::with(['detail', 'detail.makanan'])->find($transaksi->id);

        return response()->json(['message' => 'Checkout Berhasil!', 'transaksi' => $transaksi]);
    }

    public function getTransaksi(Request $request)
    {
        $mahasiswa = User::where('auth_key', $request->auth_key)->first();
        $transaksi_proses = Transaksi::with('kantin')->where('mahasiswa_id', $mahasiswa->id)->where('status', '<>', 'selesai')->orderBy('created_at', 'desc')->get();
        $transaksi_selesai = Transaksi::with('kantin')->where('mahasiswa_id', $mahasiswa->id)->where('status', 'selesai')->orderBy('created_at', 'desc')->get();
        
        return response()->json(['transaksi_proses' => $transaksi_proses, 'transaksi_selesai' => $transaksi_selesai]);
    }

    public function getTransaksiDetail(Request $request)
    {
        $transaksi = Transaksi::with(['detail', 'kantin', 'mahasiswa', 'detail.makanan'])->find($request->transaksi_id);
        
        return response()->json($transaksi);
    }
}