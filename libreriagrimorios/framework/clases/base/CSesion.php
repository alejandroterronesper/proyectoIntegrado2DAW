<?php

/**
 * Componente CSesion 
 * clase que permite gestionar las sesiones. 
 * 
 * Tiene las siguientes características: 
 * -constructor
 * -crearSesion
 * -haySesion
 * -destruirSesion
 * 
 * 
 */
class CSesion { 


    /**
     * Constructor de la clase modelo
     * CSesion
     */
    // public function __construct()
    // {
    //     if (!$this->haySesion()){ //Si no hay sesión
    //         $this->crearSesion();
    //     }
    // }


    /**
     * Método de la clase CSesion, comprueba si 
     * existe la sesión, en tal caso devuelve true
     * . Si no existe la sesión devuelve false
     *
     * @return Bool True-> Existe sesión | False -> No existe la sesión
     */
    public function haySesion():Bool {

        if (isset($_SESSION)){
            return true;
        }
        else{
            return false;
        }
    }


    /**
     * Método de la clase CSesion
     * que crea la sesión. Primero se comprueba
     * si existe una sesión, en tal caso devuelve false
     * 
     * Si no existe una sesión, se llama a session_start
     * y devolvera true si se ha podido crear o false en caso de no crearse
     *
     * @return Bool True -> si no hay sesión y se crea | False en caso de 
     * existir sesión o en caso de no haberse poder creado
     */
    public function crearSesion(): Bool{


        //primero comprobamos si hay sesión
        if ($this->haySesion() === true){
            return false; //Si hay sesión devuelvo false
        }
        else{ //En caso de no haber sesión, devuelvo true

            return session_start(); //devuelve true si se crea, false si no
        }
    }



    /**
     * Método de la clase CSesion
     * se encarga de eliminar la sesión creada
     * 
     * primero se comprueba si existe sesión, en caso de no existir devuelve false
     * 
     * Si existe, devolvemos session_destroy que devuelve true en caso de eliminarla
     * o false en caso de fallar a la hora de eliminarla
     *
     * @return Bool True --> si se elimina | False -> si no hay sesión o 
     *          No puede eliminar la sesión
     */
    public function destruirSesion(): Bool{

        //comprobamos si existe sesión
        if ($this->haySesion() === true){//Si hay sesión, se destruye

            return session_destroy(); //true si se destruye, false si falla

        }
        else{//Si no hay sesión, devuelvo false
            return false;
        }
    }
}














?>