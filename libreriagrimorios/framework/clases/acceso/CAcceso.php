<?php

/**
 * Clase del componente
 * CAcceso de la relación 8
 * 
 * Se modifica para que use una instancia CSesion privada
 * 
 */
class CAcceso {

    // Variables de instancia
    private bool $_validado;
    private string $_nick;
    private  string $_nombre;
    private array $_permisos;
    private CSesion $_sesion;


    
    /**
     * Constructor de la clase acceso
     * desde aqui inicializo las variables
     * 
     * y llamo al metodo recogerDeSesion
     * que es desde cogemos los datos de usuario
     * 
     */
    public function __construct() {
        
        $this->_validado=false;
        $this->_nick="";
        $this->_nombre="";
        $this->_permisos=[];
        $this->_sesion = new CSesion ();
        
        $this->recogerDeSesion();
        
    }


    /**
     * Funcion privada para guardar la información en la sesión
     *
     * @return boolean Devuelve true si se ha podido hacer. False en cualquier otro caso
     */
    private function escribirASesion():bool
    {
     if (!$this->_sesion->haySesion())    
         return false;
     
     if ($this->_validado)
     {
         $_SESSION["acceso"]["validado"]=true;
         $_SESSION["acceso"]["nick"]=$this->_nick;
         $_SESSION["acceso"]["nombre"]=$this->_nombre;
         $_SESSION["acceso"]["permisos"]=$this->_permisos;


        return true;
         
     }
     else 
     {
         $_SESSION["acceso"]["validado"]=false;
         return true;
     }
    }



    /**
     * Función privada que recoje la información de la sesión
     *
     * @return boolean Devuelve true si se ha podido recoger
     */
    private function recogerDeSesion():bool
    {
       if (!$this->_sesion->haySesion() ||
           !isset($_SESSION["acceso"]) ||
           !isset($_SESSION["acceso"]["validado"]) ||
           $_SESSION["acceso"]["validado"]==false)
       {
           $this->_validado=false;
       }
       else 
       {
           $this->_validado=true;
           $this->_nick=$_SESSION["acceso"]["nick"];
           $this->_nombre=$_SESSION["acceso"]["nombre"];
           $this->_permisos=$_SESSION["acceso"]["permisos"];
           
       }

       return true;
        
    }



    /**
     * Sirve para registrar un usuario en la aplicación. Almacena
     * los valores en las propiedades apropiadas y en la sesión 
     * para guardar en la sesión la información del usuario validado.
     *
     * @param string $nick nick del usuario a registrar
     * @param string $nombre nombre del usuario a registrar
     * @param array $permisos permisos del usuario a registrar
     * @return boolean Devuelve true si ha podido registrar el usuario
     */
    public function registrarUsuario(string $nick, string $nombre, array $permisos):bool
     {
        if ($nick == "")
            $this->_validado = false;
        else
            $this->_validado = true;
        $this->_nick = $nick;
        $this->_nombre = $nombre;
        $this->_permisos = $permisos;
        
        if (!$this->escribirASesion())
            return false;

        return true;
    }

   
    /**
     * Elimina la información de registro de un usuario
     *
     * @return boolean Devuelve true si ha podido hacerlo
     */
    public function quitarRegistroUsuario():bool {
        $this->_validado = false;
        if (!$this->escribirASesion())
             return false;

        return true;
    }


    
    /**
     * Función que devuelve si hay o no un usuario registrado
     *
     * @return boolean Devuelve true si hay usuario registrado. False en caso contrario
     */
    public function hayUsuario():bool {
        return $this->_validado;
    }


    /**
     * Función que devuelve si el usuario registrado tiene o no el permiso indicado
     *
     * @param integer $numero Numero de permiso a comprobar
     * @return bool Devuelve true si hay usuario registrado y tiene el permiso indicado
     */
    public function puedePermiso(int $numero):bool {
       
        //se comprueba que hay usuario registrado
        if ($this->hayUsuario()){

            //comprobamos que existe el permiso
            if ( array_key_exists($numero, $this->_permisos)){
        
                //accedemos a la var permisos, le damos la posicion que se pasa como parametro
                //por cada valor hay un bool, si da true es que tiene permiso, si da false es que no
                return $this->_permisos[$numero];
            }
            else{ //no esta el permiso
                return false;
            }
        }
        else{
            return false;
        }


    }



    /*
    * Métodos get
    */

    /**
    * Devuelve el nick del usuario indicado o false si no hay usuario
    *
    * @return string|false devuelve el nick del usuairo o false si no hay usuario
    */
    public function getNick():string|false 
    { 
        if (!$this->hayUsuario()){
            return false;
        }
        else{
            return $this->_nick;
        }
    }


    /**
     * Devuelve el nombre del usuario registrado o false si no hay usuario
     *
     * @return string|false devuelve el nombre de usuario o false si no existe el usuario
     */
    public function getNombre():string|false 
    { 
        if (!$this->hayUsuario())
            return false;
        
        return $this->_nombre; 
    }    


    //------------------------------------------------------------------------//
    //-------------------No se pueden crear propiedades dinámicas-------------//
    //------------------------------------------------------------------------//

     /**
     * Método mágico set, lo usamos para quitar 
     * la carga dinamica de propiedades
     *
     * @param String $propiedad propiedad que queremos modificar
     * @param Mixed $valor el valor nuevo que va a tomar la propiedad
     * @return Void no devuelve nada, pero si no la encuentra lanza excepción
     */
    public function __set(string $propiedad, mixed $valor):void
    {
        throw new Exception('No se puede modificar la propiedad ' . $propiedad);
    }


    
    /**
     * Método mágico get, 
     * se usa para consultar datos de propiedades no accesibles. 
     *
     * @param String $propiedad propiedad a la que queremos acceder
     * @return Mixed devuelve el valor de propiedad o se lanza excepcion si no se encuentra
     */
    public function __get(String $propiedad): mixed
    {
        throw new Exception('No se puede obtener el valor de ' . $propiedad);
    }


    /**
     * 
     * Método mágico isset
     * 
     * Comprueba que una propiedad existe o no
     * 
     * @param String $propiedad propiedad que queremos que compruebe que exista
     * @return Bool  false para evitar que se añadan propiedades dinámicas
     */
    public function __isset(string $propiedad): bool
    {
        return false;
    }


    
    /**
     * Método mágico unset
     * Se invoca cuando se usa unset() sobre propiedades inaccesibles
     *
     * @param String $propiedad es la variable sobrecargada
     * @return void no devuelve nada, solo excepción si no la encuentra
     */
    public function __unset(String $propiedad): void
    {
        throw new Exception("No existe la propiedad ".$propiedad);
    }

}


?>