<?php

//javascript
$this->textoHead = CPager::requisitos().PHP_EOL;
$this->textoHead .= "    ". CCaja::requisitos().PHP_EOL;
$this->textoHead .= "   ". CHTML::scriptFichero("/js/main.js", ["defer" => "defer"]).PHP_EOL;


//paginador
$varPagina = new CPager($paginador, []);

echo CHTML::dibujaEtiqueta("h1", [], "Index principal", true).PHP_EOL;

//caja de filtrado
$objCaja = new CCaja("Filtrar búsqueda", "", []);
echo $objCaja->dibujaApertura().PHP_EOL;

echo CHTML::iniciarForm("","POST", ["class" => "formulario"]).PHP_EOL;
echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "titulo"], "Título: ", true).PHP_EOL;
echo CHTML::campoText("titulo", $datos["titulo"], []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "autor"], "Autor: ", true).PHP_EOL;
echo CHTML::campoText("autor", $datos["autor"], []).PHP_EOL;

echo "<br>".PHP_EOL;


echo CHTML::dibujaEtiqueta("label", ["for" => "fecha"], "Ordenar por fecha: ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("fecha", $datos["fecha"], [-1=> "Sin ordenar",0 => "Descentente " ,1 => "Ascendente", ], "").PHP_EOL;


echo "<br>".PHP_EOL;

echo CHTML::campoBotonSubmit("Filtrar", ["class" => "boton", "name" => "filtraDatosPrincipal"]).PHP_EOL;
echo CHTML::campoBotonSubmit("Limpiar filtrado", ["class" => "boton", "name" => "limpiaFiltradoPrincipal"]).PHP_EOL;
echo CHTML::campoBotonSubmit("Exportar búsqueda (.txt)", ["class" => "boton", "name" => "exportarPrincipal"]).PHP_EOL;



echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;


echo CHTML::finalizarForm().PHP_EOL;


echo $objCaja->dibujaFin().PHP_EOL;


echo $varPagina->dibujate().PHP_EOL;
echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor"], null, false).PHP_EOL;

    foreach ($libros as $clave => $valor){

        echo $this->dibujaVistaParcial("divLibro", ["libro" => $valor], true).PHP_EOL;

    }

echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
echo $varPagina->dibujate().PHP_EOL;





?>

    