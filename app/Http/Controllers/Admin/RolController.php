<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Permisos;
use Exception;

class RolController extends Controller
{
    //METODO INDEX
    public function index()
    {

        // Obtenemos los roles con los permisos
        $roles = Rol::withCount('permisos')->orderBy('id_rol', 'asc')->get();
        return view('dashboard.admin.roles.index', compact('roles'));
    }
    //METODO EDIT
    public function edit($id)
    {
        $rol = Rol::with('permisos')->findOrFail($id);

        // Obtenemos todos los permisos
        $todosLosPermisos = Permisos::all();

        //  AGRUPAMOS por la columna 'modulo'
        $permisosAgrupados = Permisos::select('id_permiso', 'name', 'nombre_corto', 'modulo', 'descripcion')
            ->get()
            ->groupBy('modulo');

        // Obtenemos los IDs que este rol YA tiene asignados para marcarlos
        $permisosAsignados = $rol->permisos->pluck('id_permiso')->toArray();

        return view('dashboard.admin.roles.edit', compact('rol', 'permisosAgrupados', 'permisosAsignados'));
    }
    //METODO CREATE
    public function create()
    {

        $permisos = DB::table('seg_permiso')
            ->whereNull('deleted_at') // Respetamos el SoftDelete manualmente
            ->get();
        //Obtenemos todos los permisos agrupados
        $permisosAgrupados = Permisos::select('id_permiso', 'name', 'nombre_corto', 'modulo', 'descripcion')
            ->get()
            ->groupBy('modulo');
        //Retornamos a la vista
        $permisosAgrupados = $permisos->groupBy('modulo');

        return view('dashboard.admin.roles.create', compact('permisosAgrupados'));
    }
    //METODO STORE
    public function store(Request $request)
    {
        // Validar
        $request->validate([
            'nombre_corto' => 'required|string|max:50|unique:seg_rol,nombre_corto',
            'permisos' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            // Crear el Rol
            $rol = new Rol();
            $rol->nombre_corto = $request->nombre_corto;
            // Generamos nombre_corto automático "Jefe Financiero" "jefe-financiero"
            $rol->name = Str::slug($request->nombre_corto);
            $rol->descripcion = $request->descripcion;
            $rol->save();

            // Asignar Permisos (Llenar tabla pivote)
            $rol->permisos()->sync($request->permisos);

            DB::commit();

            return redirect()->route('administracion.roles.index')
                ->with('success', 'Rol creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el rol: ' . $e->getMessage())->withInput();
        }
    }
    //METODO UPDATE
    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        // Validamos nombre y array de permisos
        $request->validate([
            'nombre' => 'required|string|max:50|unique:seg_rol,nombre_corto,' . $id . ',id_rol',
            'permisos' => 'array',
            'current_password' => 'required'
        ]);

        //Verificacion de seguridad
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            // Si falla devolvemos al usuario atrás con un error en ese campo
            return back()
                ->withErrors(['current_password' => 'La contraseña es incorrecta. No se guardaron los cambios.'])
                ->withInput();
        }
        DB::beginTransaction();
        try {
            //Si el usuario desbloquea el candado y cambia el nombre actualizamos
            if ($rol->nombre_corto !== $request->nombre) {
                $rol->nombre_corto = $request->nombre;
                $rol->slug = Str::slug($request->nombre_corto);
                $rol->save();
            }
            // sincronizamos la tabla intermedia
            // borra lo que habia y deja solo lo nuevo
            $rol->permisos()->sync($request->permisos);
            DB::commit();
            return redirect()->route('administracion.roles.index')->with('success', 'Permisos actualizados correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            dd($request->all());
            return redirect()->route('administracion.roles.index')
                ->with('error', 'No se pudo guardar los permisos. Detalles: ' . $e->getMessage());
        }
    }
    //METODO DESTROY
    public function destroy(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        // 1. Verificar seguridad (Igual que en update)
        // Nota: Como viene de un modal, recibimos 'current_password'
        $request->validate(['current_password' => 'required']);

        if (!Hash::check($request->current_password, Auth()->password)) {
            return back()->withErrors(['password_delete' => 'Contraseña incorrecta, no se pudo eliminar.']);
        }

        // 2. Validaciones de Negocio
        if ($rol->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: Hay usuarios usando este rol.');
        }

        // 3. Eliminar (El trait Auditable se encargará del log 'deleted')
        $rol->delete();

        return redirect()->route('administracion.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }

}
