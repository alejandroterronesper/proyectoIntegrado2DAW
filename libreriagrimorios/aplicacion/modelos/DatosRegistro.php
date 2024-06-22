  <?php


class DatosRegistro extends CActiveRecord{


    protected function fijarNombre(): string
    {
        return "dato";
    }



    protected function fijarAtributos(): array
    {
        return array ("nick", "nif", "fecha_nacimiento",
                     "provincia", "estado",
                      "contrasenia","confirmar_contrasenia"
                    );
    }


    protected function fijarDescripciones(): array
    {
        return array ("fecha_nacimiento" => "Fecha de nacimiento",
                        "contrasenia" => "Contraseña",
                        "confirmar_contrasenia" => "Confirmar contraseña");
    }


    protected function fijarRestricciones(): array
    {
        
        
        return array(
            //Atributos obligatorios
            array ("ATRI" => "nick",
            "TIPO"=> "REQUERIDO",
            "MENSAJE" => "Debe introducir un nick"),

            array ("ATRI" =>  "nif",
            "TIPO"=> "REQUERIDO",
            "MENSAJE" => "Debe introducir un nif"),

            array ("ATRI" =>  "contrasenia",
            "TIPO"=> "REQUERIDO",
            "MENSAJE" => "Debe introducir una contraseña"),

            array ("ATRI" =>  "confirmar_contrasenia",
            "TIPO"=> "REQUERIDO",
            "MENSAJE" => "Debe introducir el campo repite contraseña"),

            //longitud cadena nick 
            array ("ATRI"=>"nick", "TIPO"=>"CADENA",
                    "TAMANIO"=> 40),

            //longitud minima del nif
            array ("ATRI"=> "nif", "TIPO" => "CADENA", 
                    "TAMANIO" => 10),
            
         
            //Establecemos fecha por defecto            
            array ("ATRI"=> "fecha_nacimiento", "TIPO"=> "FECHA"
                    ,"DEFECTO" => $this->devuelveFechaDefecto()),
            
            //Validamos fecha
            array ("ATRI" => "fecha_nacimiento", 
                    "TIPO" => "FUNCION",
                    "FUNCION" => "validaFechaNacimiento",
                    "DEFECTO" => ""),

                
            //PROVINCIA
            array ("ATRI" => "provincia",
                    "TIPO" => "CADENA", "TAMANIO" => 30,
                "DEFECTO" => "MALAGA"),

            //AQUI MIRAR COMO SE VALIDA ESTADO
            array ("ATRI" => "estado", "TIPO" => "ENTERO",
                    "DEFECTO" => 0),


            //Valido estado
            array ("ATRI" => "estado", "TIPO" => "FUNCION",
                "FUNCION" => "validaEstado"
            ),
            
            //CONTRASEÑA
            array ("ATRI"=> "contrasenia", "TIPO" => "CADENA",
                    "TAMANIO" => 30),


            //REPITE CONTRASEÑA
            array ("ATRI"=> "confirmar_contrasenia", "TIPO" => "CADENA",
            "TAMANIO" => 30),


            //Valido estado
            array ("ATRI" => "confirmar_contrasenia", "TIPO" => "FUNCION",
            "FUNCION" => "validaContrasenia")
        );
    }

    protected function afterCreate(): void
    {
        $this->nick = "";
        $this->nif = "";
        $this->fecha_nacimiento = $this->devuelveFechaDefecto();
        $this->provincia = "MALAGA";
        $this->estado = 0;
        $this->contrasenia = "";
        $this->confirmar_contrasenia = "";
    }


    /**
     * Función que valida la fecha
     * del atributo fecha_nacimiento
     * 
     * Se comprueba que la fecha que se pasa como parámetro
     * es menor a 1/1/1900
     * 
     * Si no se cumple la condición se almacena el error
     *
     * @return void
     */
    public function validaFechaNacimiento():void{

        $fechaObj = DateTime::createFromFormat("d/m/Y", 
                $this->fecha_nacimiento);
        
        $fechaLimite = DateTime::createFromFormat("d/m/Y",
            "01/01/1900");
        
        $fechaActual = new DateTime();
        if ($fechaObj < $fechaLimite){

            $this->setError( "fecha_nacimiento", 
            "La fecha de nacimiento debe ser anterior a 01/01/1900");
        }

        if ($fechaObj > $fechaActual){
            $this->setError( "fecha_nacimiento", 
            "La fecha de nacimiento no puede ser posterior a la fecha de hoy");
        }
    }

    /**
     * Funcion que valida el estado seleccionado
     * comprueba desde la funcion de clase dameEstados si existe
     * para ello se comprueba si lo que devuelve es la cadena del estado
     * si es distinto de eso de guarda el error correspondiente
     *
     * @return void
     */
    public function validaEstado (){


        if (!is_string(DatosRegistro::dameEstados($this->estado))){
            $this->setError( "estado", 
            "Debe elegir un estado existente");
        }
    }
    

    /**
     * Funcion que valida
     * que el atributo contrasenia
     * sea igual a repite_contrasenia
     * 
     * Si no son iguales, generamos error
     *
     * @return Void
     */
    public function validaContrasenia ():Void{
        if ($this->contrasenia !== $this->confirmar_contrasenia){

            $this->setError( "confirmar_contrasenia", 
            "Deben coincidir las contraseñas");
        }
    }


    /**
     * Función que valida el formato de una cadena de DNI
     * 
     * usamos la funcion validaDNI de CValidaciones
     * 
     * Si da false la validación, mandamos mensaje
     * de error
     *
     * @return void
     */
    public function validarDNI ():void {

        $partes = [];

        if (!CValidaciones::validaDNI($this->nif, $partes)){
            $this->setError("nif", "Formato de DNI inválido");
        }


    }



    /**
     * Método estático del modelo DatosRegistros
     * 
     * recibe como parámetro un entero cod_estado
     * si el cod_estado es nulo, devuelve un array con los 5 estados
     * 
     * Si es distinto de nulo, se comprueba si el valor se encuentra en el array
     * si corresponde, devuelve el estado
     * Si no lo encuentra devuelve false
     *
     * @param Integer|Null $cod_estado -> cod del estado entero o nulo
     * @return  Array Si el codigo es null , 
     *              si encuentra el codigo devuelve String descripcion,
     *          Si no lo encuentra, devuelve false
     */
    public static  function dameEstados (?int $cod_estado = null): array |string |false{
        
        //array de estados
        $estados = array( 
            0 => "no sabe",
            1 => "estudiando",
            2 => "trabajando",
            3 => "en paro",
            4 => "jubilado"
        );

        if ($cod_estado === null){ //comprobamos si es nulo
            return  $estados;
        }
        else{
            //Si no es nulo, se comprueba que existe en el array
            if (isset($estados[$cod_estado])){
                return $estados[$cod_estado];
            }
            else{ //Si no existe, devuelvo false
                return false;
            }
        }
    }



    /**
     * Función que devuelve la fecha por
     * defecto para el atributo fecha_nacimiento
     * Será la fecha actual  - 18 años
     * @return String formato fecha dd/mm/aaaa
     */
    private function devuelveFechaDefecto ():String{

        $fechaActual = new DateTime();
        $fechaDefecto = $fechaActual->sub(new DateInterval("P18Y")); 


        return $fechaDefecto->format("d/m/Y");
    }

}



?>