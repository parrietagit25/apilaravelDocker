<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cursos;

class TodoscursosController extends Controller
{
    public function index(){

        $cursos = Cursos::all();

        return json_encode($cursos, true);

    }
}
