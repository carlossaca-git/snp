<?php

namespace App\Http\Controllers\Inversion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inversion\Programa; // Asegúrate de que el modelo esté aquí

class ProgramaController extends Controller
{
    /**
     * Muestra el listado de programas.
     */
    public function index()
    {
        // USAMOS PAGINATE en lugar de all() para que no colapse la tabla
        $programas = Programa::orderBy('id_programa', 'desc')->paginate(10);

        // CORRECCIÓN: Apuntamos a la nueva carpeta de vistas
        return view('inversion.programas.index', compact('programas'));
    }

    /**
     * Muestra el formulario para crear un nuevo programa.
     */
    public function create()
    {
        // CORRECCIÓN: Apuntamos a la nueva carpeta de vistas
        return view('inversion.programas.create');
    }

    /**
     * Almacena el recurso en la base de datos (tra_programa).
     */
    /**
     * Almacena el recurso en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN DE DATOS
        $request->validate([
            'nombre_programa'   => 'required|max:255',
            'codigo_cup'        => 'required|unique:tra_programa,codigo_cup',
            'monto_planificado' => 'required|numeric',
            // Agrega aquí validaciones para anio_inicio, anio_fin si son obligatorios
        ]);

        // 2. PREPARACIÓN DE DATOS
        // Tomamos todos los datos que vienen del formulario
        $datos = $request->all();

        // --- AQUÍ ESTÁ LA MAGIA ---
        // Inyectamos manualmente el ID del usuario conectado en el campo 'registrado_by'
        $datos['registrado_by'] = auth()->id();

        // 3. GUARDAR EN BASE DE DATOS
        // Usamos el array $datos (que ya tiene el ID del usuario)
        Programa::create($datos);

        // 4. REDIRECCIÓN
        return redirect()->route('inversion.programas.index')
            ->with('status', 'Programa registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar (Faltaba este método).
     */
    public function edit($id)
    {
        $programa = Programa::findOrFail($id);
        return view('inversion.programas.edit', compact('programa'));
    }

    /**
     * Actualiza el registro (Faltaba este método).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_programa'   => 'required|max:255',
            'codigo_cup'        => 'required|unique:tra_programa,codigo_cup,'.$id, // Ignora su propio ID
            'monto_planificado' => 'required|numeric',
        ]);

        $programa = Programa::findOrFail($id);

        $datos = $request->all();

        // SI TIENES UN CAMPO PARA AUDITAR EDICIONES:
        // $datos['modificado_by'] = auth()->id();

        $programa->update($datos);

        return redirect()->route('inversion.programas.index')
            ->with('status', 'Programa actualizado correctamente.');
    }

    /**
     * Elimina (Soft Delete) el registro.
     */
    public function destroy($id)
    {
        $programa = Programa::findOrFail($id);

        // SoftDeletes se encarga automáticamente si está en el Modelo
        $programa->delete();

        return redirect()->back()
            ->with('status', 'Programa enviado a la papelera correctamente.');
    }
}
