<?php

/**
 * Clase para el controlador del login
 * nos permite logearnos con uno de los usuarios disponibles en la BBDD
 */
class loginControlador extends CControlador {


    /**
     * Acción para el login de la aplicación, 
     * nos muestra un formulario que nos pide el nick y contraseña
     * 
     * se validan los datos a partir del modelo de Login, si se cumplen
     * iniciamos sesión con el nick ingresado, si no se muestran los errores del logeo
     *
     * @return Void, no devuelve nada imprime una vista
     */
    public function accionInicioSesion (){

        //Comprobamos si hay usuario logeado, en tal caso
        //lo redirigimos a la acción anterior

		$nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual); 

        //Si hay usuario y no está borrado, hay login, lo mandamos a la acción anterior
		if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){

			Sistema::app()->irAPagina(["inicial"]);
            exit();
		}


        //Barra de ubicación
		$this->barraUbi = [
			[
                "texto" => "inicio",
                "url" => "/"
            ],
            [
                "texto" => "Login",
                "url" => ["login", "InicioSesion"]
            ]
	  	];


        $login = new Login();


        $nombre = $login->getNombre();


        if (isset($_POST[$nombre])){


            //asigno valores de registro
            $login->setValores($_POST[$nombre]);


            if ($login->validar()){

                $codUser = Sistema::app()->ACL()->getCodUsuario($login->nick);
                $nombreUser = Sistema::app()->ACL()->getNombre($codUser); //Nombre de usuario
                $arrayPermisos = Sistema::app()->ACL()->getPermisos($codUser); //Lista de permisos


                if (Sistema::app()->ACL()->getBorrado($codUser) === true){ //Si da true, es borrado, no accede
                    Sistema::app()->paginaError("404", "El usuario está borrado, no puede acceder");
                    exit;
                }
                else{ //Si no está borrado

                    if (Sistema::app()->Acceso() !== null) {

                        $registro = Sistema::app()->Acceso()->registrarUsuario($login->nick, $nombreUser, $arrayPermisos);


                        if ($registro === true){ //Si da true, le mandamos a la acción anterior
                           

                            //gaurdamos en sesion datos del login
                            if (!isset($_SESSION["datosLogin"])){ //Si no existe la creamos
                                $_SESSION["datosLogin"] = [
                                    "nick" => $login->nick,
                                    "pw" => $login->contrasenia
                                ];
                            }
                            else{ //Si existe actualizamos
                                $_SESSION["datosLogin"] = [
                                    "nick" => $login->nick,
                                    "pw" => $login->contrasenia
                                ];
                            }
                            
                            if (isset($_SESSION["anterior"])){
                                Sistema::app()->irAPagina($_SESSION["anterior"]);
                                exit;
                            }
                            else{
                                Sistema::app()->irAPagina(["inicial"]);
                                exit;
                            }

                        }
                        else{
                            Sistema::app()->paginaError("404", "No se ha podido registrar el usuario");
                            exit;
                        }

                    }
                }
             
            }
            else{
                $this->dibujaVista("login", ["miLOGIN" => $login], "Login");
                exit;
            }
        }


        $this->dibujaVista("login", ["miLOGIN" => $login], "Login");
    }



    /**
     * Acción que vamos a usar para mandar datos del login
     * al javascript en formato JSON
     *
     * @return void
     */
    public function accionDatosLogin (){

        if (isset($_SESSION["datosLogin"])){
            $loginJSON = [
                "correcto" => true,
                "datos" => [
                    "nick" => $_SESSION["datosLogin"]["nick"],
                    "pw" => $_SESSION["datosLogin"]["pw"]
                ]
            ];

        }
        else{ //Si no hay login mandamos un false
            $loginJSON = [
                "correcto" => false
            ];
        }




        $resultado =  json_encode($loginJSON, JSON_PRETTY_PRINT);

        echo $resultado;
    }
}






?>