<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Clientes;


class ClientesController extends BaseController
{
    public function index(){

        $json = array(
            "detalle"=>"No encontrado"
        );

        return json_encode($json, true);

    }

    // metodo post reditro

    public function store(Request $request){

        // recoger satos

        $datos = array("primer_nombre"=>$request->input("primer_nombre"), 
                       "primer_apellido"=>$request->input("primer_apellido"), 
                       "email"=>$request->input("email"));

        if (!empty($datos)) {

        //validar datos 

            $validator = Validator::make($datos, [
                'primer_nombre' => 'required|string|max:255',
                'primer_apellido' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:clientes',
            ]);

            // si la validacion falla

            if ($validator->fails()) {
                $json = array(
                    "staus"=>404,
                    "detalle"=>"Registros con errores"
                );
        
                return json_encode($json, true);

            }else{

                $id_cleinte = Hash::make($datos['primer_nombre'].$datos['primer_apellido'].$datos['email']);
                $llave_secreta = Hash::make($datos['email'].$datos['primer_apellido'].$datos['primer_nombre'], [
                    'rounds' => 12
                ]);

                $cliente = new Clientes();
                $cliente->primer_nombre = $datos['primer_nombre'];
                $cliente->primer_apellido = $datos['primer_apellido'];
                $cliente->email = $datos['email'];
                $cliente->id_cliente = str_replace('$', 'a', $id_cleinte);
                $cliente->llave_secreta = str_replace('$', 'o', $llave_secreta);

                $cliente->save();

                $json = [
                    "status"=>"200", 
                    "detalle"=>"Registro exitoso, guarde las credenciales",
                    "credenciales" => [
                    "id_cliente" => str_replace('$', 'a', $id_cleinte), 
                    "llave_secreta" => str_replace('$', 'o', $llave_secreta)
                    ]
                ];
        
                return json_encode($json, true);

            }
        }else{

            $json = array(
                "status"=>"404", 
                "id_cliente"=>"Registro con errores"
            );
    
            return json_encode($json, true);

        }
    }
}
