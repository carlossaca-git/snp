<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Notifications\NuevoUsuarioCreado;

class UserController extends Controller
{
    /**
     * Listado de Usuarios
     */
    public function index()
    {
        $usuarios = User::with('roles')->orderBy('id_usuario', 'desc')->paginate(10);

        // 3. Vista actualizada: carpeta 'administracion/usuarios'
        return view('administracion.usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario de Creación
     */
    public function create()
    {
        $roles = Rol::all();
        return view('administracion.usuarios.crear', compact('roles'));
    }

    /**
     * Guardar Usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'identificacion'     => 'required|unique:seg_usuario,identificacion',
            'nombres'            => 'required|string',
            'apellidos'          => 'required|string',
            'usuario'            => 'required|string|unique:seg_usuario,usuario',
            'correo_electronico' => 'required|email|unique:seg_usuario,correo_electronico|confirmed',
            'id_rol'             => 'required|exists:seg_rol,id_rol',
            'password'           => 'required|confirmed|min:8',
        ]);

        try {
            DB::beginTransaction();

            // Crear Usuario
            $user = User::create([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'usuario'            => $request->usuario,
                'correo_electronico' => $request->correo_electronico,
                'password'           => Hash::make($request->password),
            ]);

            // Asignar Rol
            $user->roles()->attach($request->id_rol);

            DB::commit();

            // Notificación
            try {
                $user->notify(new NuevoUsuarioCreado($request->password, $user));
            } catch (\Exception $e) {
                // Si falla el correo, no revertimos la creación del usuario, solo avisamos
                // Opcional: Log::error('Fallo correo: ' . $e->getMessage());
            }

            // 4. Redirección actualizada: 'administracion.usuarios.index'
            return redirect()->route('administracion.usuarios.index')
                ->with('status', 'Usuario creado y notificación enviada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Formulario de Edición
     * Nota: Usamos $usuario en lugar de $user porque en routes definimos parameters(['usuarios' => 'usuario'])
     */
    public function edit(User $usuario)
    {
        $roles = Rol::all();
        $rolActual = $usuario->roles->first()?->id_rol;

        // Pasamos la variable compactada como 'usuario' para que coincida con la ruta
        return view('administracion.usuarios.editar', compact('usuario', 'roles', 'rolActual'));
    }

    /**
     * Actualizar Usuario
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'identificacion'     => ['required', Rule::unique('seg_usuario')->ignore($usuario->id_usuario, 'id_usuario')],
            'nombres'            => 'required|string|max:255',
            'apellidos'          => 'required|string|max:255',
            'correo_electronico' => ['required', 'email', Rule::unique('seg_usuario')->ignore($usuario->id_usuario, 'id_usuario')],
            'id_rol'             => 'required|exists:seg_rol,id_rol',
            'password'           => 'nullable|confirmed|min:8',
        ]);

        try {
            DB::beginTransaction();

            $usuario->update([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'correo_electronico' => $request->correo_electronico,
            ]);

            if ($request->filled('password')) {
                $usuario->update(['password' => Hash::make($request->password)]);
            }

            $usuario->roles()->sync([$request->id_rol]);

            DB::commit();

            return redirect()->route('administracion.usuarios.index')
                ->with('status', 'Usuario actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Fallo al actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar Usuario
     */
    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id_usuario) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta administrativa.']);
        }

        try {
            DB::beginTransaction();

            $usuario->roles()->detach();
            $usuario->delete();

            DB::commit();

            return redirect()->route('administracion.usuarios.index')
                ->with('status', 'Usuario eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }
}
