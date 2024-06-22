<?php

/**
 * Clase para el controlador inicial
 * contiene la acción del index principal
 * de la página
 */
class inicialControlador extends CControlador
{

	/**
	 * Acción index, esta será la acción que se ejecuta
	 * al poner el nombre de la página, esta imprime una vista con 
	 * los diferentes libros de la tabla libros, para obtenerlos
	 * usamos el modelo de Libros
	 *
	 * @return Void no devuelve nada, imprime la vista de libros
	 */
	public function accionIndex()
	{

		//Guardamos en sesión acción actual
		$_SESSION["anterior"] = ["inicial", "index"];

	
		//Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
		  ]
	  	];



		//creamos array de filtrado
		if (!isset($_SESSION["arrayFiltradoPrincipal"])){
			$_SESSION["arrayFiltradoPrincipal"] = [
				"titulo" => "",
				"autor" => "",
				"fecha" => -1,
				"selectWhere" => "",
				"ordernar" => "",
				"libros" => []
			];
		}

		//filtrado recogio del formulario
		$datos = [
			"titulo" => $_SESSION["arrayFiltradoPrincipal"]["titulo"],
			"autor" => $_SESSION["arrayFiltradoPrincipal"]["autor"],
			"fecha" => $_SESSION["arrayFiltradoPrincipal"]["fecha"]

		];

		$selectWhere = "";
		$orderBy = "";


		if ($_POST){

			if (isset($_POST["filtraDatosPrincipal"])){
				
				$titulo = "";
				if (isset($_POST["titulo"])){
					$titulo = trim(($_POST["titulo"]));
					$titulo = CGeneral::addSlashes($titulo);


					if ($titulo !== ""){
						$selectWhere.= " titulo LIKE '%$titulo%'";
					}
					
				}
				$datos["titulo"] = $titulo;

				$autor = "";
				if (isset($_POST["autor"])){
					$autor = trim(($_POST["autor"]));
					$autor = CGeneral::addSlashes($autor);

					if ($selectWhere !== ""){
						if ($autor !== ""){
							$selectWhere.= " AND autor LIKE '%$autor%'";
						}
					}
					else{
						if ($autor !== ""){
							$selectWhere.= " autor LIKE '%$autor%'";
						}
					}
				}
				$datos["autor"] = $autor;

				$fecha = -1;
				if (isset($_POST["fecha"])){
					$fecha = intval($_POST["fecha"]);

					if ($fecha !== -1){ //-1 es sin ordenar por fecha

						if ($fecha === 0){ //Descendente
							$orderBy = "fecha_lanzamiento DESC";
							
						}

						if ($fecha === 1){ //Ascendente
							$orderBy = "fecha_lanzamiento ASC";

						}
					}
				}
				$datos["fecha"] = $fecha;


			}


			if (isset($_POST["limpiaFiltradoPrincipal"])){
					
				//se limpian los campos y el array de la sesion
				$datos["titulo"] = "";
				$datos["autor"] = "";
				$datos["fecha"] = -1;
				$selectWhere = "";
				$orderBy = "";

				
			}

			$_SESSION["arrayFiltradoPrincipal"] = [
				"titulo" => $datos["titulo"],
				"autor" => $datos["autor"],
				"fecha" => $datos["fecha"],
				"selectWhere" => $selectWhere,
				"ordernar" => $orderBy
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

		//se guarda la consulta en sesión
		if (isset($_SESSION["arrayFiltradoPrincipal"]["selectWhere"]) && $_SESSION["arrayFiltradoPrincipal"]["selectWhere"] !== ""){
			
			$selectWhere = 	$_SESSION["arrayFiltradoPrincipal"]["selectWhere"];
		}

		if (isset($_SESSION["arrayFiltradoPrincipal"]["orderBy"]) && $_SESSION["arrayFiltradoPrincipal"]["orderBy"] !== ""){
	
			$orderBy = $_SESSION["arrayFiltradoPrincipal"]["orderBy"];

		}


		//Buscar todos con where y orderBy
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


		$librosDescargas = $libros->buscarTodos(
			["where" => $selectWhere]
		);

		$_SESSION["arrayFiltradoPrincipal"]["libros"] = $librosDescargas;
			
		//opciones del paginador
		$opcPaginador = array(
			"URL" => Sistema::app()->generaURL(array("inicial", "index")),
			"TOTAL_REGISTROS" => $libros->buscarTodosNRegistros($selectWhere !== "" ? ["where" => $selectWhere] : []),
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
		
		$this->dibujaVista("index",["libros" => $filas, "paginador" => $opcPaginador, "datos" => $datos],
							"Librería Grimorios - Inicio");
	}



	/**
	 * 
	 *
	 * @return void
	 */
	public function accionDescarga (){

		//Comprobamos si hay productos guardados en la sesión
		if (isset($_SESSION["arrayFiltradoPrincipal"]["libros"])) {

			$libros = $_SESSION["arrayFiltradoPrincipal"]["libros"];
		}
		else{ //Si no hay búsqueda previa, se hace búsqueda de todos
			$libros = new Libros ();
			$libros = $libros->buscarTodos();
		}

		//formateamos valores
		foreach($libros as $clave => $fila){

			if ($clave === "borrado"){ //Guardaremos todos los datos para el usuario, menos el borrado



			}
		}

	}


	/**
	 * Vista que muestra un formulario de contacto
	 * lo usamos para validar un formulario desde JavaScript
	 * en esta vista no pueden acceder usuarios logeados
	 * @return void
	 */
	public function accionformSugerencias (){

		$nickUserActual = Sistema::app()->Acceso()->getNick();

		if ($nickUserActual !== false){ //Si no estamos logeado da false
			Sistema::app()->paginaError(404, "Para visitar esta página no puedes estar logeado!");
			exit;
		}

	
		//Barra de ubicación
		$this->barraUbi = [
			[
			  "texto" => "inicio",
			  "url" => "/"
			],
			[
				"texto" => "Formulario de sugerencias",
				"url" => "formSugerencias"
			]
	  	];

		$generosArray = Generos::dameGenero(null);

		
		
		$this->dibujaVista("formContacto",["generos" => $generosArray],
							"Librería Grimorios - Formulario de sugerencias");
	}

	
}
