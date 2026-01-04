<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Configuracion\Ods;


class OdsController extends Controller
{
    public function index()
    {
        // Traemos todos los ODS ordenados por su número (1 al 17)
        $ods = Ods::orderBy('numero', 'asc')->get();

        return view('dashboard.configuracion.ods.index', compact('ods'));
    }
    public function edit($id)
    {
        $ods_item = Ods::findOrFail($id);
        return view('configuracion.ods.editar', compact('ods_item'));
    }

    public function update(Request $request, $id)
    {


        $request->validate([
            'numero'      => 'required|integer',
            'nombre_corto'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color_hex'   => 'required|string|max:7', // Validación para el código hexadecimal
        ]);

        $ods = Ods::findOrFail($id);
        $ods->update($request->all());

        return redirect()->route('configuracion.ods.index')
            ->with('success', '¡Excelente! El ODS #' . $ods->numero . ' se ha actualizado correctamente.');
    }
}
