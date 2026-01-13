<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vidoes;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function video($id)
    {
        $items = [];
        $user  = Vidoes::where(DB::raw('md5(teach_id)'), $id);
        if ($user->exists()) {
            $items = $user->get();
        }

        $to = Vidoes::where(DB::raw('md5(student_id)'), $id);
        if ($to->exists()) {
            $items = $to->get();
        }

        return view('auth.video', compact('items'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek apakah role adalah Admin (0) atau Operator (4)
            if (!in_array($user->role, [0, 4])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akses ditolak. Hanya Admin dan Operator yang dapat login.',
                ]);
            }

            if ($user->status != 1) {
                Auth::logout();

                // Hapus session dan CSRF token
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda tidak aktif.',
                ]);
            }
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
