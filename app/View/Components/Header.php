<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $userName;

    public function __construct()
    {
        // Obtenemos el nombre del usuario directamente desde la sesión
        $this->userName = Auth::user()->name ?? 'Usuario';
    }

    public function render()
    {
        return view('components.layouts.header');
    }
}
