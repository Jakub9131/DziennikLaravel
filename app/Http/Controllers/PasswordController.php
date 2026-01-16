<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], // Sprawdza czy obecne hasło jest poprawne
            'new_password' => ['required', 'min:8'],
        ], [
            'current_password.current_password' => 'Podane obecne hasło jest nieprawidłowe.',
            'new_password.min' => 'Nowe hasło musi mieć co najmniej 8 znaków.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Hasło zostało pomyślnie zmienione.');
    }
}
