<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Institucional\OrganizacionEstatal;
use App\Models\Inversion\ProyectoInversion;
use App\Models\Seguridad\User;
use App\Models\Seguridad\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Listado de Usuarios
     */
    public function index(Request $request)
    {
        //  Capturamos el texto del buscador
        $busqueda = $request->input('busqueda');

        //  Iniciamos la consulta con relaciones
        $query = User::with(['roles', 'organizacion']);

        //  Aplicamos el filtro si existe búsqueda
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombres', 'LIKE', "%{$busqueda}%")
                    ->orWhere('apellidos', 'LIKE', "%{$busqueda}%")
                    ->orWhere('usuario', 'LIKE', "%{$busqueda}%")
                    ->orWhere('identificacion', 'LIKE', "%{$busqueda}%")
                    ->orWhere('correo_electronico', 'LIKE', "%{$busqueda}%")
                    // Búsqueda avanzada: Buscar también por nombre de la Organización
                    ->orWhereHas('organizacion', function ($subQ) use ($busqueda) {
                        $subQ->where('nom_organizacion', 'LIKE', "%{$busqueda}%");
                    });
            });
        }
        $usuarios = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['busqueda' => $busqueda]);

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
     * Perfil de usuario
     */
    public function show()
    {
        // Obtenemos al usuario logueado
        /** @var \App\Models\Seguridad\User $user */
        $user = Auth::user();
        $user->load('organizacion');
        $proyectos = ProyectoInversion::where('id_usuario_creacion', $user->id)
            ->with('objetivo')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.admin.usuarios.show', compact('user', 'proyectos'));
        //return view('dashboard.admin.usuarios.show', compact('user'));
    }
    /**
     * Guardar Usuario
     */
    public function store(Request $request)
    {
    //  obtenemos el usuario actual
        /** @var \App\Models\Seguridad\User $currentUser */
        $currentUser = Auth::user();

        // Definimos reglas base
        $rules = [
            'identificacion'     => 'required|digits:10|unique:seg_usuario,identificacion',
            'nombres'            => 'required|string|max:100',
            'apellidos'          => 'required|string|max:100',
            'usuario'            => 'required|string|unique:seg_usuario,usuario',
            'correo_electronico' => 'required|email|unique:seg_usuario,correo_electronico',
            'password'           => 'required|confirmed|min:8',
            'roles'              => 'required|array',
            'roles.*'            => 'exists:seg_rol,id_rol',
        ];
        // Si es Super Admin, exigimos que envíe una org válida.
        // Si es local, no lo validamos aquí porque lo vamos a forzar después (o validamos que coincida).
        if ($currentUser->tieneRol('SUPER_ADMIN')) {
            $rules['id_organizacion'] = 'required|exists:cat_organizacion_estatal,id_organizacion';
        }

        $validatedData = $request->validate($rules);

        try {
            DB::beginTransaction();


            // Determinamos el ID de Organización final
            if ($currentUser->tieneRol('SUPER_ADMIN') || $currentUser->tieneRol('ADMIN_TI')) {
                // Si es Super Admin, confiamos en lo que eligió en el select
                $finalOrgId = $request->id_organizacion;
            } else {
                // Si es un Admin Local, IGNORAMOS lo que venga en el request
                // y forzamos su propio ID. Esto evita ataques de inyección de ID.
                $finalOrgId = $currentUser->id_organizacion;
            }


            // Evitar que un usuario local cree un "Super Admin"
            $rolesAAsignar = $request->roles;
            if (!$currentUser->tieneRol('SUPER_ADMIN')) {
                if (in_array(1, $rolesAAsignar)) {
                    // Opción A: Lanzar error y detener todo
                    throw new Exception('No tienes permiso para crear Super Administradores.');
                }
            }

            // CREAR EL USUARIO
            $user = User::create([
                'identificacion'     => $validatedData['identificacion'],
                'nombres'            => $validatedData['nombres'],
                'apellidos'          => $validatedData['apellidos'],
                'usuario'            => $validatedData['usuario'],
                'correo_electronico' => $validatedData['correo_electronico'],
                'password'           => Hash::make($validatedData['password']),
                'id_organizacion'    => $finalOrgId,
                'email_verified_at'  => $request->has('verificado') ? now() : null,
                'estado'             => 1,
            ]);

            // ASIGNAR ROLES
            $user->roles()->attach($rolesAAsignar);

            DB::commit();

            // NOTIFICACIÓN
            try {
                $user->notify(new \App\Notifications\NuevoUsuarioCreado($request->password, $user));
            } catch (Exception $e) {
                Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
            }

            return redirect()->route('administracion.usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creando usuario: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Ocurrió un error al guardar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Formulario de Edición
     *
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
            'id_rol'             => 'required|exists:seg_rol,id_rol',
            'id_organizacion'    => 'required|exists:cat_organizacion_estatal,id_organizacion',
            'estado'             => 'required|in:1,0',
            'password'           => 'nullable|confirmed|min:8',
        ]);
        DB::beginTransaction();
        try {

            $fechaVerificacion = $request->has('verificado')
                ? ($usuario->email_verified_at ?? now())
                : null;

            //  ACTUALIZAR DATOS BÁSICOS
            $usuario->update([
                'identificacion'     => $request->identificacion,
                'nombres'            => $request->nombres,
                'apellidos'          => $request->apellidos,
                'correo_electronico' => $request->correo_electronico,
                'id_organizacion'    => $request->id_organizacion,
                'estado'             => $request->estado,
                'email_verified_at'  => $fechaVerificacion,
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

        } catch (Exception $e) {
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
