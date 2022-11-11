<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cursos;
use App\Models\Clientes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    
    // mostrar todos los registros

    public function index(Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();

        $json = [];

        foreach ($clientes as $key => $value) {
            
            // validar token
            if("Basic ".base64_encode($value['id_cliente'].':'.$value['llave_secreta']) == $token){

                //$cursos = Cursos::all();
                // hacer un inner join
                /*
                $cursos = DB::table('cursos')
                ->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
                ->select('cursos.id', 'cursos.titulo', 'cursos.instructor', 'clientes.primer_nombre','clientes.primer_apellido')
                ->get();*/

                if (isset($_GET['page'])) {
                    // con paginacion 

                    $cursos = DB::table('cursos')
                    ->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
                    ->select('cursos.id', 'cursos.titulo', 'cursos.instructor', 'clientes.primer_nombre','clientes.primer_apellido')
                    ->paginate(10);
                }else{
                    $cursos = DB::table('cursos')
                    ->join('clientes', 'cursos.id_creador', '=', 'clientes.id')
                    ->select('cursos.id', 'cursos.titulo', 'cursos.instructor', 'clientes.primer_nombre','clientes.primer_apellido')
                    ->get();
                }

                

                if (!empty($cursos)) {
        
                    $json = [
                        "status"=>200, 
                        "Total Registros"=>count($cursos),
                        "detalles" =>$cursos 
                    ];
        
                    return json_encode($json, true);
                
                }else{
        
                    $json = [
                        "status"=>200, 
                        "Total Registros"=>0,
                        "detalles" =>"No hay nincgun curso registrado" 
                    ];
        
                    //return json_encode($json, true);
        
                } 

                break;
                
            }else {

                $json = [
                    "status"=>404, 
                    "Total Registros"=>0,
                    "detalles" =>"No esta autorizado para ver los registros" 
                ];

            }

        }

        return json_encode($json, true);

    } 

    // crear un registro

    public function store(Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();
        $json = [];

        foreach ($clientes as $key => $value) {

            // validar token
            if("Basic ".base64_encode($value['id_cliente'].':'.$value['llave_secreta']) == $token){

                $datos = array("titulo"=>$request->input("titulo"), 
                               "descripcion"=>$request->input("descripcion"), 
                               "instructor"=>$request->input("instructor"), 
                               "imagen"=>$request->input("imagen"), 
                               "precio"=>$request->input("precio"));

                if (!empty($datos)) {
                   
                    $validator = Validator::make($datos, [
                        'titulo' => 'required|string|max:255|unique:cursos',
                        'descripcion' => 'required|string|max:255|unique:cursos',
                        'instructor' => 'required|string|max:255|',
                        'imagen' => 'required|string|max:255|unique:cursos',
                        'precio' => 'required|numeric',
                    ]);

                    // se falla la validacion 

                    if ($validator->fails()) {

                        $errores = $validator->errors(); // manera correcta de validar todos los errores y muestre los mensajes de error bien 

                        $json = [
                            "status"=>404, 
                            "detalles" =>$errores 
                        ];

                        return json_encode($json, true);
                        
                    }else{

                        $cursos = new Cursos();
                        $cursos->titulo = $datos['titulo'];
                        $cursos->descripcion = $datos['descripcion'];
                        $cursos->instructor = $datos['instructor'];
                        $cursos->imagen = $datos['imagen'];
                        $cursos->precio = $datos['precio'];
                        $cursos->id_creador = $value['id'];

                        $cursos->save();

                        $json = [
                            "status"=>200, 
                            "detalles" =>"Registro Exitoso" 
                        ];

                        return json_encode($json, true);

                    }

                }else{

                    $json = [
                        "status"=>404, 
                        "detalles" =>"Registro con errores" 
                    ];

                }

             }
            
        }

        return json_encode($json, true);


    }

    // tomar un solo registro 

    public function show($id, Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();
        $json = [];

        foreach ($clientes as $key => $value) {

            // validar token

            if("Basic ".base64_encode($value['id_cliente'].':'.$value['llave_secreta']) == $token){

                $cursos = Cursos::where('id', $id)->get();

                if (!empty($cursos)) {
        
                    $json = [
                        "status"=>200, 
                        "detalles" =>$cursos 
                    ];
        
                    return json_encode($json, true);
                
                }else{
        
                    $json = [
                        "status"=>200, 
                        "detalles" =>"No hay ningun curso registrado" 
                    ];
        
                    return json_encode($json, true);
        
                } 
            
            }else{

                $json = [
                    "status"=>400, 
                    "detalles" =>"No esta autorizado para consultar ese unico registro" 
                ];

            }

        }

        return json_encode($json, true);

    }

    // editar un registro

    public function update($id, Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();
        $json = [];

        foreach ($clientes as $key => $value) {

            // validar token

            if("Basic ".base64_encode($value['id_cliente'].':'.$value['llave_secreta']) == $token){

                $datos = array("titulo"=>$request->input("titulo"), 
                               "descripcion"=>$request->input("descripcion"), 
                               "instructor"=>$request->input("instructor"), 
                               "imagen"=>$request->input("imagen"), 
                               "precio"=>$request->input("precio"));

                if (!empty($datos)) {
                   
                    $validator = Validator::make($datos, [
                        'titulo' => 'required|string|max:255',
                        'descripcion' => 'required|string|max:255',
                        'instructor' => 'required|string|max:255',
                        'imagen' => 'required|string|max:255',
                        'precio' => 'required|numeric',
                    ]);

                    // se falla la validacion 

                    if ($validator->fails()) {

                        $json = [
                            "status"=>404, 
                            "detalles" =>"No se permiten caracteres especiales, el precio tiene que ser numerico" 
                        ];

                        return json_encode($json, true);
                        
                    }else{

                        $traer_curso = Cursos::where('id', $id)->get();

                        if ($value['id'] == $traer_curso[0]['id_creador']) {

                            // actualizar

                            $datos = ["titulo"=>$datos['titulo'], 
                                      "descripcion"=>$datos['descripcion'], 
                                      "instructor"=>$datos['instructor'], 
                                      "imagen"=>$datos['imagen'], 
                                      "precio"=>$datos["precio"]];

                            $actualizar = Cursos::where('id', $id)->update($datos);
                        
                            $json = [
                                "status"=>200, 
                                "detalles" =>"Registro Exitoso, su curso ha sido actuaizado" 
                            ];

                            return json_encode($json, true);

                        }else{

                            $json = [
                                "status"=>404, 
                                "detalles" =>"No esta autorizado para modificar este curso" 
                            ];

                            return json_encode($json, true);
                            
                        }
                    }

                }else{

                    $json = [
                        "status"=>404, 
                        "detalles" =>"Registro con errores" 
                    ];

                }

             }
            
        }

        return json_encode($json, true);

    }

    // eliminar un restro

    public function destroy($id, Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();
        $json = [];

        foreach ($clientes as $key => $value) {

            // validar token

            if("Basic ".base64_encode($value['id_cliente'].':'.$value['llave_secreta']) == $token){

                $validar = Cursos::where("id", $id)->get();

                if (!empty($validar)) {

                    if ($value['id'] == $validar[0]['id_creador']) { 

                        $cursos = Cursos::where("id", $id)->delete();

                        $json = [
                            "status"=>200, 
                            "detalles" =>"Se ha borrado con exito" 
                        ];
    
                        return json_encode($json, true);

                    }else{

                        $json = [
                            "status"=>404, 
                            "detalles" =>"No esta autorizado para eliminar este curso" 
                        ];
    
                        return json_encode($json, true);

                    }

                }else{

                    $json = [
                        "status"=>404, 
                        "detalles" =>"El curso no existe" 
                    ];

                    return json_encode($json, true);

                }


             }

        }

    }

}
