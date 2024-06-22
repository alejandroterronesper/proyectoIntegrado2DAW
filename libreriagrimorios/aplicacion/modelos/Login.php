<?php

/**
 * Clase del modelo Login
 * recoge datos necesarios para el login
 * 
 * atributos
 *  - Nick : cadena 20 caracteres, obligatoria
 *  - contraseña: cadena 20 caracteres, obligatoria
 *  - Restriccion: saltara un error en el modelo si la contraseña no es igual a "c-nick"
 */
class Login extends CActiveRecord{



    /**
     * Define el nombre del modelo, es un metodo redefinido
     * de CActiveRecord
     *
     * @return string devuelve el nombre del modelo
     */
    protected function fijarNombre(): string
    {
        return "login";
    }


    /**
     * Redefinición del método de CActiveRecord, 
     * se encarga de fijar los atributos de la clase
     * devuelve un array con los atributos de la clase
     *
     * @return array array con los atributos del modelo
     */
    protected function fijarAtributos(): array
    {
        return array ("nick", "contrasenia");
    }


    /**
     * Redefinición del método de CActiveRecord,
     * devuelve un array con las descripciones que aparecen
     * de los atributos
     *
     * @return array
     */
    protected function fijarDescripciones(): array
    {
        return array ("contrasenia" => "contraseña");
    }


    /**
     * Método que se encarga de fijar las restricciones
     * de los diferentes atributos del modelo Login
     *
     * @return array
     */
    protected function fijarRestricciones(): array
    {
        return array (
            array ("ATRI" => "nick",
                    "TIPO" => "REQUERIDO",
                    "MENSAJE" => "Debes introducir un nick"),
            
            array ("ATRI" => "nick", "TIPO" => "CADENA",
                 "TAMANIO" => 20),
            
            array ("ATRI" => "contrasenia",
                    "TIPO" => "REQUERIDO",
                    "MENSAJE" => "Debes introducir una contraseña"),
            array (
                "ATRI" => "contrasenia", "TIPO" => "CADENA",
                 "TAMANIO" => 20
            ),

            //Validación de contraseña con nueva funcion de ACL
            array("ATRI" => "contrasenia", "TIPO" => "FUNCION",
            "FUNCION" => "autenticar"),

            //validamos nick
            array("ATRI" => "nick", "TIPO" => "FUNCION",
            "FUNCION" => "validaUsuario")
        );
    }

    /**
     * Método que da valor a los atributos
     * despues de crear una instancia de este modelo
     *
     * @return void no devuelve nada
     */
    protected function afterCreate(): void
    {
        $this->nick = "";
        $this->contrasenia = "";
    }


    

    /**
     * función que valida el nick de un usuario
     * para comprobar que existe o no
     * 
     * Se llama a la funcion existeUsuario desde la ACL
     * 
     * se le pasa como parámetro el nick del usuario
     * 
     * si devuelve false, lanzará la excepción
     *
     * @return void no devuelve nada
     */
    public function validaUsuario (): void {

        if (!Sistema::app()->ACL()->existeUsuario($this->nick)){
            $this->setError( "nick", 
            "El usuario no existe");
        }
    }



    /**
     * Funcion que se encarga de rellenar
     * el objeto de acceso con los usuarios autenticados
     * 
     *
     * @return void no devuelve nada
     */
    public function autenticar ():void{


        if (!Sistema::app()->ACL()->esValido($this->nick, $this->contrasenia)){
            $this->setError( "contrasenia", 
            "Contraseña incorrecta");
        }
        
    }
}


?>