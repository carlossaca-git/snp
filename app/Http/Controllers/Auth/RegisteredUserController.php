<?php

namespace App\Http\Auth\Controllers;

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller{
    /**
     * Display the registration view.
     */
    public function create(): View{
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {

        $request->validate([
            'usuario' => 'required|string|max:255|unique:seg_usuario,usuario',
            'correo_electronico' => 'required|email|unique:seg_usuario,correo_electronico',
            'password' => 'required|min:8|confirmed',
            'identificacion' => 'required|string|unique:seg_usuario,identificacion',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'id_rol' => 'required|exists:seg_rol,id_rol',
        ]);
        try {
            $user = User::create([
                'usuario'            => $request->usuario,
                'correo_electronico' => $request->correo_electronico,
                'password'           => Hash::make($request->password),
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
            ]);

            event(new Registered($user));

            $user->roles()->attach($request->id_rol);

            //Auth::login($user);

            //return redirect(route('dashboard', absolute: false));
            return back()->with('success', 'Registro completado con éxito.');
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e->getMessage());
        }
    }
}
