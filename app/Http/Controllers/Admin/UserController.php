<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Seguridad\User;
use App\Models\Seguridad\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Notifications\NuevoUsuarioCreado;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Listado de Usuarios
     */
    public function index()
    {
        $usuarios = User::with(['roles','organizacion'])->orderBy('id_usuario', 'desc')->paginate(10);

        return view('dashboard.admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario de Creación
     */
    public function create()
    {
        $roles = Rol::all();
        $organizaciones = OrganizacionEstatal::all();
        return view('dashboard.admin.usuarios.crear', compact('roles', 'organizaciones'));
    }

    /**
     * Guardar Usuario
     */
    public function store(Request $request)
    {
        // dd($request->all());

        // 1. VALIDACIÓN ROBUSTA
        $request->validate([
            'identificacion'     => 'required|digits:10|unique:seg_usuario,identificacion',
            'nombres'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'usuario'            => 'required|string|unique:seg_usuario,usuario',
            'correo_electronico' => 'required|email|unique:seg_usuario,correo_electronico',
            'password'           => 'required|confirmed|min:8',
            'roles'              => 'required|array',
            'roles.*'            => 'exists:seg_rol,id_rol',
            'id_organizacion'    => 'required|exists:cat_organizacion_estatal,id_organizacion',
        ]);

        try {
            DB::beginTransaction();

            // 2. CREAR EL USUARIO
            $user = User::create([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'usuario'            => $request->usuario,
                'correo_electronico' => $request->correo_electronico,
                'password'           => Hash::make($request->password),
                'id_organizacion'    => $request->id_organizacion,

                // LÓGICA DE VERIFICACIÓN MANUAL
                // se guarda la fecha actual. Si no, queda NULL.
                'email_verified_at'  => $request->has('verificado') ? now() : null,
                'estado'             => 1,
            ]);

            // 3. ASIGNAR ROLES
            // Usamos $request->roles porque así se llama en la validación y en el formulario HTML
            $user->roles()->attach($request->roles);

            DB::commit();

            // 4. NOTIFICACIÓN (Opcional)
            try {
                //  importar la clase: use App\Notifications\NuevoUsuarioCreado;
                $user->notify(new \App\Notifications\NuevoUsuarioCreado($request->password, $user));
            } catch (\Exception $e) {
                Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
                // No hacemos rollback porque el usuario SÍ se creó correctamente
            }

            return redirect()->route('administracion.usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log para que tú veas el error real en storage/logs/laravel.log
            Log::error('Error creando usuario: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Ocurrió un error al guardar: ' . $e->getMessage()])
                ->withInput();
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
        $organizaciones = OrganizacionEstatal::all();

        // Pasamos la variable compactada como 'usuario' para que coincida con la ruta
        return view('dashboard.admin.usuarios.editar', compact('usuario', 'roles', 'rolActual', 'organizaciones'));
    }

    /**
     * Actualizar Usuario
     */
    public function update(Request $request, User $usuario)
    {
        //  VALIDACIÓN
        $request->validate([
            'identificacion'     => ['required', 'digits:10', Rule::unique('seg_usuario')->ignore($usuario->id_usuario, 'id_usuario')],
            'nombres'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'correo_electronico' => ['required', 'email', Rule::unique('seg_usuario')->ignore($usuario->id_usuario, 'id_usuario')],

            // Campos de Negocio
            'id_rol'             => 'required|exists:seg_rol,id_rol',
            'id_organizacion'    => 'required|exists:cat_organizacion_estatal,id_organizacion',
            'estado'             => 'required|in:1,0',

            // Password opcional
            'password'           => 'nullable|confirmed|min:8',
        ]);

        try {
            DB::beginTransaction();

            $fechaVerificacion = $request->has('verificado')
                ? ($usuario->email_verified_at ?? now())
                : null;

            //  ACTUALIZAR DATOS BÁSICOS
            $usuario->update([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'correo_electronico' => $request->correo_electronico,
                'id_organizacion'    => $request->id_organizacion, // Actualizar entidad
                'estado'             => $request->estado,          // Actualizar estado
                'email_verified_at'  => $fechaVerificacion,        // Actualizar verificación
            ]);

            //  ACTUALIZAR CONTRASEÑA (SOLO SI SE ESCRIBIÓ)
            if ($request->filled('password')) {
                $usuario->update(['password' => Hash::make($request->password)]);
            }

            //  ACTUALIZAR ROL
            $usuario->roles()->sync([$request->id_rol]);

            DB::commit();

            return redirect()->route('administracion.usuarios.index')
                ->with('success', 'Usuario actualizado correctamente.'); // Usa 'success' si tu layout usa esa variable

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Fallo al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar Usuario
     */
    public function destroy(User $usuario)
    {
        if (Auth::user() && Auth::user()->id_usuario === $usuario->id_usuario) {
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
