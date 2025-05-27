<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Makanan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KantinController extends Controller
{
    //
    public function list(Request $request)
    {
        $makanans = Makanan::get()->map(function ($item) {
            $item->price = intval($item->price);

            return $item;
        });

        return response()->json($makanans);
    }

    public function add(Request $request)
    {
        $makanan = new Makanan();
        $makanan->kantin_id = User::where('auth_key', $request->auth_key)->first()->id;
        $makanan->name = $request->name;
        $makanan->price = $request->price;
        $makanan->description = $request->desc;
        $makanan->is_ready = 1;
        $makanan->enabled = 1;
        if($request->file('image')) {
            $file_name = User::where('auth_key', $request->auth_key)->first()->id . '_' . time() . '.' . $request->image->extension();
            // var_dump($file_name);die;
            $request->image->move(public_path('/kantin'), $file_name);
        }
        $makanan->image = 'kantin/' . $file_name;
        $makanan->save();

    }

    public function edit(Request $request)
    {
        $makanan = Makanan::find($request->makanan_id);

        if($makanan) {
            
            $makanan->name = $request->name;
            $makanan->price = $request->price;
            $makanan->description = $request->desc;
            $makanan->is_ready = $request->is_ready ?? $makanan->is_ready;
            $makanan->enabled = 1;
            if($request->file('image')) {
                $file_name = User::where('auth_key', $request->auth_key)->first()->id . '_' . time() . '.' . $request->image->extension();
                // var_dump($file_name);die;
                $request->image->move(public_path('/kantin'), $file_name);
                $makanan->image = 'kantin/' . $file_name;
            }
            $makanan->save();

            return response()->json([], 200);
        } else {
            return response()->json(['error' => 'Makanan tidak ditemukan!'], 500);
        }
    }

    public function getProfil(Request $request)
    {
        $auth_key = $request->auth_key;
        $user = User::where('auth_key', $auth_key)->first();

        return response()->json($user);
    }

    public function editProfil(Request $request)
    {
        $auth_key = $request->auth_key;
        $user = User::where('auth_key', $auth_key)->first();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->description = $request->description;
        $user->open_at = date("Y-m-d H:i:s", strtotime($request->open_at));
        $user->close_at = date("Y-m-d H:i:s", strtotime($request->close_at));
        if($request->file('image')) {
            $file_name = $user->id . '_' . time() . '.' . $request->image->extension();
            // var_dump($file_name);die;
            $request->image->move(public_path('/kantin'), $file_name);
        }
        if($request->file('qris_path')) {
            $qris_file_name = $user->id . '_qris' . time() . '.' . $request->qris_path->extension();
            // var_dump($qris_file_name);die;
            $request->qris_path->move(public_path('/kantin'), $qris_file_name);
            $user->qris_path = 'kantin/' . $qris_file_name;
        }
        $user->image = 'kantin/' . $file_name;
        Log::info($user);
        $user->save();

        return response()->json(['message' => 'Profil berhasil disimpan!']);
    }

    public function listPesanan(Request $request)
    {
        $auth_key = $request->auth_key;
        $user = User::where('auth_key', $auth_key)->first();

        $pesanans = Transaksi::with(['mahasiswa', 'kantin', 'detail', 'detail.makanan'])->where('kantin_id', $user->id)->where('status', '<>', 'selesai')->where('status', '<>', 'tolak')->orderBy('created_at', 'desc')->get();
        $total_menu = Makanan::where('kantin_id', $user->id)->count();
        $transaksi = Transaksi::whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime('today')), date('Y-m-d 23:59:59', strtotime('today'))])->count();
        $menu_habis = Makanan::where('kantin_id', $user->id)->where('is_ready', 0)->count();
        $pemasukan = Transaksi::whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime('today')), date('Y-m-d 23:59:59', strtotime('today'))])->selectRaw('sum(total) as pemasukan')->first();
        return response()->json(['pesanans' => $pesanans, 'total_menu' => $total_menu, 'transaksi' => $transaksi, 'menu_habis' => $menu_habis, 'pemasukan' => $pemasukan]);
    }

    public function listRiwayat(Request $request)
    {
        $auth_key = $request->auth_key;
        $user = User::where('auth_key', $auth_key)->first();

        $pesanans = Transaksi::with(['mahasiswa', 'kantin', 'detail', 'detail.makanan'])->where('kantin_id', $user->id)->where(function ($query) {
            $query->where('status', 'selesai')->orWhere('status', 'tolak');
        })->orderBy('created_at', 'desc')->get();

        return response()->json(['riwayats' => $pesanans]);
    }

    public function tolakPesanan(Request $request)
    {
        $pesanan = Transaksi::find($request->pesanan_id);
        $pesanan->status = 'tolak';
        $pesanan->komentar = $request->komentar;
        $pesanan->Save();

        return response()->json(['message' => 'Pesanan telah ditolak!']);
    }

    public function updatePesanan(Request $request)
    {
        $pesanan = Transaksi::find($request->pesanan_id);
        $pesanan->status = $request->status;
        if($request->komentar) $pesanan->komentar = $request->komentar;
        $pesanan->Save();

        return response()->json(['message' => 'Status pesanan telah diubah!']);
    }

    public function laporPengguna(Request $request)
    {
        $laporan = new Laporan();
        $laporan->transaksi_id = $request->transaksi_id;
        $laporan->komentar = $request->komentar;
        $laporan->Save();
    }

    // public function delete(Request $request)
    // {
    //     // $email = $request->email;
    //     // $auth_key = $request->auth_key;
    //     $user = User::find($request->kantin_id);

    //     if($user) {
    //         $user->enable = false;
    //         $user->type = 2;
    //         $user->save();

    //         return response()->json([], 200);
    //     } else {
    //         return response()->json(['error' => 'Kantin tidak ditemukan!'], 500);
    //     }
    // }
}
