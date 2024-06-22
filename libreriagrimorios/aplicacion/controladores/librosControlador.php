<?php


/**
 * Clase para el controlador libro, contiene
 * diferentes acciones referidas al modelo de libros
 * como es el CRUD de libros
 */
class librosControlador extends CControlador {


    /**
     * Constructor que contiene la barra de ubicación
     */
    function __construct()
    {


        //Barra de ubicación
        $this->barraUbi = [
			[
			  "texto" => "Inicio ",
			  "url" => "/"
		  	],
			[
				"texto" => "Index de la tabla de libros",
				"url" => ["libros", "index"]
			]
	    ];

    }



    /**
     * Acción para el CRUD de la tabla principal libros,
     * muestra una tabla con los diferentes parámetros de cada
     * libro de la tabla Libros, además contiene una columna con las
     * diferentes operaciones disponibles para cada libro:
     * ver, modificar y eliminar
     * 
     * Además tiene la opción para añadir libros nuevos,
     * que nos redirige a la acción de añadir libros
     *
     * @return Void no devuelve nada, imprime la vista
     */
    public function accionIndex(){


        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual); //tiene que ser false, 0, para que podamos entrar


        //Guardo página actual, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["libros", "index"];



        if (Sistema::app()->Acceso()-> hayUsuario() === true && (!$borradoActual)){

            //Para acceder al crud de libros, se debe tener unicamente el permiso 1
            if (!Sistema::app()->Acceso()->puedePermiso(1)){

                //página de error
                Sistema::app()->paginaError("404", "No tienes permiso para acceder al index de libros");
                exit;
            }
        }

        else{ 

            //Si no hay usuario registrado, lo mandamos al login
            //tras logearse, se mandará a la acción anterior
            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;


        }




        //creamos array de filtrado
		if (!isset($_SESSION["arrayFiltradoLibros"])){
            $_SESSION["arrayFiltradoLibros"] = [
                "titulo" => "",
                "genero" => -1,
                "autor" => -1,
                "selectWhere" => "",
                "orderBy" => "",
                "editorial" => -1
            ];
		}

        $datos = [
            "titulo" => $_SESSION["arrayFiltradoLibros"]["titulo"],
            "genero" => $_SESSION["arrayFiltradoLibros"]["genero"],
            "editorial" => $_SESSION["arrayFiltradoLibros"]["editorial"],
            "autor" => $_SESSION["arrayFiltradoLibros"]["autor"]
        ]; 

        $selectWhere = "";
        $orderBy ="";


        //PARTE DEL POST//
        if ($_POST){


            if (isset($_POST["filtrarDatosIndexLibros"])){

				$titulo = "";
				if (isset($_POST["titulo"])){
					$titulo = trim(($_POST["titulo"]));
					$titulo = CGeneral::addSlashes($titulo);


					if ($titulo !== ""){
						$selectWhere.= " titulo LIKE '%$titulo%'";
					}
					
				}
				$datos["titulo"] = $titulo;

                $genero = -1;
                if (isset($_POST["genero"])){
                    $genero = intval($_POST["genero"]);

                    if ($selectWhere !== ""){
                        if (is_string(Generos::dameGenero($genero))){
                            $selectWhere .= "  AND cod_genero = $genero";
                        }
                    }
                    else{
                        if (is_string(Generos::dameGenero($genero))){
                            $selectWhere .= "  cod_genero = $genero";
                        }
                    }
                }
                $datos["genero"] = $genero;


                $editorial = -1;
                if (isset($_POST["editorial"])){
                    $editorial = intval($_POST["editorial"]);

                    if ($selectWhere !== ""){
                        if (is_string(Libros::dameEditorial($editorial))){
                            $selectWhere .= "  AND cod_editorial = $editorial";
                        }
                    }
                    else{
                        if (is_string(Libros::dameEditorial($editorial))){
                            $selectWhere .= "  cod_editorial = $editorial";
                        }
                    }
                }
                $datos["editorial"] = $editorial;

                //order by Por autor
                $autor = -1;
                if (isset($_POST["autor"])){
                    $autor = intval($_POST["autor"]);

                    if ($autor !== -1){//opcion -1 no se ordena

                        if ($autor === 0){ //Descendente
							$orderBy = "  autor DESC";
							
						}

						if ($autor === 1){ //Ascendente
							$orderBy = "  autor ASC";

						}
                    }
                }
                $datos["autor"] = $autor;
            }


            //limpiar filtrado
            if (isset($_POST["limpiarDatosIndexLibros"])){

                $datos["titulo"] = "";
                $datos["genero"] = -1;
                $datos["editorial"] = -1;
                $datos["autor"] = -1;
                $selectWhere = "";
                $orderBy = "";
            }

            $_SESSION["arrayFiltradoLibros"] = [
				"titulo" => $datos["titulo"],
				"genero" => $datos["genero"],
				"editorial" => $datos["editorial"],
                "autor" => $datos["autor"],
				"selectWhere" => $selectWhere,
				"orderBy" => $orderBy
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

        $libros = new Libros ();

        //guardamos consultas en la sesión
		if (isset($_SESSION["arrayFiltradoLibros"]["selectWhere"]) && $_SESSION["arrayFiltradoLibros"]["selectWhere"] !== ""){
			
			$selectWhere = 	$_SESSION["arrayFiltradoLibros"]["selectWhere"];
		}

		if (isset($_SESSION["arrayFiltradoLibros"]["orderBy"]) && $_SESSION["arrayFiltradoLibros"]["orderBy"] !== ""){
	
			$orderBy = $_SESSION["arrayFiltradoLibros"]["orderBy"];

		}

        //Comprobamos búsqueda para realizar con where y/o orderBy
		if ($selectWhere !== "" || $orderBy !== ""){


			if ($selectWhere !== "" && $orderBy !== ""){ //filtrado por where y order by
				$filas = $libros->buscarTodos(
					[
						"where" => $selectWhere,
						"order" => $orderBy,
						"limit" => $limite
					]
				);
			}

			//filtrado solamente por where
			if ($selectWhere !== "" && $orderBy === ""){ 
				$filas = $libros->buscarTodos(
					[
						"where" => $selectWhere,
						"limit" => $limite
					]
				);
			}


			//filtrado solamente por order by
			if ($selectWhere === "" && $orderBy !== ""){ 
				$filas = $libros->buscarTodos(
					[
						"order" => $orderBy,
						"limit" => $limite
					]
				);
			}

		}
		else{
			$filas = $libros->buscarTodos(
				[
					"limit" => $limite
				]
	
			);
		}

        //opciones del paginador
        $opcPaginador = array(
            "URL" => Sistema::app()->generaURL(array("libros", "index")),
            "TOTAL_REGISTROS" =>$libros->buscarTodosNRegistros($selectWhere !== "" ? ["where" => $selectWhere] : []), 
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
		

      

        foreach ($filas as $clave => $fila){



            $filas[$clave] = $fila;

            $filas[$clave]["fecha_lanzamiento"] =  CGeneral::fechaMysqlANormal($filas[$clave]["fecha_lanzamiento"]);
            //$filas[$clave]["foto"] = CHTML::imagen("/imagenes/libros/".$filas[$clave]["foto"], "{$filas[$clave]['titulo']}", ["style" => "width: 10%;"] );

            $filas[$clave]["oper"] = CHTML::link(CHTML::imagen("/imagenes/24x24/ver.png", "", ["title" => "Ver libro"]), Sistema::app()->generaURL(["libros","verLibro"], ["id" => $filas[$clave]["cod_libro"]])). " ".
            CHTML::link(CHTML::imagen("/imagenes/24x24/modificar.png", "", ["title" => "Modificar libro"]), Sistema::app()->generaURL(["libros","modificarLibro"], ["id" => $filas[$clave]["cod_libro"]]));

            $filas[$clave]["foto"] =  CHTML::imagen("/imagenes/libros/".$filas[$clave]["foto"], "Libro de {$filas[$clave]['titulo']}", ["class" => "imgTabla"]);

            $filas[$clave]["editorial"] = Libros::dameEditorial($filas[$clave]["cod_editorial"]);

            if (intval($filas[$clave]["borrado"]) === 0){
                $filas[$clave]["oper"] .= CHTML::link(CHTML::imagen("/imagenes/24x24/borrar.png", "", ["title" => "Borrar libro"]), Sistema::app()->generaURL(["libros", "borrarLibro"], ["id" => $filas[$clave]["cod_libro"]]));
                $filas[$clave]["borrado"] = CHTML::imagen("/imagenes/aceptar.png", "libro no borrado");
            }

            
            if (intval($filas[$clave]["borrado"]) === 1){
                $filas[$clave]["borrado"] =  CHTML::imagen("/imagenes/eliminar.png", "libro borrado");
            }

        }



        //No mostramos el cod_libro, cod_editoriales, cod_genero
        $cabecera = [
            ["ETIQUETA" => "Título",
				"CAMPO" => "titulo"],
				["ETIQUETA" => "ISBN",
				"CAMPO" => "isbn"],
				["ETIQUETA" => "Género",
				"CAMPO" => "genero"],
                ["ETIQUETA" => "Editorial",
				"CAMPO" => "editorial"],
				["ETIQUETA" => "Autor",
				"CAMPO" => "autor"],
				["ETIQUETA" => "Fecha de lanzamiento",
				"CAMPO" => "fecha_lanzamiento"],
				["ETIQUETA" => "Unidades",
				"CAMPO" => "unidades"],
                ["ETIQUETA" => "Precio de venta",
                "CAMPO" => "precio_venta"],
                ["ETIQUETA" => "Foto",
                "CAMPO" => "foto"],
                ["ETIQUETA" => "Borrado",
                "CAMPO" => "borrado"],
                [
                    "ETIQUETA" => "Operaciones",
                    "CAMPO" => "oper"
                ]
        ];

        $generos = Generos::dameGenero(null);
        $editoriales = Libros::dameEditorial(null);
        

        $this->dibujaVista("index", ["cabecera" => $cabecera, "filas" => $filas, "editoriales" => $editoriales,
                                     "generos" => $generos ,"paginador" => $opcPaginador, "datos" => $datos], "Index CRUD de Libros");
        
    }


    /**
     * Acción para ver la información de un libro
     * determianda a partir de un id, se muestran los diferentes campos
     * del libro pero no se pueden modificar solo ver
     * 
     * Se contiene operaciones para borrar o modificar el libro actual
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionVerLibro (){


        $id = "";
        if ($_GET){

            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }

            if ($id === ""){//en caso de no recibir parámetro id
                Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
                exit;
            }
        }




        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);

        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["libros", "verLibro/id=$id"];


        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(1)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }


        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        $this->barraUbi = [
            [
                "texto" => "inicio",
                "url" => "/"
            ],
            [
                "texto" => "Index de la tabla libros",
                "url" => ["libros", "index"]
            ],
            [
                "texto" => "Ver libro",
                "url" => ["libros", "VerLibro/id=".$id]
            ]
        ];



            //Se comprueba que el id pasado realmente existe

            $libro = new Libros ();


            if ($libro->buscarPorId($id) === false){
                Sistema::app()->paginaError(404, "No se ha encontrado un libro con el código indicado");
                exit;
            }
            else{
                $this->dibujaVista("verLibro", ["libro" => $libro], "Ver libro");

            }
    }


    /**
     * Acción para modificar los datos de un libro
     * muestra un formulario con los datos del
     * libro seleccionado a modificar, cuando se pulsa la opción de modificar
     * se validarán estos datos a partir de las restricción del modelo Libros
     * Si todo es correcto, se nos lleva a la acción ver libro, si no, no se modifica
     * y nos muestra los errores de los parámetros
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionModificarLibro(){

        $id = "";
        if ($_GET){

            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }

            if ($id === ""){//en caso de no recibir parámetro id
                Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
                exit;
            }
        }


        //poner enlace de acción anterior


        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);


        
        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["libros", "modificarLibro/id=$id"];

        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(1)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }
        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        $this->barraUbi = [
            [
                "texto" => "inicio",
                "url" => "/"
            ],
            [
                "texto" => "Index de la tabla libros",
                "url" => ["libros", "index"]
            ],
            [
                "texto" => "Modificar libro",
                "url" => ["libros", "ModificarLibro/id=".$id]
            ]
            ];



            //Se comprueba que el id pasado realmente existe

            $libro = new Libros ();
            $arrayGeneros = Generos::dameGenero();
            $arrayEditoriales = Libros::dameEditorial();


            if ($libro->buscarPorId($id) === false){
                Sistema::app()->paginaError(404, "No se ha encontrado un libro con el código indicado");
                exit;
            }
            else{

                

                if ($_POST){
                    $nombre = $libro->getNombre();

                    if (isset($_POST[$nombre])){
                        $libro->setValores($_POST[$nombre]);

                        $libro->genero = Generos::dameGenero($libro->cod_genero);
                        $libro->editorial = Libros::dameEditorial($libro->cod_editorial);
    
    
                        if (!$libro->validar()){
                            $this->dibujaVista("modificarLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Modificar libro");
                            exit();
                        }
                        else{
    
                            
                            if ($_FILES){
                                $fotoNueva = "";
                                if (isset($_FILES["libros"]["name"]["foto"])){
                                    $fotoNueva = trim($_FILES["libros"]["name"]["foto"]);
                                    $fotoNueva = CGeneral::addSlashes($fotoNueva);
    
                                    if ($fotoNueva !== ""){ //Comprobamos si se ha subido foto nueva
                                        $libro->foto = $fotoNueva;
                                        $rutaImagen = RUTA_BASE. "/imagenes/libros/".$fotoNueva;
    
                                        if (!move_uploaded_file($_FILES["libros"]["tmp_name"]["foto"], $rutaImagen)){
                                            Sistema::app()->paginaError(404, "No se pudo subir la foto");
                                        }
                                    }
                                }
                            }
    
    
                            if ($libro->guardar() === true){
                                $cod_libro = intval($libro->cod_libro);
                                header("location: ". Sistema::app()->generaURL(["libros", "verLibro"], ["id"=>$cod_libro]));
                                exit;
                            }
                            else{
                                $this->dibujaVista("modificarLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Modificar libro");
                                exit;
                            }
                        }
                    }

                }

                $this->dibujaVista("modificarLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Modificar libro");

            }
    }


    /**
     * Acción para hacer el borrado LÓGICO de un libro seleccionado
     * se puede elegir entre borrarlo o no, cuando se realiza la operación
     * nos lleva a la acción de ver libro
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionBorrarLibro(){
        
        $id = "";
        if ($_GET){

            if (isset($_GET["id"])){
                $id = intval($_GET["id"]);
            }

            if ($id === ""){//en caso de no recibir parámetro id
                Sistema::app()->paginaError(404, "Se ha accedido con un parámetro distinto al id");
                exit;
            }
        }



        $nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);

        
        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["libros", "borrarLibro/id=$id"];


        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(1)){
                Sistema::app()->paginaError(404, "No tienes permiso para acceder a esta página");
                exit;
            }


        }
        else{

            //En caso de no estar logeado, lo llevamos a la página de login

            Sistema::app()->irAPagina(["login", "InicioSesion"]);
            exit;
        }


        $this->barraUbi = [
            [
                "texto" => "inicio",
                "url" => "/"
            ],
            [
                "texto" => "Index de la tabla libros",
                "url" => ["libros", "index"]
            ],
            [
                "texto" => "Borrar libro",
                "url" => ["libros", "borrarLibro/id=".$id]
            ]
            ];



            //Se comprueba que el id pasado realmente existe

            $libro = new Libros ();


            //comprobar borrado lógico

            if ($libro->buscarPorId($id) === false){
                Sistema::app()->paginaError(404, "No se ha encontrado un libro con el código indicado");
                exit;
            }
            else{
                if ($_POST){
                    $nombre = $libro->getNombre();

                    if (isset($_POST[$nombre])){
                        $libro->setValores($_POST[$nombre]);    
    
                        if (!$libro->validar()){
                            $this->dibujaVista("borrarLibro", ["libro" => $libro], "Borrar libro");
                            exit();
                        }
                        else{
        
                            if ($libro->guardar() === true){
                                $cod_libro = intval($libro->cod_libro);
                                header("location: ". Sistema::app()->generaURL(["libros", "verLibro"], ["id"=>$cod_libro]));
                                exit;
                            }
                            else{
                                $this->dibujaVista("borrarLibro", ["libro" => $libro], "Borrar libro");
                                exit;
                            }
                        }
                    }

                }


                //Se comprueba si el libro ha sido borrado, si es borrado
                //lo mandamos a la página de error

                if ($libro->borrado === 1){
                    Sistema::app()->paginaError(505, "El libro seleccionado ya ha sido borrado");
                    exit;
                }
                else{
                    $this->dibujaVista("borrarLibro", ["libro" => $libro], "Borrar libro");
                }


            }

        }

    


//--------------------------------------------------------------------------------//




    /**
     * Acción para añadir un libro nuevo a la tabla de libros
     * nos muestra un formulario vacío con los diferentes parámetros
     * a rellenar, estos datos se validaran a partir del modelo Libros,
     * si se cumplen, se inserta producto y nos lleva a la acción de verLibro
     * con el id del libro insertado
     *
     * @return Void no devuelve nada, imprime una vista
     */
    public function accionAnadirLibro (){


        $nickUserActual = Sistema::app()->Acceso()->getNick();
        $codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
        $borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual);

        //Guardo página actual en SESION, en caso de no
        //estar logeado
        $_SESSION["anterior"] = ["libros", "anadirLibro"];


        if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){


            //comprobamos permisos
            if (!Sistema::app()->Acceso()->puedePermiso(1)){
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
                "texto" => "Index de tabla de libros",
                "url" => ["libros", "index"]
            ],
            [
                "texto" => "Añadir libro",
                "url" => ["libros", "AnadirLibro"]
            ]
        ];

        $libro = new Libros();
        $libro->fecha_lanzamiento = $libro->fecha_lanzamiento->format("d/m/Y");
        $arrayGeneros = Generos::dameGenero();
        $arrayEditoriales = Libros::dameEditorial();


        if ($_POST) {
            $nombre = $libro->getNombre();


            if (isset($_POST[$nombre])) {
                $libro->setValores($_POST[$nombre]);

                $libro->genero = Generos::dameGenero($libro->cod_genero);
                $libro->editorial = Libros::dameEditorial($libro->cod_editorial);


                if (!$libro->validar()) {
                    $this->dibujaVista("anadeLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Modificar libro");
                    exit();
                } else {


                    if ($_FILES) {
                        $fotoNueva = "";
                        if (isset($_FILES["libros"]["name"]["foto"])) {
                            $fotoNueva = trim($_FILES["libros"]["name"]["foto"]);
                            $fotoNueva = CGeneral::addSlashes($fotoNueva);

                            if ($fotoNueva !== "") { //Comprobamos si se ha subido foto nueva
                                $libro->foto = $fotoNueva;
                                $rutaImagen = RUTA_BASE . "/imagenes/libros/" . $fotoNueva;

                                if (!move_uploaded_file($_FILES["libros"]["tmp_name"]["foto"], $rutaImagen)) {
                                    Sistema::app()->paginaError(404, "No se pudo subir la foto");
                                }
                            }
                        }
                    }


                    if ($libro->guardar() === true) {
                        $cod_libro = intval($libro->cod_libro);
                        header("location: " . Sistema::app()->generaURL(["libros", "verLibro"], ["id" => $cod_libro]));
                        exit;
                    } else {
                        $this->dibujaVista("anadeLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Modificar libro");
                        exit;
                    }
                }
            }
        }

        $this->dibujaVista("anadeLibro", ["libro" => $libro, "generos" => $arrayGeneros, "editoriales" => $arrayEditoriales], "Añadir  libro");

}

 

/**
 * Acción para la petición AJAX
 * la usamos para la parte de JavaScript del proyecto, 
 * a partir de una petición fetch, recibimos el parámetro GET con el id de un libro
 * comprobamos que el id existe, en caso de existir enviamos al javascript
 * un JSON con los datos del libro solicitado
 *
 * @return Void, no devuelve nada imprime un JSON del libro
 */
public function accionPeticionAJAXLibro (){



        if ($_GET) {

            $id = "";
            if (isset($_GET["id"])) {

                $id = intval($_GET["id"]);

                $libros = new Libros();

                $libro = $libros->buscarTodos(["where" => " cod_libro = $id"]);

                //llamamos la funcion de devuelveEditoriales para
                //pasarle la editorial correspondiente
                $libro = $libro[0];

                $libro["editorial"] =  Libros::dameEditorial(intval($libro["cod_editorial"]));


                if ($libro !== false) {

                    $resultado = [
                        "libro" => $libro,
                        "correcto" => true
                    ];
                } else {
                    $resultado = [
                        "libro" => "No se ha encontrado libro con el id indicado",
                        "correcto" => false
                    ];
                }

                echo json_encode($resultado);
            }
        }
    }





}
?>