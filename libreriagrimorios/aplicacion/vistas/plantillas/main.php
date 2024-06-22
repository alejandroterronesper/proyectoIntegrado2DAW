<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $titulo; ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/estilos/principal.css" />

	<link rel="icon" type="image/jpg" href="/imagenes/favicon.jpg" />
	<?php
	if (isset($this->textoHead)){
			$this->textoHead .= " ". CMenu::requisitos().PHP_EOL;
			echo $this->textoHead;
	}
	

	?>
</head>

<body>

	<header>
		<div class="logo">
			<a href="/index.php"> <img alt="logo" src="/imagenes/logo.jpg"> </a>

			<a href="/index.php">
				<h2 class="nombreTienda"> Librería Grimorios</h2>
			</a>
		</div>
		<div class="opcionesHeader">
			<?php

				if ($_POST){
					if (isset($_POST["CerrarSesion"])){

						Sistema::app()->Acceso()->quitarRegistroUsuario();
						Sistema::app()->irAPagina(array("inicial"));
						exit;
					}
				}
				

				if (Sistema::app()->Acceso()->hayUsuario() === true){

					echo CHTML::iniciarForm("", "post", []).PHP_EOL;
					echo CHTML::campoBotonSubmit("Cerrar sesión", ["name" => "CerrarSesion", "class" => "boton"]).PHP_EOL;
					echo CHTML::finalizarForm().PHP_EOL;
					echo CHTML::dibujaEtiqueta("span", ["style" => "font-weight: bold;"], "Usuario: ".Sistema::app()->Acceso()->getNick()).PHP_EOL;


				}
				else{
					echo CHTML::botonHtml(CHTML::link("Login", ["login", "InicioSesion"], []), ["class" => "boton"]).PHP_EOL;
					echo CHTML::dibujaEtiqueta("span", ["style" => "font-weight: bold;"], "Usuario no conectado").PHP_EOL;
				}

			?>
		</div>

	</header><!-- #header -->


	<!-- NAVEGADOR-->
	<?php

		//Cogemos usuario si hay para poder controlar los permisos
		$nickUserActual = Sistema::app()->Acceso()->getNick();
		$codUserActual = Sistema::app()->ACL()->getCodUsuario($nickUserActual);
		$borradoActual = Sistema::app()->ACL()->getBorrado($codUserActual); 


		//ETIQUETA MENU CON EL COMPONENTE CMENU
		$arrayContenido = [];
		array_push($arrayContenido,CHTML::link("Libros", "/", []));
		array_push($arrayContenido,CHTML::link("Editoriales", ["editoriales", "index"], []));

		if (Sistema::app()->Acceso()->hayUsuario() === true && (!$borradoActual)){
					
			//Para acceder al crud de libros debe tener unicamente el permiso 1
			if (Sistema::app()->Acceso()->puedePermiso(1)){
				array_push($arrayContenido, CHTML::link("CRUD Libros", ["libros", "index"], []));
			}


			//Para acceder al crud de editoriales (API) se debe tener unicamente el permiso 9
			if (Sistema::app()->Acceso()->puedePermiso(9)){
				array_push($arrayContenido,CHTML::link("CRUD Editoriales", ["editoriales", "EditorialCRUD"], []));
		
			}
			
		}

		if (Sistema::app()->Acceso()->hayUsuario()  === false){
			array_push($arrayContenido,CHTML::link("Formulario sugerencias", ["inicial", "formSugerencias"], []));

		}

		$menu = new CMenu($arrayContenido);
	
		
	
		echo $menu->dibujate();

	
	?>
			
	<!-- Barra de ubicación-->
	<div id="barraUbicacion">

			<?php
				$longitud = count ($this->barraUbi) -1 ;
				foreach ($this->barraUbi as $clave => $valor) {
					
					if ($clave === $longitud){
					 	echo CHTML::link($valor["texto"], $valor["url"]). PHP_EOL; 
					}
					else{
					echo CHTML::link($valor["texto"], $valor["url"]). " &gt;&gt; ". PHP_EOL;
					}
				}
			?>
	
	</div>

	<main>

        <article> <!-- Aqui metemos el contenido de los productos -->
			<?php echo $contenido; ?>
        </article>
    </main>
	
	<footer>
		<h2><span>Copyright:</span> <?php echo Sistema::app()->autor ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Dirección:</span>
		<?php echo Sistema::app()->direccion ?></h2>
	</footer><!-- #footer -->


</body>

</html>