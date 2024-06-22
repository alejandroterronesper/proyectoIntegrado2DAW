<?php

/**
 * Clase para el controlador editoriales
 * se recogen las diferentes acciones englobadas a las editoriales
 * 
 * los diferentes datos que se muestran se sacan desde la API, es decir, 
 * no atacamos directamente a la base de datos y se comprobaran previamente
 * antes de mostrarse.
 */
class editorialesControlador extends CControlador {



    /**
     * Acción index, se encarga de mostrar el segundo
     * index de la página, mostrará las editoriales en forma
     * de página. 
     * 
     * En esta acción además, se cuenta con un filtrado y un paginador
     * Las vistas parciales mostradas de las editoriales se obtienen a partir de 
     * una petición CURL con el parámetro GET
     *
     * @return Void no se devuelve nada, se imprime la vista
     */
    public function accionIndex (){

        //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Editoriales",
                "url" => ["editoriales", "index"]
            ]
	  	];

        //se guarda en sesión, acción actual
        $_SESSION["anterior"] = ["editoriales", "index"];


        //filtrado de index editoriales
        if (!isset($_SESSION["arrayFiltradoEditoriales"])){
            $_SESSION["arrayFiltradoEditoriales"] = [
                "nombre" => "",
                "fundador" => "",
                "cese" => -1,
                "orderBy" => -1,
                "parametros" => ""
            ];
        }


        //datos de filtrado
        $datos = [
            "nombre" => $_SESSION["arrayFiltradoEditoriales"]["nombre"],
            "fundador" =>   $_SESSION["arrayFiltradoEditoriales"]["fundador"],
            "cese" => $_SESSION["arrayFiltradoEditoriales"]["cese"],
            "orderBy" => $_SESSION["arrayFiltradoEditoriales"]["orderBy"]
        ];
        

        //post del filtrado
        if ($_POST){

            

            if (isset($_POST["filtraDatosEdPrincipal"])){
                
                $nombre = "";
                if(isset($_POST["nombre"])){
                    $nombre = trim($_POST["nombre"]);
                    $nombre = CGeneral::addslashes($nombre);

                    
                }
                if ($nombre !== ""){
                    $datos["nombre"] = $nombre;
                }
               

                $fundador = "";
                if(isset($_POST["fundador"])){
                    $fundador = trim($_POST["fundador"]);
                    $fundador  = CGeneral::addslashes($fundador);

              

                }
                if ($fundador !== ""){
                    $datos["fundador"] = $fundador;
                }


                $cese = -1;
                if (isset($_POST["cese"])){

                    $cese = intval($_POST["cese"]);
                }


                if ($cese !== -1){
                    $datos["cese"] = $cese;
                }

                $orderBy = -1;
                if (isset($_POST["orderBy"])){

                    $orderBy = intval($_POST["orderBy"]);
                }
                if ($orderBy !== -1){
                    $datos["orderBy"] = $orderBy;
                }


            }


            if (isset($_POST["limpiaFiltradoEdPrincipal"])){

                $datos["nombre"] = "";
                $datos["fundador"] = "";
                $datos["cese"] = -1;
                $datos["orderBy"] = -1;
                $parametros = "";
                
            }

            //actualizamos sesion de filtrado
            $_SESSION["arrayFiltradoEditoriales"] = [
                "nombre" => $datos["nombre"],
                "fundador" => $datos["fundador"],
                "cese" => $datos["cese"],
                "orderBy" => $datos["orderBy"]
            ];

        }

        $numPaginas = 0;
		$numProductos = 4; 
		$limite = "";
		$paginaActual = 1;

		if (isset($_GET["reg_pag"]) && isset($_GET["pag"])){
			$paginaActual = intval($_GET["pag"]);   
			$numProductos = intval($_GET["reg_pag"]);
			$numPaginas = $numProductos * ($paginaActual - 1);
			$limite = $numPaginas.",".$numProductos;
		}
		else{
			$paginaActual = 1;
			$limite = $numPaginas.",". $numProductos;
		}


        //construimos sentencia de parámetros 
        //para mandarla a la api con el GET
        $parametros = "";
        $cont = 0;
        foreach ($datos as $clave => $valor){
            
            if ($cont  === (count ($datos) -1)){
               if ($valor !== "" && $valor !== -1){
                    $parametros .= "$clave=$valor";

                }
            }
            else{
                if ($valor !== "" && $valor !== -1){
                    $parametros .= "$clave=$valor&";
                }
            }            
        
            $cont ++;
        }
 

        //quitamos el & del final, en caso de haberlo
        $parametros = rtrim($parametros, "&");


        $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);

        if ($parametros !== ""){
            
            $parametros .= "&limite=$limite"; 
            $editoriales = CGeneral::getCURL($link , "GET", $parametros);

        }
        else{

            $parametros .= "limite=$limite";
            $editoriales = CGeneral::getCURL($link , "GET", $parametros);
        }


        
        $datos["parametros"] = $parametros;
        if (isset($_SESSION["arrayFiltradoEditoriales"]["parametros"])){ //actualizamos sesión
            $_SESSION["arrayFiltradoEditoriales"]["parametros"] = $datos["parametros"];
        }
        
        //comprobamos si ha habido errores
        if ($editoriales === false){
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $editoriales = json_decode($editoriales, true);
        if (!isset($editoriales["correcto"])){

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$editoriales["correcto"]){

            Sistema::app()->paginaError(505, $editoriales["datos"]);
            exit;
        }

        if (isset($_SESSION["arrayFiltradoEditoriales"]["parametros"])
             && ($_SESSION["arrayFiltradoEditoriales"]["parametros"] !== "")){

            $parametros = $_SESSION["arrayFiltradoEditoriales"]["parametros"];
            
        } 


        //opciones del paginador
        $totalRegistros = CGeneral::getCURL($link , "GET", $parametros."&coalesce=si");
  
        //comprobamos si ha habido errores
        if ($totalRegistros === false){
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $totalRegistros = json_decode($totalRegistros, true);
        if (!isset($totalRegistros["correcto"])){

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$totalRegistros["correcto"]){

            Sistema::app()->paginaError(505, $totalRegistros["datos"]);
            exit;
        }



        $tRegistros =  intval($totalRegistros["datos"][0]["numero"]);

    


        $opcPaginador = array(
            "URL" => Sistema::app()->generaURL(array("editoriales", "index")),
            "TOTAL_REGISTROS" => $tRegistros,
            "PAGINA_ACTUAL" => $paginaActual,
            "REGISTROS_PAGINA" => $numProductos,
            "TAMANIOS_PAGINA" => array(
                5 => "5",
                10 => "10",
                20 => "20",
                30 => "30",
                40 => "40",
                50 => "50"
            ),
            "MOSTRAR_TAMANIOS" => true,
            "PAGINAS_MOSTRADAS" => 7,
        );


        $filas = $editoriales["datos"];


        //formateamos parámetros
        foreach($filas as $clave => $valor){

            $filas[$clave]["fecha_creacion"] = CGeneral::fechaMysqlANormal($valor["fecha_creacion"]);
            

        }

       
        $this->dibujaVista("index", ["filas" => $filas, "paginador" => $opcPaginador, "datos" => $datos], "Grimorios - Editoriales");
    }


    /**
     * Acción del CRUD de editoriales,
     * muestra una vista con una tabla con las diferentes editoriales
     * contiene un campo con la infomación de cada, tiene un campo de operaciones del crud 
     * para la editorial correspondiente; ver, modificar y borrar,
     *  además de la opción añadir editoriales
     *
     * Los datos se obtienen a partir del controlador de la API
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionEditorialCRUD (){

        $nickUserActual = Sistema::app()->Acceso()->getNick();
        $codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
        $borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);


        //Guardo acción actual en sesión
        $_SESSION["anterior"] = ["editoriales", "editorialCRUD"];

        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //Para acceder al CRUD de editoriales necesitas el permiso 9
            if (!Sistema::app()->Acceso()->puedePermiso(9)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }


        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }

       //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Crud de editoriales",
                "url" => ["editoriales", "EditorialCRUD"]
            ]
	  	];




        if (!isset($_SESSION["arrayFiltradoCrudEditoriales"])) {
            $_SESSION["arrayFiltradoCrudEditoriales"] = [
                "nombre" => "",
                "fundador" => "",
                "cese" => -1,
                "orderByDate" => -1,
                "parametros" => ""
            ];
        }

        //datos de filtrado
        $datos = [
            "nombre" => $_SESSION["arrayFiltradoCrudEditoriales"]["nombre"],
            "fundador" => $_SESSION["arrayFiltradoCrudEditoriales"]["fundador"],
            "cese" => $_SESSION["arrayFiltradoCrudEditoriales"]["cese"],
            "orderByDate" =>  $_SESSION["arrayFiltradoCrudEditoriales"]["orderByDate"]

        ];

        if ($_POST){


            if (isset($_POST["filtrarDatosCrudEd"])){

                $nombre = "";
                if(isset($_POST["nombre"])){
                    $nombre = trim($_POST["nombre"]);
                    $nombre = CGeneral::addslashes($nombre);

                    
                }
                if ($nombre !== ""){
                    $datos["nombre"] = $nombre;
                }
               

                $fundador = "";
                if(isset($_POST["fundador"])){
                    $fundador = trim($_POST["fundador"]);
                    $fundador  = CGeneral::addslashes($fundador);

              

                }
                if ($fundador !== ""){
                    $datos["fundador"] = $fundador;
                }


                $cese = -1;
                if (isset($_POST["cese"])){

                    $cese = intval($_POST["cese"]);
                }


                if ($cese !== -1){
                    $datos["cese"] = $cese;
                }

                $orderByDate = -1;
                if (isset($_POST["orderByDate"])){

                    $orderByDate = intval($_POST["orderByDate"]);
                }
                if ($orderByDate !== -1){
                    $datos["orderByDate"] = $orderByDate;
                }

            }



            if (isset($_POST["limpiarDatosCrudEd"])){
                
                $datos["nombre"] = "";
                $datos["fundador"] = "";
                $datos["cese"] = -1;
                $datos["orderByDate"] = -1;
                $parametros = "";
            }


            //actualizamos sesion de filtrado
            $_SESSION["arrayFiltradoCrudEditoriales"] = [
                "nombre" => $datos["nombre"],
                "fundador" => $datos["fundador"],
                "cese" => $datos["cese"],
                "orderByDate" => $datos["orderByDate"]
            ];
        }

        
     
        $numPaginas = 0;
		$numProductos = 4; 
		$limite = "";
		$paginaActual = 1;


        if (isset($_GET["reg_pag"]) && isset($_GET["pag"])){
			$paginaActual = intval($_GET["pag"]);   
			$numProductos = intval($_GET["reg_pag"]);
			$numPaginas = $numProductos * ($paginaActual - 1);
			$limite = $numPaginas.",".$numProductos;
		}
		else{
			$paginaActual = 1;
			$limite = $numPaginas.",". $numProductos;
		}


        //construimos sentencia de parámetros 
        //para mandarla a la api con el GET
        $parametros = "";
        $cont = 0;
        foreach ($datos as $clave => $valor){
            
            if ($cont  === (count ($datos) -1)){
               if ($valor !== "" && $valor !== -1){
                    $parametros .= "$clave=$valor";

                }
            }
            else{
                if ($valor !== "" && $valor !== -1){
                    $parametros .= "$clave=$valor&";
                }
            }            
        
            $cont ++;
        }

        //quitamos el & del final, en caso de haberlo
        $parametros = rtrim($parametros, "&");

        $link = $_SERVER["HTTP_HOST"] . "" . Sistema::app()->generaURL(["api", "editorialAPI"]);
     
        if ($parametros !== ""){
            
            $parametros .= "&limite=$limite"; 
            $editoriales = CGeneral::getCURL($link , "GET", $parametros);

        }
        else{

            $parametros .= "limite=$limite";
            $editoriales = CGeneral::getCURL($link , "GET", $parametros);
        }




        $datos["parametros"] = $parametros;
        if (isset($_SESSION["arrayFiltradoCrudEditoriales"]["parametros"])){ //actualizamos sesión
            $_SESSION["arrayFiltradoCrudEditoriales"]["parametros"] = $datos["parametros"];
        }

 

        //comprobamos si ha habido errores
        if ($editoriales === false) {
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $editoriales = json_decode($editoriales, true);
        if (!isset($editoriales["correcto"])) {

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$editoriales["correcto"]) {

            Sistema::app()->paginaError(505, $editoriales["datos"]);
            exit;
        }


        if (isset($_SESSION["arrayFiltradoCrudEditoriales"]["parametros"])
             && ($_SESSION["arrayFiltradoCrudEditoriales"]["parametros"] !== "")){

            $parametros = $_SESSION["arrayFiltradoCrudEditoriales"]["parametros"];
            
        }       



        $totalRegistros = CGeneral::getCURL($link , "GET", $parametros."&coalesce=si");

        //comprobamos si ha habido errores
        if ($totalRegistros === false){
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $totalRegistros = json_decode($totalRegistros, true);
        if (!isset($totalRegistros["correcto"])){

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$totalRegistros["correcto"]){

            Sistema::app()->paginaError(505, $totalRegistros["datos"]);
            exit;
        }

        //Se sacan el nº de registros totales de la consulta
        $tRegistros =  intval($totalRegistros["datos"][0]["numero"]);



        //opciones del paginador
        $opcPaginador = array(
            "URL" => Sistema::app()->generaURL(array("editoriales", "editorialCRUD")),
            "TOTAL_REGISTROS" => $tRegistros,
            "PAGINA_ACTUAL" => $paginaActual,
            "REGISTROS_PAGINA" => $numProductos,
            "TAMANIOS_PAGINA" => array(
                5 => "5",
                10 => "10",
                20 => "20",
                30 => "30",
                40 => "40",
                50 => "50"
            ),
            "MOSTRAR_TAMANIOS" => true,
            "PAGINAS_MOSTRADAS" => 7,
        );

        $filas = $editoriales["datos"];


        //formateamos parámetros
        foreach($filas as $clave => $valor){

            $filas[$clave]["historia"] = CHTML::botonHtml("Ver información", ["class" => "boton", "onclick" => "verDescripcionEditorial({$filas[$clave]['cod_editorial']})"]);
            $filas[$clave]["fecha_creacion"] = CGeneral::fechaMysqlANormal($valor["fecha_creacion"]);
            $filas[$clave]["logo"] = CHTML::imagen("/imagenes/logosEditoriales/". $filas[$clave]["logo"], "Logo de {$filas[$clave]['nombre']}", ["class" => "imgTabla", "style" => "margin-top: 2%"]);
            $filas[$clave]["oper"] = CHTML::link(CHTML::imagen("/imagenes/24x24/ver.png", "", ["title" => "Ver editorial"]), Sistema::app()->generaURL(["editoriales","verEditorial"], ["id" => $filas[$clave]["cod_editorial"]])). " ".
							CHTML::link(CHTML::imagen("/imagenes/24x24/modificar.png", "", ["title" => "Modificar editorial"]), Sistema::app()->generaURL(["editoriales","modificarEditorial"], ["id" => $filas[$clave]["cod_editorial"]]));


            //Se comprueba si está en cese o no, para añadirle la opción de borrado lógico
            if (intval($filas[$clave]["cese"]) === 0){
                $filas[$clave]["oper"] .= CHTML::link(CHTML::imagen("/imagenes/24x24/borrar.png", "", ["title" => "Borrar producto"]), Sistema::app()->generaURL(["editoriales","eliminarEditorial"], ["id" => $filas[$clave]["cod_editorial"]]));

                $filas[$clave]["cese"] = CHTML::imagen("/imagenes/abierto.png", "cartel de abierto");

            }


            if (intval($filas[$clave]["cese"]) === 1){
                $filas[$clave]["cese"] = CHTML::imagen("/imagenes/cese.png", "cartel de cerrado");

            }
        }

        $cabecera = [
            ["ETIQUETA" => "Nombre",
            "CAMPO" => "nombre"],
            ["ETIQUETA" => "Historia",
            "CAMPO" => "historia"],
            ["ETIQUETA" => "Fecha de creación",
            "CAMPO" => "fecha_creacion"],
            ["ETIQUETA" => "Cese",
            "CAMPO" => "cese"],
            ["ETIQUETA" => "Fundador",
            "CAMPO" => "fundador"],
            ["ETIQUETA" => "Logo",
            "CAMPO" => "logo"],
            [
                "ETIQUETA" => "Operaciones",
                "CAMPO" => "oper"
            ]
        ];

       
        $this->dibujaVista("crudEditoriales", ["cabecera" => $cabecera ,"filas" => $filas, "paginador" => $opcPaginador, "datos" => $datos], "Grimorios - Editoriales CRUD");
    }



    /**
     * Acción que se encarga de mostrar
     * todos los datos de una editorial en específico,
     * para obtener los datos se hace un petición CURL
     * con parámetro GET a la API, los campos a mostrar no 
     * son editables, además se incluyen otras operaciones del CRUD
     * de la editorial correspondiente para realizar
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionVerEditorial (){

        $id = "";

        if ($_GET){
            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }
        }

        if ($id === ""){
            Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
            exit;
        }



        //COMPROBACION DE PERMISOS
        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);


        $_SESSION["anterior"] = ["editoriales", "verEditorial/id=$id"];


        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(9)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }


        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Crud de editoriales",
                "url" => ["editoriales", "EditorialCRUD"]
            ],
            [
                "texto" => "Ver Editorial",
                "url" => ["editoriales", "verEditorial/id=".$id]
            ]
	  	];


        //Hacemos petición a la API
        //Se comprueba que nos devuelve el resultado de una fila  
        //En caso de no devolver nada, se ha introducido un id inexistente
        //en la tabla de editoriales
        $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
        $editorial = CGeneral::getCURL($link , "GET", "cod_editorial=$id");

        //comprobamos si ha habido errores
        if ($editorial === false) {
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $editorial = json_decode($editorial, true);
        
        if (!isset($editorial["correcto"])) {

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$editorial["correcto"]) {

            Sistema::app()->paginaError(505, $editorial["datos"]);
            exit;
        }



        if (count($editorial["datos"]) === 0){
            Sistema::app()->paginaError(404, "No se ha encontrado ninguna editorial con el id indicado");
            exit;
        }
        else{
            $editorial = $editorial["datos"][0];

            //formateamos valores
            $editorial["fecha_creacion"] = CGeneral::fechaMysqlANormal($editorial["fecha_creacion"]);
            $editorial["cese"] = intval($editorial["cese"]);

            if ($editorial["cese"] === 0) {
                $editorial["cese"] = "No";
            } else {
                $editorial["cese"] = "Si";
            }

            $this->dibujaVista("verEditorial", ["editorial" => $editorial], "Grimorios - Ver editorial");

        }


    


    }


    /**
     * Acción que muestra los datos de una editorial determinada
     * para modificar dichos datos, los datos a modificar se pasan
     * mediante una petición CURL con parametro POST, si los datos
     * a enviar son validados correctamente, se devuelve el id de la
     * editorial modificada y lo enviamos a la acción verEditorial
     * en caso contrario se muestra esta misma acción pero con los errores
     * de cada campo
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionModificarEditorial (){

        $id = "";

        if ($_GET){
            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }
        }

        if ($id === ""){
            Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
            exit;
        }


        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);


        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["editoriales", "modificarEditorial/id=$id"];

        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(9)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }


        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Crud de editoriales",
                "url" => ["editoriales", "EditorialCRUD"]
            ],
            [
                "texto" => "Modificar Editorial",
                "url" => ["editoriales", "modificarEditorial/id=".$id]
            ]
	  	];


        //Hacemos petición a la API
        //Se comprueba que nos devuelve el resultado de una fila  
        //En caso de no devolver nada, se ha introducido un id inexistente
        //en la tabla de editoriales
        $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
        $editorial = CGeneral::getCURL($link , "GET", "cod_editorial=$id");

        //comprobamos si ha habido errores
        if ($editorial === false) {
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $editorial = json_decode($editorial, true);
        if (!isset($editorial["correcto"])) {

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$editorial["correcto"]) {

            Sistema::app()->paginaError(505, $editorial["datos"]);
            exit;
        }




        if (count($editorial["datos"]) === 0){
            Sistema::app()->paginaError(404, "No se ha encontrado ninguna editorial con el id indicado");
            exit;
        }
        else{

            $editorial = $editorial["datos"][0];


            //formateamos valores
            $editorial["fecha_creacion"] = CGeneral::fechaMysqlANormal($editorial["fecha_creacion"]);
            $editorial["cese"] = intval($editorial["cese"]);



            //parametros formulario
            $datos = [
                "cod_editorial" => $editorial["cod_editorial"],
                "nombre" => $editorial["nombre"],
                "fundador" => $editorial["fundador"],
                "fecha_creacion" => $editorial ["fecha_creacion"],
                "cese" => $editorial["cese"],
                "historia" => $editorial["historia"],
                "logo" => $editorial["logo"]
            ];


            if ($_POST){

                if (isset($_POST["inputModEditorial"])){

                    //las validaciones se hacen en la api de editoriales
                    $parametros = "";

                    $fundador = "";
                    if (isset($_POST["fundador"])){
                        $fundador = trim($_POST["fundador"]);
                    }
                    $datos["fundador"] = $fundador;
                    $parametros .= "fundador=$fundador";


                    $fecha_creacion = "";
                    if (isset($_POST["fecha_creacion"])){
                        $fecha_creacion = trim($_POST["fecha_creacion"]);
                    }
                    $datos["fecha_creacion"] = $fecha_creacion;
                    $parametros .= "&fecha_creacion=$fecha_creacion";

                    $cese = -1;
                    if (isset($_POST["cese"])){
                       $cese = intval($_POST["cese"]); 
                    }
                    $datos["cese"] = $cese;
                    $parametros .= "&cese=$cese";


                    $historia = "";
                    if (isset($_POST["historia"])){
                        $historia = trim($_POST["historia"]);
                    }
                    $datos["historia"] = $historia;
                    $parametros .= "&historia=$historia";



                    $parametros.= "&cod_editorial={$datos['cod_editorial']}";
                    //$parametros .= "&oper=1"; //si hacemos la peticion por POST

                }

                
                if (isset($_POST["inputModEditorial"]) && ($parametros !== "")){


                    //Como es modificar un elemento, hacemos petición PUT
                    $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
                    $res = CGeneral::getCURL($link, "PUT", $parametros);
                    
                    
                    //Se comprueban posibles errores de la API
                    if ($res === false){
                        Sistema::app()->paginaError(505, "No se han podido obtener los datos");
                        exit;
                    }

                    $res = json_decode($res, true);
                    if (!isset($res["correcto"])){
                        Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
                        exit;
                    }

                    if (!$res["correcto"]){ //Se comprueba si hay array de errores

                        if (is_array($res["datos"])){

                            $errores = $res["datos"]; //devolvemos el array con los errores de las validaciones de la API y los mostramos
                            $this->dibujaVista("modificarEditorial", ["datos" => $datos, "errores" => $errores], "Grimorios - Modificar editorial");
                            exit;

                        }

                        if (is_string($res["datos"])){
                            Sistema::app()->paginaError(505, "Error al procesar la petición");
                            exit;
                        }
                    }


                    if ($res["correcto"] === true){ //Llevamos al ver Editorial
                        $id = intval($res["datos"]);
                        header("location: ". Sistema::app()->generaURL(["editoriales", "verEditorial"], ["id"=>$id]));
                        exit;
                    }

                }
            }


            $this->dibujaVista("modificarEditorial", ["datos" => $datos], "Grimorios - Modificar editorial");

        }


    

    }




    /**
     * Acción para eliminar una editorial
     * se hará un borrado lógico no físico, 
     * se permite elegir al usuario con un radio button
     * a elegir entre si o no borrar la editorial
     * 
     * Cualquiera de las dos opciones se envia por petición CURL con
     * parametro DELETE, se validaran los parámetros si todo sale correctamente
     * nos llevará a la acción de ver la editorial correspondiente,
     * en caso contrario se nos muestra esta misma acción los errores 
     * de la petición
     *
     * @return Void, no se devuelve nada imprime una vista
     */
    public function accionEliminarEditorial (){


        $id = "";

        if ($_GET){
            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }
        }

        if ($id === ""){
            Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
            exit;
        }


        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);

        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["editoriales", "eliminarEditorial/id=$id"];

        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(9)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }

        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login
            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Crud de editoriales",
                "url" => ["editoriales", "EditorialCRUD"]
            ],
            [
                "texto" => "Eliminar Editorial",
                "url" => ["editoriales", "eliminarEditorial/id=".$id]
            ]
	  	];


        //Hacemos petición a la API
        //Se comprueba que nos devuelve el resultado de una fila  
        //En caso de no devolver nada, se ha introducido un id inexistente
        //en la tabla de editoriales
        $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
        $editorial = CGeneral::getCURL($link , "GET", "cod_editorial=$id");

        //comprobamos si ha habido errores
        if ($editorial === false) {
            Sistema::app()->paginaError(505, "No se han podido obtener los datos");
            exit;
        }

        $editorial = json_decode($editorial, true);
        if (!isset($editorial["correcto"])) {

            Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
            exit;
        }


        if (!$editorial["correcto"]) {

            Sistema::app()->paginaError(505, $editorial["datos"]);
            exit;
        }




        if (count($editorial["datos"]) === 0){
            Sistema::app()->paginaError(404, "No se ha encontrado ninguna editorial con el id indicado");
            exit;
        }
        else{
            $editorial = $editorial["datos"][0];



            //Se formatean los valores
            $editorial["fecha_creacion"] = CGeneral::fechaMysqlANormal($editorial["fecha_creacion"]);
            $editorial["cese"] = intval($editorial["cese"]);


            
            //Se comprueba si la editorial ha sido borrada
            //En caso de ser así, no podriamos acceder a la acción de eliminar editorial

            if ($editorial["cese"] === 1){
                Sistema::app()->paginaError(505, "La editorial seleccionada ya ha sido borrada");
                exit;
            }

            //parametros formulario
            $datos = [
                "cod_editorial" => $editorial["cod_editorial"],
                "nombre" => $editorial["nombre"],
                "fundador" => $editorial["fundador"],
                "fecha_creacion" => $editorial["fecha_creacion"],
                "cese" => $editorial["cese"],
                "historia" => $editorial["historia"],
                "logo" => $editorial["logo"]
            ];

            if ($_POST){

                if (isset($_POST["inputDelEditorial"])){


                    $parametros = "";

                    $cese = -1;
                    if (isset($_POST["cese"])){

                        $cese = intval($_POST["cese"]);
                    }

                    $parametros.= "cese=$cese";
                    $parametros.= "&cod_editorial={$datos['cod_editorial']}";
                    //$parametros .= "&oper=2"; //si hacemos la peticion por POST
                }



                if (isset($_POST["inputDelEditorial"]) && ($parametros !== "")){

                    //Eliminar elemento (lógico), hacemos petición DELETE
                    $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
                    $res = CGeneral::getCURL($link, "DELETE", $parametros);


                    //Se comprueban posibles errores de la API
                    if ($res === false){
                        Sistema::app()->paginaError(505, "No se han podido obtener los datos");
                        exit;
                    }

                    $res = json_decode($res, true);
                    if (!isset($res["correcto"])){
                        Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
                        exit;
                    }

                    if (!$res["correcto"]){ //Se comprueba si hay array de errores

                        if (is_array($res["datos"])){

                            $errores = $res["datos"]; //devolvemos el array con los errores de las validaciones de la API y los mostramos
                            $this->dibujaVista("borrarEditorial", ["datos" => $datos, "errores" => $errores], "Grimorios - Modificar editorial");
                            exit;

                        }

                        if (is_string($res["datos"])){
                            Sistema::app()->paginaError(505, "Error al procesar la petición");
                            exit;
                        }
                    }


                    if ($res["correcto"] === true){ //Llevamos al ver Editorial
                        $id = intval($res["datos"]);
                        header("location: ". Sistema::app()->generaURL(["editoriales", "verEditorial"], ["id"=>$id]));
                        exit;
                    }
                }
            }


            $this->dibujaVista("borrarEditorial", ["editorial" => $datos], "Grimorios - Eliminar editorial");

        }
    }





    /**
     * Acción para añadir una nueva editorial a la tabla
     * editoriales, se recogen los parámetros mediante POST, 
     * y se envian mediante petición CURL a la API, donde se validan
     * si todo es correcto, se envia el id de la editorial insertada
     * y se redirige a la acción de ver la editorial correspondiente
     * en caso contrario, se muestra la acción actual con los posibles errores
     * 
     * EN ESTA ACCIÓN ESTABLECEMOS LA COOKIE
     * Y VAMOS A GUARDAR EL VALOR DE UNO DE LOS CAMPOS
     * EN ESPECIFICO la fecha de creación
     *
     * @return Void, no se devuelve nada imprime una vista
     */
    public function accionAnadeEditorial (){

        //COMPROBACION DE PERMISOS
        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);

        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["editoriales", "anadeEditorial"];

        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(9)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }

        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login
            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }

        //Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
            ],
            [
                "texto" => "Crud de editoriales",
                "url" => ["editoriales", "EditorialCRUD"]
            ],
            [
                "texto" => "Añade Editorial",
                "url" => ["editoriales", "anadeEditorial"]
            ]
	  	];


        //parametros formulario
        $datos = [
            "nombre" => "",
            "fundador" => "",
            "fecha_creacion" => "",
            "cese" => 0,
            "historia" => "",
            "logo" => "logo.png"
        ];

        //Se comprueba que existe la cookie, si existe le asignamos el valor
        if (isset($_COOKIE["fecha_creacion"])){
            $datos["fecha_creacion"] = $_COOKIE["fecha_creacion"];
        }



        if ($_POST){

            if (isset($_POST["inputAddEditorial"])){

                //las validaciones se hacen desde la api editoriales
                $parametros = "";

                $nombre = "";
                if (isset($_POST["nombre"])){

                    $nombre = trim($_POST["nombre"]);
                }
                $datos["nombre"] = $nombre;
                $parametros .= "nombre=$nombre";


                $fundador = "";
                if (isset($_POST["fundador"])){
                    $fundador = trim($_POST["fundador"]);
                }
                $datos["fundador"] = $fundador;
                $parametros .= "&fundador=$fundador";


                $fecha_creacion = "";
                if (isset($_POST["fecha_creacion"])){
                    $fecha_creacion = trim($_POST["fecha_creacion"]);
                }
                $datos["fecha_creacion"] = $fecha_creacion;
                $parametros .= "&fecha_creacion=$fecha_creacion";


                $cese = -1;
                if (isset($_POST["cese"])){
                   $cese = intval($_POST["cese"]); 
                }
                $datos["cese"] = $cese;
                $parametros .= "&cese=$cese";


                $historia = "";
                if (isset($_POST["historia"])){
                    $historia = trim($_POST["historia"]);
                }
                $datos["historia"] = $historia;
                $parametros .= "&historia=$historia";


                $logo = "logo.png";
                if (isset($logo)){
                    $logo = trim($logo);
                }
                $datos["logo"] = $logo;
                $parametros .= "&logo=$logo";
            }



            if (isset($_POST["inputAddEditorial"]) && ($parametros !== "")){

                    //Como es insertar un elemento, hacemos petición POST
                    $link = $_SERVER["HTTP_HOST"]. "". Sistema::app()->generaURL(["api", "editorialAPI"]);
                    $res = CGeneral::getCURL($link, "POST", $parametros);

                    //Se comprueban posibles errores de la API
                    if ($res === false){
                        Sistema::app()->paginaError(505, "No se han podido obtener los datos");
                        exit;
                    }

                    
                    $res = json_decode($res, true);
                    if (!isset($res["correcto"])){
                        Sistema::app()->paginaError(505, "La respuesta no cumple el formato");
                        exit;
                    }

                    if (!$res["correcto"]){ //Se comprueba si hay array de errores

                        if (is_array($res["datos"])){

                            $errores = $res["datos"]; //devolvemos el array con los errores de las validaciones de la API y los mostramos
                            $this->dibujaVista("anadeEditorial", ["datos" => $datos, "errores" => $errores], "Grimorios - Añade editorial");
                            exit;

                        }

                        if (is_string($res["datos"])){
                            Sistema::app()->paginaError(505, "Error al procesar la petición");
                            exit;
                        }
                    }

                    
                    if ($res["correcto"] === true){ //Llevamos al ver Editorial
                        $id = intval($res["datos"]);

                        //Como se han validado todos los datos declaramos la cookie aqui


                        if (isset($_COOKIE["fecha_creacion"])) { //si existe la cookie fecha creacion: se actualiza
                            setcookie("fecha_creacion", $datos["fecha_creacion"] , time() + 60 * 60);
                        } 
                        
                        else { //si no existe la cookie fecha creacion: se crea
                            setcookie("fecha_creacion", $datos["fecha_creacion"] , time() + 60 * 60);
                        }


                        header("location: ". Sistema::app()->generaURL(["editoriales", "verEditorial"], ["id"=>$id]));
                        exit;
                    }
            }

        
        }


        $this->dibujaVista("anadeEditorial", ["datos" => $datos], "Grimorios - Añade editorial");
    }
}


?>