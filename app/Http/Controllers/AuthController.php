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
        $user = User::where(DB::raw('md5(id)'), $id)->first();
        if ($user->role == 3) {
            $items = Vidoes::where('to', 3)->get();
        } else {
            $items = Vidoes::where('user', $id)->get();
        }

        return view('auth.video',compact('items'));
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
