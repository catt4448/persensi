<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman register
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Proses register mahasiswa
     */
    public function store(Request $request)
    {   
       
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // set role sebagai 'user'
        ]);
        

        // event(new Registered($user));

        // // ⛔ OPSI 1: langsung login
        // Auth::login($user);

        // ✅ redirect ke dashboard
       // return redirect('/user/dashboard');
    }
}
