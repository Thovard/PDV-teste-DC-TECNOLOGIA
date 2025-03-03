<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $messages = [
            'email.required'    => 'O campo email é obrigatório.',
            'email.email'       => 'O campo email deve ser um endereço de email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve ter pelo menos 6 caracteres.',
        ];

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ], $messages);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->filled('remember'))) {
            return redirect()->route('dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors(['email' => 'Credenciais inválidas'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Você saiu da sua conta.');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $messages = [
            'name.required'      => 'O campo nome é obrigatório.',
            'email.required'     => 'O campo email é obrigatório.',
            'email.email'        => 'O campo email deve ser um endereço de email válido.',
            'email.unique'       => 'Este email já está em uso.',
            'password.required'  => 'O campo senha é obrigatório.',
            'password.min'       => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ];

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], $messages);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }
}
