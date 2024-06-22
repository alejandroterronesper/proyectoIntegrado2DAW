<?php

$this->textoHead = CPager::requisitos();
$this->textoHead .= "    ". CCaja::requisitos() ;
$this->textoHead .= "   ". CHTML::scriptFichero("/js/main.js", ["defer" => "defer"]).PHP_EOL;

//paginador
$varPagina = new CPager($paginador, []);

echo CHTML::dibujaEtiqueta("h1", [], "CRUD de libros", true).PHP_EOL;



//caja de filtrado
$objCaja = new CCaja("Filtrar búsqueda", "", []);
echo $objCaja->dibujaApertura().PHP_EOL;

echo CHTML::iniciarForm("","POST", ["class" => "formulario"]).PHP_EOL;
echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "titulo"], "Título: ", true).PHP_EOL;
echo CHTML::campoText("titulo", $datos["titulo"], []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "genero"], "Género: ", true).PHP_EOL;
echo CHTML::campoListaDropDown("genero", $datos["genero"], $generos, []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "editorial"], "Editorial: ", true).PHP_EOL;
echo CHTML::campoListaDropDown("editorial", $datos["editorial"], $editoriales, []).PHP_EOL;

echo "<br>".PHP_EOL;


echo CHTML::dibujaEtiqueta("label", ["for" => "autor"], "Ordenar por autor (alfabeticamente): ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("autor", $datos["autor"], [-1=> "Sin ordenar", 0 => "Descentente " ,1 => "Ascendente", ], "").PHP_EOL;


echo "<br>".PHP_EOL;

echo CHTML::campoBotonSubmit("Filtrar", ["class" => "boton", "name" => "filtrarDatosIndexLibros"]).PHP_EOL;
echo CHTML::campoBotonSubmit("Limpiar filtrado", ["class" => "boton", "name" => "limpiarDatosIndexLibros"]).PHP_EOL;



echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;


echo CHTML::finalizarForm().PHP_EOL;


echo $objCaja->dibujaFin().PHP_EOL;


$valor = Libros::dameEditorial(100);
//TABLA
$tabla = new CGrid($cabecera, $filas, ["class" => "tabla1"]);

echo $varPagina->dibujate().PHP_EOL;
    echo $tabla->dibujate().PHP_EOL;
echo $varPagina->dibujate().PHP_EOL;

echo CHTML::botonHtml(CHTML::link("Volver atrás", ["inicial", "index"]), 
                    ["class"=>"boton", "style" => "margin-top: 3%; margin-left: 1.6%"]).PHP_EOL;


echo CHTML::botonHtml(CHTML::link("Añadir libro", ["libros", "AnadirLibro"]),["class" => "boton"]). PHP_EOL;

?>