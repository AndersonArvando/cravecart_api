<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $email = $request->email;
        $email_exist = User::where('email', $email)->exists();
        if(!$email_exist) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->npm = $request->npm;
            $user->dob = $request->dob;
            $user->password = Hash::make($request->password);
            $user->auth_key = random_int(6, 6) . time();
            $user->save();

            return response()->json([], 200);
        } else {
            return response()->json(['error' => 'Email sudah terdaftar!'], 500);
        }
    }

    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();
        if($user) {
            $password_check = Hash::check($password, $user->password);
            if($password_check) {
                $user_otp = UserOTP::where("email", $email)->first();
                if (!$user_otp) {
                    $user_otp = new UserOTP;
                    $user_otp->email = $email;
                    $user_otp->otp_code = generateVerificationCode();
                    $user_otp->otp_date = date("Y-m-d H:i:s");
                } else {
                    $user_otp->otp_code = generateVerificationCode();
                    $user_otp->otp_date = date("Y-m-d H:i:s");
                }
                $user_otp->save();

                $receiver_email = $user_otp->email;
                // var_dump($user_otp);die;
                $subject = "[CraveCart] Login OTP Code";
                sendMail("emails.login_otp", compact('user_otp'), $user_otp->email, $subject, []);
                
                return response()->json(['email' => $user->email], 200);
            }
            return response()->json(['error' => 'Email atau Password salah!'], 500);
        }
        return response()->json(['error' => 'Email atau Password salah!'], 500);
    }

    public function login_otp(Request $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        $user = User::where('email', $email)->first();
        if($user) {
            $user_otp = UserOTP::where('email', $user->email)->first();
            if($user_otp) {
                if($user_otp->otp_code == $otp) {
                    $user_otp->delete();
                    return response()->json(['user' => $user], 200);
                } else {
                    return response()->json(['error' => 'OTP salah!'], 500);
                }
            } else {
                return response()->json(['error' => 'OTP tidak ditemukan!'], 500);
            }
        } else {
            return response()->json(['error' => 'Data tidak ditemukan!'], 500);
        }
    }
}
