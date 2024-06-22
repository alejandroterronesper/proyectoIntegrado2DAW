<?php


/**
 * Clase para el modelo libros
 * de la vista cons_libroscontiene los parámetros correspondiente
 * y sus restricciones contiene el método de dameEditoriales
 * que en funcion del parámetro pasado, nos devolvera un genero
 * o una lista de las editoriales actuales de la tabla editoriales o false en caso de error
 */
class Libros extends   CActiveRecord {


    /**
     * Devuelve el nombre del modelo
     *
     * @return String cadena del nombre del modelo
     */
    protected function fijarNombre(): String {

        return "libros";
    }


    /**
     * Devuelve array con los atributos
     *
     * @return Array de los atributos del modelo de libros
     */
    protected function fijarAtributos(): array
    {
        return array (
            "cod_libro", "titulo", "isbn",
            "cod_editorial", "editorial","cod_genero", "genero","autor",
            "fecha_lanzamiento", "unidades", "precio_venta",
            "foto", "borrado"
        );
    }


    /**
     * Devuelve el nombre de la tabla
     *
     * @return String de la vista del modelo de libros
     */
    protected function fijarTabla(): string
    {
        return "cons_libros";
    }


    /**
     * Primary key de la vista
     *
     * @return String devuelve cadena del nombre de la primary key
     */
    protected function fijarId(): string
    {
        return "cod_libro";
    }

    /**
     * Devuelve un array
     * de las diferentes parámetros
     * que tiene el modelo de libros
     *
     * @return Array con descripción de los parámetros
     */
    protected function fijarDescripciones(): array
    {
        return array (
                        "cod_editorial" => "Editorial",
                        "cod_genero" => "Género",
                        "editorial" => "Editorial",
                        "genero" => "Género",
                        "fecha_lanzamiento" => "Fecha de lanzamiento",
                        "precio_venta" => "Precio de venta",
                        "autor" => "Autor",
                        "titulo" => "Título",
                        "isbn" => "ISBN"
                    );
    }



    /**
     * Función que devuelve un array con las difernetes restricciones de 
     * modelo actual
     *
     * @return Array de restricciones
     */
    protected function fijarRestricciones(): array
    {
        return array (


            //titulo
            array("ATRI" => "titulo", "TIPO" => "REQUERIDO", "MENSAJE" => "Debe introducir un título para el libro"),
            array ("ATRI" => "titulo", "TIPO" => "CADENA", "TAMANIO" => 35, "MENSAJE" => "El título no puede superar los 35 caracteres"),

            //isbn
            array("ATRI" => "isbn", "TIPO" => "REQUERIDO", "MENSAJE" => "EL ISBN es obligatorio para el libro"),
            array ("ATRI" => "isbn", "TIPO" => "CADENA", "TAMANIO" => 17, "MENSAJE"=> "El Código ISBN debe tener una longitud de 17 caracteres"),
            array ("ATRI" => "isbn", "TIPO" => "FUNCION", "FUNCION" => "validaISBN13"),


            //cod_editorial
            array ("ATRI" => "cod_editorial", "TIPO" => "REQUERIDO", "MENSAJE" => "La editorial es obligatoria"),
            array ("ATRI" => "cod_editorial", "TIPO" => "ENTERO"),
            array("ATRI" => "cod_editorial", "TIPO" => "RANGO","RANGO" => array_keys($this->dameEditorial()) , "MENSAJE" => "Debe elegir una editorial disponible"),
            
            //editorial
            array ("ATRI" => "editorial", "TIPO" => "CADENA", "MAXIMO" => 30),
            array ("ATRI" => "editorial", "TIPO" => "REQUERIDO"),
            
            //cod_genero
            array ("ATRI" => "cod_genero", "TIPO" => "REQUERIDO", "MENSAJE" => "Debes elegir un género"),
            array ("ATRI" => "cod_genero", "TIPO" => "ENTERO"),
            array("ATRI" => "cod_genero", "TIPO" => "RANGO",
                "RANGO" => array_keys(Generos::dameGenero(null)), "MENSAJE" => "Debes elegir un género disponible"),

            //genero
            array ("ATRI" => "genero", "TIPO" => "CADENA", "MAXIMO" => 30),
            array ("ATRI" => "genero", "TIPO" => "REQUERIDO"),


            //autor
            array ("ATRI" => "autor", "TIPO" => "REQUERIDO", "MENSAJE" => "El autor es obligatorio"),
            array ("ATRI" => "autor", "TIPO" => "CADENA", "TAMANIO" => 50, "La longitud del autor debe ser 50 caracteres como máximo"),


            //fecha_lanzamiento
            array("ATRI" => "fecha_lanzamiento", "TIPO" => "REQUERIDO", "MENSAJE" => "Debes introducir una fecha"),
            array ("ATRI" => "fecha_lanzamiento", "TIPO" => "FECHA", "DEFECTO" => new DateTime()), //se coge la fecha por defecto de hoy

            
            //validacion de fecha no puede ser posterior al dia de hoy ni anterior a 1/1/2007
            array ("ATRI" => "fecha_lanzamiento", "TIPO" => "FUNCION", "FUNCION" => "validaFecha", "DEFECTO" => ""),

            
            //unidades
            array ("ATRI" => "unidades", "TIPO" => "REQUERIDO", "MENSAJE" => "Debes introducir una unidad"),
            array ("ATRI" => "unidades", "TIPO" => "ENTERO", "MIN" => 1, "DFEFECTO" => 1,
                 "MENSAJE" => "No se pueden introducir unidades negativas"),


            //precio_venta
            array ("ATRI" => "precio_venta", "TIPO" => "REQUERIDO", "MENSAJE" => "El precio de venta es obligatorio"),
            array ("ATRI" => "precio_venta", "TIPO" => "REAL",
            "MIN" => 1, "DEFECTO" => 1 ,"MENSAJE" => "Debes introducir un precio mayor que 0"),


            //FOTO
            array ("ATRI" => "foto", "TIPO" => "CADENA", "TAMANIO" => 40, "DEFECTO" => "libro.png"),

            //BORRADO
            array("ATRI" => "BORRADO", "TIPO" => "ENTERO",
             "DEFECTO" => 0),
            array ("ATRI" => "BORRADO", "TIPO" => "RANGO",
            "RANGO" => array(0,1), "MENSAJE" => "Debes elegir una opción disponible"),

         
        );
    }

    /**
     * Función que inicializa los diferentes parámetros
     * tras darle memoria al modelo de libros
     *
     * @return Void, no devuelve inicializa valores
     */
    protected function afterCreate(): void
    {
        $this->cod_libro = 0;
        $this->titulo = "";
        $this->autor = "";
        $this->isbn = "";
        $this->editorial = ""; //metodo estatico
        $this->cod_editorial = 0;
        $this->genero = ""; //metodo estático
        $this->cod_genero = 0;
        $this->fecha_lanzamiento = new DateTime();
        $this->unidades = 0;
        $this->precio_venta = 0;
        $this->foto = "libro.png";
        $this->borrado = 0; //0 es no borrado 1 es borrado

    }


    /**
     * Función que convierte/transforma   
     * los diferentes valores del modelo actual
     * buscado en la BDD, por ejemplo transformación de fechas
     * saneamiento de cadenas, y conversión de tipos
     *
     * @return Void, no devuelve nada
     */
    protected function afterBuscar(): void
    {
        
        //conversión de fecha
        $fecha = $this->fecha_lanzamiento;
        $fecha = CGeneral::fechaMysqlANormal($fecha);
        $this->fecha_lanzamiento = $fecha;


        //pasamos cadenas a entero/real, en caso de los números
        $this->cod_libro = intval($this->cod_libro);
        $this->cod_editorial = intval( $this->cod_editorial);
        $this->cod_genero = intval($this->cod_genero);
        $this->precio_venta = floatval($this->precio_venta);
        $this->borrado = intval($this->borrado);
         

        $this->editorial = $this->dameEditorial($this->cod_editorial);
    }



    /**
     * Devuelve una cadena INSERT
     * para la tabla actual
     *
     * @return String cadena de la sentencia insert
     */
    protected function fijarSentenciaInsert(): string
    {
        
        $titulo = trim($this->titulo); 
        $titulo = CGeneral::addSlashes($this->titulo);

        $isbn = trim( $this->isbn);
        $isbn = CGeneral::addSlashes($this->isbn);

        $cod_editorial = intval($this->cod_editorial);

        $cod_genero = intval($this->cod_genero);

        $autor = trim($this->autor);
        $autor = CGeneral::addSlashes($this->autor);

        $fecha_lanzamiento = CGeneral::fechaNormalAMysql($this->fecha_lanzamiento);

        $unidades = intval($this->unidades);

        $precio_venta = floatval($this->precio_venta);

        $foto = trim($this->foto);
        $foto = CGeneral::addSlashes($this->foto);

        $borrado = intval($this->borrado);

        $sentencia =  "INSERT INTO `libros` (`titulo`, `isbn`, `cod_editorial`, 
                                    `cod_genero`, `autor`, `fecha_lanzamiento`,
                                    `unidades`, `precio_venta`, `foto`
                                    ,`borrado`)
                                    
                        VALUES ('$titulo', '$isbn', $cod_editorial, 
                                $cod_genero, '$autor', '$fecha_lanzamiento',
                                $unidades, $precio_venta, '$foto',
                                $borrado)";

        return $sentencia;
    }




    /**
     * Devuelve la sentencia SQL Update que se ejecutará cuando se guarde el registro
     *
     * @return String de la cadena UPDATE
     */
    protected function fijarSentenciaUpdate(): string
    {
        $cod_libro = intval($this->cod_libro);

        $titulo = trim($this->titulo); 
        $titulo = CGeneral::addSlashes($this->titulo);

        $isbn = trim( $this->isbn);
        $isbn = CGeneral::addSlashes($this->isbn);

        $cod_editorial = intval($this->cod_editorial);

        $cod_genero = intval($this->cod_genero);

        $autor = trim($this->autor);
        $autor = CGeneral::addSlashes($this->autor);

        $fecha_lanzamiento = CGeneral::fechaNormalAMysql($this->fecha_lanzamiento);

        $unidades = intval($this->unidades);

        $precio_venta = floatval($this->precio_venta);

        $foto = trim($this->foto);
        $foto = CGeneral::addSlashes($this->foto);

        $borrado = intval($this->borrado);

        $sentencia = "UPDATE `libros` SET `titulo` = '$titulo',  `isbn` = '$isbn' ,
                                    `cod_editorial` = $cod_editorial, `cod_genero` = '$cod_genero',
                                    `autor` = '$autor', `fecha_lanzamiento` = '$fecha_lanzamiento',
                                    `unidades` = $unidades, `precio_venta` = $precio_venta,
                                    `foto` = '$foto', `borrado` = '$borrado' 
                                
                                    WHERE `cod_libro` = $cod_libro";


        return $sentencia;
    }



    /**
     * Función del modelo Libro que sirve para validar la fecha de lanzamiento
     * la fecha de lanzamiento debe ser anterior al 1 de enero de 2007 
     * ni posterior a la fecha del día de hoy 
     * Si no cumple está condición se guarda un error con el mensaje indicado
     * 
     * @return Void no devuelve nada
     */
    public function validaFecha ():void{//hacerlo entre el 01/01/2007 y la fecha de hoy

        $fecha_lanzamiento = DateTime::createFromFormat("d/m/Y", $this->fecha_lanzamiento);

        $fechaHoy = new DateTime();
        $fechaLimite = new DateTime("2007-01-01");

        if ($fechaLimite > $fecha_lanzamiento){
            $this->setError("fecha_lanzamiento", "La fecha de lanzamiento no puede ser anterior
            al 1 de enero de 2007");
        }


        if ($fecha_lanzamiento > $fechaHoy){
            $this->setError("fecha_lanzamiento",
                        "La fecha de lanzamiento no puede ser posterior al día de hoy");
        }
    }

    /**
     * Función que valida el código de ISBN-13
     * a través de una expresión regular
     * 
     * se valida si la cadena que se recibe tiene longitud de 17
     * 
     * y si cumple con el formato 978-NN-NNN-NNNN-NN
     *
     * @return Void, no devuelve nada
     */
    public function validaISBN13(): void {


        //primero validamos longitud, el isbn tiene 13 numeros y 4 guiones, debe tener longitud 17
        if (mb_strlen ($this->isbn) < 17 || mb_strlen ($this->isbn) > 17){
            $this->setError("isbn", "EL ISBN debe tener una longitud de 17 caracteres, 13 números y 4 guiones");
        }


        //Ahora validamos formato con expresión regular
        $exReg = "/([9]{1}[7]{1}[8]{1}-[0-9]{2}-[0-9]{3}-[0-9]{4}-[0-9]{1})/";

        if (!preg_match_all($exReg, $this->isbn)){
            $this->setError("isbn", "Formato de ISBN incorrecto debe ser: 978-NN-NNN-NNNN-NN");
        }

    }




    /**
     * Método de clase del modelo Libros, se llama a la API editoriales, en funcion de un parámetro
     * este puede ser un entero (cod_editorial) o un null
     * 
     * Si es null, se llamará a la api y se devolverá un array en clave valor con cod_editorial = nombre
     * 
     * Si es un entero, se llama a la api y se devolvera el nombre correspondiente a ese cod_editorial
     * en caso de no existir se devuelve false, si hay problemas en la llamada de la api también se devolverá
     * false
     *
     * @param Integer |Null $cod_editorial
     * @return  Array -> si no recibe parámetros |String -> si existe cod_editorial |
     *             False-> si no existe el cod_editorial o si hubo problemas en la llamada de la API
     */
    public static function dameEditorial (?int $cod_editorial = null): String | Array | False{

        $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
        $arrayEditoriales = [];

        if ($cod_editorial === null){ //si no recibimos nada, devolvemos todas las editoriales
            
            $editoriales = CGeneral::getCURL($link , "GET");

            //comprobamos si ha habido errores
            if ($editoriales === false){
                return false;
            }

            $editoriales = json_decode($editoriales, true);
            if (!isset($editoriales["correcto"])){
                return false;
            }


            if (!$editoriales["correcto"]){

                return false;
            }

            foreach ($editoriales["datos"] as $clave => $valor){

                $arrayEditoriales[intval($valor["cod_editorial"])] = $valor["nombre"];
               
            }


            return $arrayEditoriales;
        }


        if (is_int($cod_editorial)){ //Si nos llega un entero, devolvemos el nombre de editorial o false si no existe
            
            if ($cod_editorial === 0){
                return false;
            }

            $editorial = CGeneral::getCURL($link , "GET", "cod_editorial=$cod_editorial");


             //comprobamos si ha habido errores
             if ($editorial === false){
                return false;
            }

            $editorial = json_decode($editorial, true);
            if (!isset($editorial["correcto"])){
                return false;
            }


            if (!$editorial["correcto"]){

                return false;
            }


            if (count ($editorial["datos"]) === 0){
                return false;
            }
            else{
                return $editorial["datos"][0]["nombre"];
            }
            
        }
        

        
    }
}
?>