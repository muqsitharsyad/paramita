<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $this->attachUnitKerjaToSession($request);

            return $this->redirectToRole();
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

    private function redirectToRole()
    {
        $user = Auth::user()->loadMissing('unitKerja:id,nama,kode');
        $homeRoute = RoleHelper::getUserHomeRoute($user);

        if ($homeRoute) {
            // Jika starts with '/', anggap URL, bukan route name
            if (str_starts_with($homeRoute, '/')) {
                return redirect()->to($homeRoute);
            }
            return redirect()->route($homeRoute);
        }

        // No role assigned
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Akun Anda belum memiliki role yang sesuai.',
        ]);
    }

    private function attachUnitKerjaToSession(Request $request): void
    {
        $unitKerja = Auth::user()?->loadMissing('unitKerja:id,nama,kode')->unitKerja;

        $request->session()->put('unit_kerja', $unitKerja ? [
            'id' => $unitKerja->id,
            'nama' => $unitKerja->nama,
            'kode' => $unitKerja->kode,
        ] : null);
    }
}
