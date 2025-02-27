<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function list(Request $request)
    {
        $kantins = User::where('enable', 1)->where('type', 2)->get();

        return response()->json($kantins);
    }

    public function add(Request $request)
    {
        $email = $request->email;
        $email_exist = User::where('email', $email)->exists();
        if(!$email_exist) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->type = 2;
            $user->password = Hash::make($request->password);
            $user->auth_key = random_int(6, 6) . time();
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json(['error' => 'Email sudah terdaftar!'], 500);
        }
    }

    public function edit(Request $request)
    {
        // $email = $request->email;
        // $auth_key = $request->auth_key;
        $user = User::find($request->kantin_id);

        if($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->type = 2;
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json(['error' => 'Kantin tidak ditemukan!'], 500);
        }
    }

    public function delete(Request $request)
    {
        // $email = $request->email;
        // $auth_key = $request->auth_key;
        $user = User::find($request->kantin_id);

        if($user) {
            $user->enable = false;
            $user->type = 2;
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json(['error' => 'Kantin tidak ditemukan!'], 500);
        }
    }
}
