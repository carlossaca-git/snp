<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol; // Asegúrate de importar tu modelo de Roles
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Notifications\NuevoUsuarioCreado;

class UserController extends Controller
{
    public function edit(User $user)
    {
        // Cargamos los roles para el select
        $roles = Rol::all();

        // Obtenemos el ID del rol actual del usuario desde la tabla pivote
        $rolActual = $user->roles->first()?->id_rol;

        return view('admin.users.editar', compact('user', 'roles', 'rolActual'));
    }

    /**
     * PROCESA LA ACTUALIZACION DE LOS DATOS
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'identificacion'     => ['required', Rule::unique('seg_usuario')->ignore($user->id_usuario, 'id_usuario')],
            'nombres'            => 'required|string|max:255',
            'apellidos'          => 'required|string|max:255',
            'correo_electronico' => ['required', 'email', Rule::unique('seg_usuario')->ignore($user->id_usuario, 'id_usuario')],
            'id_rol'             => 'required|exists:seg_rol,id_rol',
            // La contraseña es opcional al editar
            'password'           => 'nullable|confirmed|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar datos básicos
            $user->update([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'correo_electronico' => $request->correo_electronico,
            ]);

            // 2. Si el admin escribió una nueva contraseña, la actualizamos
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // 3. Sincronizar el rol (sync borra el anterior y pone el nuevo)
            $user->roles()->sync([$request->id_rol]);

            DB::commit();
            return redirect()->route('admin.users.index')->with('status', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Fallo al actualizar: ' . $e->getMessage()]);
        }
    }
    public function index()
    {
        // Traemos todos los roles de tu tabla para llenar el select
        $roles = Rol::all();
        //dd($roles);
        $usuarios = User::with('roles')->orderBy('id_usuario', 'desc')->paginate(10);

        return view('admin.users.index', compact('usuarios'));
    }

    public function create()
    {
        // Traemos los roles para el select del formulario
        $roles = Rol::all();

        return view('admin.users.crear', compact('roles'));
    }

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

            // 1. Crear el usuario en seg_usuario
            $user = User::create([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'usuario'            => $request->usuario,
                'correo_electronico' => $request->correo_electronico,
                'password'           => Hash::make($request->password),
            ]);

            // 2. Asignar el rol en la tabla pivote seg_usuario_perfil
            $user->roles()->attach($request->id_rol);

            DB::commit();

            // Redirigimos al listado con un mensaje de éxito
            // NOTIFICACIÓN: Enviamos el correo al nuevo usuario
            // Pasamos la contraseña original (sin encriptar) que viene del request
            $user->notify(new NuevoUsuarioCreado($request->password, $user));

            return redirect()->route('admin.users.index')
                ->with('status', '¡Usuario creado y notificacion enviada');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
    // app/Http/Controllers/Admin/UserController.php

    public function destroy(User $user)
    {
        // Evitar que el administrador se borre a sí mismo
        if (auth()->id() === $user->id_usuario) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta administrativa.']);
        }

        try {
            DB::beginTransaction();

            // 1. Eliminamos la relación en la tabla pivote (seg_usuario_perfil)
            // El método detach() borra las entradas en la intermedia automáticamente
            $user->roles()->detach();

            // 2. Eliminamos al usuario de la tabla seg_usuario
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')->with('status', 'Usuario eliminado correctamente del sistema.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }
}
