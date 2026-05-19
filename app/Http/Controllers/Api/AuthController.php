<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\NumberWa;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SendWhatsAppJob;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $login    = $request->input('login');
        $password = $request->input('password');

        $user = User::where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (! $user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if ($user->status == 0) {
            return response()->json(['error' => 'Akun tidak aktif'], 403);
        }

        $credentials = ['name' => $user->name, 'password' => $password];

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token ' . $e->getMessage()], 500);
        }

        if ($request->fcm) {
            $user->fcm = $request->fcm;
            $user->save();
        }

        return response()->json([
            'token'      => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 1,
            'role'       => $user->role,
            'uid'        => md5($user->data->id),
            'id'         => $user->id,
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            $newToken = JWTAuth::refresh($token);

            return response()->json([
                'token'      => $newToken,
                'expires_in' => auth('api')->factory()->getTTL() * 1,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token refresh failed'], 401);
        }
    }

    public function forget(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'hp' => ['required', new NumberWa()],
            ], [
                'hp.required' => 'Nomor wajib diisi.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 400);
            }

            $user = User::where('nomor', $request->hp)->first();

            if (! $user) {
                return response()->json([
                    'errors' => ['hp' => 'Nomor tidak valid'],
                ], 400);
            }

            try {
                $pass = random_int(10000, 99999);

                $user->password = Hash::make($pass);
                $user->save();

                $to = '62' . substr($user->nomor, 1);

                $message = "Anda reset berhasil\nPassword akun anda: *{$pass}*";

                // 🚀 kirim ke queue (tidak blocking)
                SendWhatsAppJob::dispatch($to, $message);

                return response()->json([
                    'status' => true,
                    'message' => 'Password reset berhasil, cek WhatsApp Anda',
                ]);

            } catch (\Throwable $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
    }

    public function upass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old' => 'required',
            'new' => 'required',
        ], [
            'old.required' => 'Password lama Wajib diisi',
            'new.required' => 'Password baru Wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $id   = JWTAuth::user()->id;
        $data = User::where('id', $id)->first();

        if (! $data) {
            return response()->json(['errors' => ['message' => 'User tidak valid']], 400);
        }

        if (! Hash::check($request->old, $data->password)) {
            return response()->json(['errors' => ['message' => 'Password lama tidak valid']], 400);
        }

        $data->password = Hash::make($request->new);
        $data->save();

        return response()->json(['status' => true, "data" => $request->new], 200);
    }
}
