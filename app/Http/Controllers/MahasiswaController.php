<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}
