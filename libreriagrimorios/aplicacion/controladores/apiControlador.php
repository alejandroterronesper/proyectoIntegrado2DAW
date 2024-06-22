<?php


/**
 * Clase para el controlador de la API, este controlador tendrá la acción
 * editorialAPI que contiene los diferentes métodos de la petición CURL
 * GET (CONSULTA), POST (INSERTAR), PUT (MODIFICAR), DELETE  (ELIMINAR)
 * 
 *  
 */
class apiControlador extends CControlador {


  
    /**
     * Acción para la API de la tabla editoriales
     * esta contiene los diferentes métodos de la petición CURL: 
     *  - GET -> consulta
     *  - POST -> insertar, si viene con parametro oper, si es 1 se hace PUT, si es 2 se hace DELETE
     *  - PUT -> modificar 
     *  - DELETE -> delete
     * @return void
     */
    public function accionEditorialAPI (){


        //Construimos la sentencia de consulta
        $sentenciaEditoriales = "SELECT * FROM editoriales";
        $selectWhere  =""; //en caso de las consultas por GET para poder filtrar
        $errores = []; //array de los posibles errores que enviamos al cliente

        //PETICION GET (CONSULTA)
        if ($_SERVER["REQUEST_METHOD"] === "GET"){


            //COALESCE
            if (isset($_GET["coalesce"])){

                $coalesce = trim( $_GET["coalesce"]);
                $coalesce = CGeneral::addslashes($coalesce);

                if ($coalesce === "si"){

                    $sentenciaEditoriales = str_replace("*", " coalesce(count(*), 0) as numero  ", $sentenciaEditoriales);
                    $_GET["limite"] ="";
                }
            }


            //COD_EDITORIAL
            if (isset($_GET["cod_editorial"])){


                $id = intval($_GET["cod_editorial"]);

                $selectWhere .= " WHERE cod_editorial = $id";
            }


            //NOMBRE
            if (isset($_GET["nombre"])){

                $nombre = trim($_GET["nombre"]);
                $nombre = CGeneral::addSlashes($nombre);

                if ($selectWhere !== ""){
                    $selectWhere .= " AND nombre LIKE '%$nombre%' ";
                }
                else{
                    $selectWhere .= " WHERE nombre LIKE '%$nombre%' ";
                }
            }

            //FUNDADOR
            if (isset($_GET["fundador"])) {

                $fundador = trim($_GET["fundador"]);
                $fundador = CGeneral::addSlashes($fundador);

                if ($selectWhere !== "") {
                    $selectWhere .= " AND fundador LIKE '%$fundador%' ";
                } 
                
                else {
                    $selectWhere .= " WHERE fundador LIKE '%$fundador%' ";
                }
            }


            //CESE
            if (isset($_GET["cese"])){

                $cese = intval($_GET["cese"]);


                if ($cese !== -1){

                    if ($selectWhere !== ""){
                        $selectWhere .= " AND cese = $cese ";
                    }
                    else{
                        $selectWhere .= " WHERE cese = $cese";
                    }
                }
            }


            //orderBy por nombre de editorial
            if (isset($_GET["orderBy"])) {

                $orderBy = intval(($_GET["orderBy"]));

                if ($orderBy === 0) { //ASC
                    $selectWhere .= "   order by nombre ASC  ";
                }

                if ($orderBy === 1) {//DESC
                    $selectWhere .= "   order by nombre DESC  ";
                }
            }


            //orderBy por fecha de fundación
            if (isset($_GET["orderByDate"])){


                $orderByDate = intval(($_GET["orderByDate"]));

                if ($orderByDate === 0) { //ASC
                    $selectWhere .= "   order by fecha_creacion ASC  ";
                }

                if ($orderByDate === 1) {//DESC
                    $selectWhere .= "   order by fecha_creacion DESC  ";
                }

            }


            //LIMIT
            if (isset($_GET["limite"])){
                $limite = trim($_GET["limite"]);
                $limite = CGeneral::addSlashes($limite);

                if ($limite !== ""){

                    $selectWhere .= "   limit  $limite";

                }

            }




            $sentenciaEditoriales .= $selectWhere;
            $filas = [];
            $consulta = Sistema::app()->BD()->crearConsulta($sentenciaEditoriales);


            //Si hay errores en la consulta, mandamos a página de error
            if($consulta->error() != 0){
                
                $resultado = [
                    "datos" => "Problemas en el acceso de la base de datos",
                    "correcto" => false
                ];

                $res = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $res;
                exit;
                
            }


            //No hay errores
            $filas = $consulta->filas();
            $resultado = [
                "datos" => $filas,
                "correcto" => true
            ];

            $res = json_encode($resultado, JSON_PRETTY_PRINT);
            echo $res;
            exit;
        }
        

        //PETICION POST INSERCCION (insert) o PUT/DELETE con oper
        if ($_SERVER["REQUEST_METHOD"] === "POST"){

            $oper = "";
            
            //podemos hacer peticion put/delete
            // desde POST con parametros 1 y 2
            if (isset($_POST["oper"])){ 

                $oper = intval($_POST["oper"]);


                if ($oper === 1){ //PETICIÓN PUT update
                    // funcion
                    $this->peticionPUT($_POST, $errores);
                }


                if ($oper === 2){ //PETICION DELETE
                    //FUNCION
                    $this->peticionDELETE($_POST, $errores);

                }

            }
            else{

                
                $nombre = "";
                if (isset($_POST["nombre"])){

                    $nombre = trim($_POST["nombre"]);
                    $nombre = CGeneral::addSlashes($nombre);
        
                    if ($nombre === ""){
                        $errores["nombre"][] = "El nombre de la editorial no puede ir vacío";
                    }

             
                    if (mb_strlen($nombre) > 30) {
                        $errores["nombre"][] = "El nombre de la editorial no puede superar los 30 caracteres";
                    }


                    //Se comprueba que el nombre de la editorial sea única
                    $consulta = Sistema::app()->BD()->crearConsulta("SELECT * FROM editoriales where nombre = '$nombre'");


                    if ($consulta->error() != 0){ //Se comprueba si hubo errores en la petición

                        $resultado = [
                            "datos" => "Error en la base de datos",
                            "correcto" => false
                        ];
                        $res = json_encode($resultado, JSON_PRETTY_PRINT);
                        echo $res;
                        exit;
                    }

                    if ($consulta->numFilas() > 0){ //Si devuelve mas de 0 filas, el nombre es repetido
                        
                        $errores["nombre"][] = "Este nombre ya lo tiene una editorial, escribe otro distinto";

                    }
                }

                //fundador
                $fundador = "";
                if (isset($_POST["fundador"])) {

                    $fundador = trim($_POST["fundador"]);
                    $fundador = CGeneral::addSlashes($fundador);


                    if ($fundador === "") {
                        $errores["fundador"][] = "El nombre del fundador no puede ir vacio";
                    }


                    if (mb_strlen($fundador) > 30) {
                        $errores["fundador"][] = "El nombre del fundador no puede superar los 30 caracteres";
                    }
                }



                //fecha creacion
                $fecha_creacion = "";
                if (isset($_POST["fecha_creacion"])) {

                    $fecha_creacion = trim($_POST["fecha_creacion"]);
                    $fecha_creacion = CGeneral::addSlashes($fecha_creacion);

                    if ($fecha_creacion === "") {
                        $errores["fecha_creacion"][] = "La fechad de fundación  no puede ir vacía";
                    }

                    if (!CValidaciones::validaFecha($fecha_creacion)) {
                        $errores["fecha_creacion"][] = "Formato de fecha incorrecta, debe ser dd/mm/aaaa";
                    }

                    //La fecha no puede ser posterior al día de hoy
                    $fechaDate = DateTime::createFromFormat("d/m/Y", $fecha_creacion);

                    $fechaHoy = new DateTime();

                    if ($fechaDate > $fechaHoy) {
                        $errores["fecha_creacion"][] = "La fecha de creación, no puede ser posterior a la fecha de hoy";
                    }
                }



                //historia
                $historia = "";
                if (isset($_POST["historia"])) {


                    $historia = trim($_POST["historia"]);
                    $historia = CGeneral::addSlashes($historia);


                    if ($historia === "") {
                        $errores["historia"][] = "El campo de historia no puede ir vacío";
                    }

                    if (mb_strlen($historia) > 230) {
                        $errores["historia"][] = "El campo historia no puede superar los 230 caracteres";
                    }
                }


                //cese
                $cese = -1;
                if (isset($_POST["cese"])){
                    $cese = intval($_POST["cese"]);

                    if (!CValidaciones::validaEntero($cese, 0, 0, 1)){
                        $errores["deleteProducto"][] = "Debe seleccionar entre una de las dos opciones";
                    }

                }

                //logo
                $logo = "";
                if (isset($_POST["logo"])){

                    $logo = trim($_POST["logo"]);
                    $logo = CGeneral::addslashes($logo);

                    if ($logo === ""){
                        $errores["logo"][] = "Debes introducir el nombre del logo";
                    }
                }

    
                if (count($errores) === 0){

                    //pasamos fecha a mysql 
                    $fecha_creacion = CGeneral::fechaNormalAMysql($fecha_creacion);

                    //Generamos sentencia insert
                    $insertEditorial = "INSERT INTO `editoriales` (`nombre`, `historia`, `fecha_creacion`,
                                                                `cese`, `fundador`, `logo`)
                                        VALUES ('$nombre', '$historia', '$fecha_creacion',
                                                 $cese, '$fundador', '$logo')";
                    
                    $ejecutaINSERT = Sistema::app()->BD()->crearConsulta($insertEditorial);

                    if ($ejecutaINSERT->error() != 0){
                        $resultado = [
                            "datos" => "Error al procesar la petición",
                            "correcto" => false
                        ];
                        $res = json_encode($resultado, JSON_PRETTY_PRINT);
                        echo $res;
                        exit;
                    }


                    //Si llegamos aqui, no hubo errores al insertar editorial
                    //Como respuesta, enviamos el id insertado de la editorial

                    $id = $ejecutaINSERT->idGenerado(); 

                    $resultado = [
                        "datos" => $id,
                        "correcto" => true
                    ];

                    $res = json_encode($resultado, JSON_PRETTY_PRINT);
                    echo $res;
                    exit;

                    
                }
                else{ //Si hay errores, enviamos array con errores

                    $resultado = [
                        "datos" => $errores,
                        "correcto" => false
                    ];
        
                    $res = json_encode($resultado, JSON_PRETTY_PRINT);
                    echo $res;
                    exit;

                }

            }
        }


        //Petición de modificación PUT
        if ($_SERVER["REQUEST_METHOD"] == "PUT"){


            //PUT -> MODIFICACION
            //recogemos los parametros
            $parametros = $this->recogerParametros();
            $this->peticionPUT($parametros, $errores);

        }


        //Petición de borrado DELETE
        if ($_SERVER["REQUEST_METHOD"] == "DELETE"){


            //DELETE -> Eliminar
            //recogemos parámetros
            $parametros = $this->recogerParametros();
            $this->peticionDELETE($parametros, $errores);

        }

       
        


    }



    /**
     * Función que recoge parámetros
     * de un .php se usa para las peticiones PUT y 
     * DELETE
     *
     * @return Array $par -> array de clave valor 
     *      nombre del parámetro : valor parámetro
     */
    private static   function recogerParametros(): Array
    {
        //recojo los parámetros
        $ficEntrada = fopen("php://input", "r");
        $datos = "";
        while ($leido = fread($ficEntrada, 1024)) {
            $datos .= $leido;
        }
        fclose($ficEntrada);
        //convierto los datos en variables
        $par = [];
        $partes = explode("&", $datos);
        foreach ($partes as $parte) {
            $p = explode("=", $parte);
            if (count($p) == 2)
                $par[$p[0]] = $p[1];
        }
        return $par;
    }



    /**
     * Método privado estático para hacer la petición put
     * se usará para el request method put y para el request method post 
     * cuando el parametro oper sea igual a 1, recibe como parámetros
     * un array con los diferentes parámetros para realizar la petición
     * y un array de errores, para los posibles errores de la petición
     * 
     * Los parámetros recibidos del array se irán verificando uno por uno
     * en caso de error, se almacenará en el array de errores
     * cuando se validen todos los parámetros se comprueba si hay errores,
     * en caso de no haberlos se realizará la insercción a la BBDD, y se devuelve en JSON
     * el id insertado
     * 
     * en caso de error, se devuelve array de errores
     *
     * @param Array $parametros array con los parámetros
     * @param Array $errores array para rellenar con los posibles errores
     * @return Void no devuelve nada, al finalizar hace echo del array 
     */
    private static function peticionPUT (array $parametros, array &$errores): void{

        //comprobamos que la editorial a modificar existe
        if (isset($parametros["cod_editorial"])){


            $id = intval($parametros["cod_editorial"]);

            //Ejecutamos consulta
            $consulta = Sistema::app()->BD()->crearConsulta("SELECT * FROM editoriales WHERE cod_editorial = $id");

            if ($consulta->error() != 0){

                $resultado = [
                    "datos" => "Problema en la base de datos",
                    "correcto" => false
                ];

                $resultado = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $resultado;
                exit;
            }

            if ($consulta->numFilas() === false){//si no se devuelve fila, da false
                $resultado = [
                    "datos" => "No existe editorial con el id indicado",
                    "correcto" => false
                ];

                $resultado = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $resultado;
                exit;
            }
        }
  


        //fundador
        $fundador = "";
        if (isset($parametros["fundador"])) {

            $fundador = trim($parametros["fundador"]);
            $fundador = CGeneral::addSlashes($fundador);


            if ($fundador === "") {
                $errores["fundador"][] = "El nombre del fundador no puede ir vacio";
            }


            if (mb_strlen($fundador) > 30) {
                $errores["fundador"][] = "El nombre del fundador no puede superar los 30 caracteres";
            }
        }



        //fecha creacion
        $fecha_creacion = "";
        if (isset($parametros["fecha_creacion"])) {

            $fecha_creacion = trim($parametros["fecha_creacion"]);
            $fecha_creacion = CGeneral::addSlashes($fecha_creacion);

            if ($fecha_creacion === "") {
                $errores["fecha_creacion"][] = "La fechad de fundación  no puede ir vacía";
            }

            if (!CValidaciones::validaFecha($fecha_creacion)) {
                $errores["fecha_creacion"][] = "Formato de fecha incorrecta, debe ser dd/mm/aaaa";
            }

            //La fecha no puede ser posterior al día de hoy
            $fechaDate = DateTime::createFromFormat("d/m/Y", $fecha_creacion);

            $fechaHoy = new DateTime();

            if ($fechaDate > $fechaHoy) {
                $errores["fecha_creacion"][] = "La fecha de creación, no puede ser posterior a la fecha de hoy";
            }
        }



        //historia
        $historia = "";
        if (isset($parametros["historia"])) {


            $historia = trim($parametros["historia"]);
            $historia = CGeneral::addSlashes($historia);


            if ($historia === "") {
                $errores["historia"][] = "El campo de historia no puede ir vacío";
            }

            if (mb_strlen($historia) > 230) {
                $errores["historia"][] = "El campo historia no puede superar los 230 caracteres";
            }
        }


        //cese
        $cese = -1;
        if (isset($parametros["cese"])){
            $cese = intval($parametros["cese"]);

            if (!CValidaciones::validaEntero($cese, 0, 0, 1)){
                $errores["deleteProducto"][] = "Debe seleccionar entre una de las dos opciones";
            }

        }

        if (count($errores) === 0) { //Se comprueba si hay errores

            //convertimos la fecha a mysql
            $fecha_creacion = CGeneral::fechaNormalAMysql($fecha_creacion);

            //construimos sentencia update
            $updateEditorial = "UPDATE `editoriales`
                                SET `fundador` = '$fundador',
                                    `historia` = '$historia', `fecha_creacion` = '$fecha_creacion',
                                    `cese` = $cese
                                    WHERE cod_editorial = $id";

            $ejecutaUPDATE = Sistema::app()->BD()->crearConsulta($updateEditorial);
            

            if ($ejecutaUPDATE->error() != 0){

                $resultado = [
                    "datos" => "Error al procesar la petición",
                    "correcto" => false
                ];
                $res = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $res;
                exit;

            }

            //Si no ha habido errores, se ha realizado la petición update
            //entonces mandamos como respuesta el id de editorial
            //actualizada 
            $resultado = [
                "datos" => $id,
                "correcto" => true
            ];

            $res = json_encode($resultado, JSON_PRETTY_PRINT);
            echo $res;
            exit;

        } 
        else { //Si hay errores, no se hace update


            $resultado = [
                "datos" => $errores,
                "correcto" => false
            ];

            $res = json_encode($resultado, JSON_PRETTY_PRINT);
            echo $res;
            exit;
        }


    }



    /**
     * Método privado estático para hacer la petición DELETE, se hace un borrado LÓGICO,
     * se usará para el request method DELETE y para el request method post 
     * cuando el parametro oper sea igual a 2, recibe como parámetros
     * un array con los diferentes parámetros para realizar la petición
     * y un array de errores, para los posibles errores de la petición
     * 
     * Los parámetros recibidos del array se irán verificando uno por uno
     * en caso de error, se almacenará en el array de errores
     * cuando se validen todos los parámetros se comprueba si hay errores,
     * en caso de no haberlos se realizará la insercción a la BBDD, y se devuelve en JSON
     * el id insertado
     * 
     * en caso de error, se devuelve array de errores
     *
     * @param Array $parametros array con los parámetros
     * @param Array $errores array para rellenar con los posibles errores
     * @return Void no devuelve nada, al finalizar hace echo del array 
     */
    private static function peticionDELETE (array $parametros, array &$errores): void{


        //primero comprobamos que existe el id de la editorial
        //a eliminar
        $id = "";
        if (isset($parametros["cod_editorial"])){
            $id = intval($parametros["cod_editorial"]);

            $sentenciaEDITORIAL = "SELECT * FROM editoriales WHERE cod_editorial = $id";
            $ejecutaSentencia = Sistema::app()->BD()->crearConsulta($sentenciaEDITORIAL);

            if ($ejecutaSentencia->error() != 0){ //Se comprueba si hubo errores
                $resultado = [
                    "datos" => "Error en la base de datos",
                    "correcto" => false
                ];
                $res = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $res;
                exit;
            }



            if ($ejecutaSentencia->numFilas() === 0){

                $errores["cese"][] = "No existe una editorial con el id indicado";
            }


            $cese = "";
            if (isset($parametros["cese"])){

                $cese = intval($parametros["cese"]);


                if (!CValidaciones::validaEntero($cese, 0, 0, 1)){
                    $errores["cese"][] = "Debe elegir una de las opciones disponibles";

                }
            }

            if ( count($errores) === 0){


                //Hacemos un borrado lógico a la tabla editoriales
                

                $deleteEditorial = "UPDATE `editoriales`
                                    SET `cese` = $cese
                                    WHERE cod_editorial = $id";

                $ejecutaDELETE = Sistema::app()->BD()->crearConsulta($deleteEditorial);
                

                if ($ejecutaDELETE->error() != 0){
                    $resultado = [
                        "datos" => "Error al procesar la petición",
                        "correcto" => false
                    ];
                    $res = json_encode($resultado, JSON_PRETTY_PRINT);
                    echo $res;
                    exit;
                }


                //Si hemos llegado aqui, se ha realizado bien la ejecución de la sentencia
                //enviamos como respuesta el id de la editorial modificada
                $resultado = [
                    "datos" => $id,
                    "correcto" => true
                ];
                $res = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $res;
                exit;

                
                                    
            }
            else{
                //si hay errores devolvemos correcto = false y datos => errores
                $resultado = [
                    "datos" => $errores,
                    "correcto" => false
                ];

                $res = json_encode($resultado, JSON_PRETTY_PRINT);
                echo $res;
                exit;
            }

        }

    }
}
?>