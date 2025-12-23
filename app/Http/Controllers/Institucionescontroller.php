<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Model_entidad_publica;
use App\Models\Model_sector;
use App\Models\Model_subsector;
use Carbon\Carbon;
use Dotenv\Parser\Entry;

use function Symfony\Component\Clock\now;

class Institucionescontroller extends Controller
{
    public function crear()
    {
        $datossubsector = Model_subsector::all();
       return view('view_entidad_publica.crear',compact('datossubsector'));
    }

    public function leerentidadpublica()
    {
        $entidadpublica = Model_entidad_publica::all();
        return view('view_entidad_publica.leer',compact('entidadpublica'));

    }
    public function leersectores()
    {
        return view('view_entidad_publica.crear', compact('sectores'));
    }
    public function leersubsectores()
    {
        $subsector = Model_subsector::all();
        return view('view_entidad_publica.crear', compact('datossubsector'));
    }

    public function update(Request $request, Model_entidad_publica $entidadpublica)
    {
        $request->validate([
            'id_sector' => 'required|integer',
            'codigo_oficial' => 'required|string|max:255',
            'nombre_organizacion' => 'required|string|max:255',
            'nombre_subsector' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ]);
        $entidadpublica->update($request->all());

        return redirect()->route('view_entidad_publica.leer')->with('success', 'Entidad actualizada exitosamente.');
    }


    public function store(Request $request)
    {

       $now=Carbon::now();
       $fecha_actual=$now->format(format:'Y-m-d H:i:s');

        $entidadpublica = new Model_entidad_publica();
        $entidadpublica->id_subsector = $request->id_subsector;
        $entidadpublica->codigo_oficial = $request->codigo_oficial;
        $entidadpublica->nom_organizacion = $request->nom_organizacion;
        $entidadpublica->nivel_gobierno = $request->nivel_gobierno;
        $entidadpublica->estado = $request->estado;
        $entidadpublica->fecha_creacion = $fecha_actual;
        $entidadpublica->save();
        return redirect()->route('view_entidad_publica.crear')->with('success', 'Entidad creada exitosamente.');
    }
}
