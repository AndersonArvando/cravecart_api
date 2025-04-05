<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class KantinController extends Controller
{
    //
    public function list(Request $request)
    {
        $makanans = Makanan::get();

        return response()->json($makanans);
    }

    public function add(Request $request)
    {
        $makanan = new Makanan();
        $makanan->kantin_id = User::where('auth_key', $request->auth_key)->first()->id;
        $makanan->name = $request->name;
        $makanan->price = $request->price;
        $makanan->description = $request->description;
        $makanan->is_ready = 1;
        $makanan->enabled = 1;
        $makanan->save();

    }

    public function edit(Request $request)
    {
        $makanan = Makanan::find($request->makanan_id);

        if($makanan) {
            
            $makanan->name = $request->name;
            $makanan->price = $request->price;
            $makanan->description = $request->description;
            $makanan->is_ready = $request->is_ready;
            $makanan->enabled = $request->enabled;
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
        if($request->file('image')) {
            $file_name = $user->id . '_' . time() . '.' . $request->image->extension();
            // var_dump($file_name);die;
            $request->image->move(public_path('/kantin'), $file_name);
        }
        $user->image = 'kantin/' . $file_name;
        $user->save();

        return response()->json(['message' => 'Profil berhasil disimpan!']);
    }

    public function listPesanan(Request $request)
    {
        $pesanans = Transaksi::with(['mahasiswa', 'kantin', 'detail', 'detail.makanan'])->where('status', '<>', 'selesai')->orderBy('created_at', 'desc')->get();

        return response()->json(['pesanans' => $pesanans]);
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
        $pesanan->Save();

        return response()->json(['message' => 'Status pesanan telah diubah!']);
    }

    public function laporPengguna(Request $request)
    {
        
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
