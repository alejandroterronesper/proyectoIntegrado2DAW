<?php

//javascript
$this->textoHead = CPager::requisitos();
$this->textoHead .= "    ". CCaja::requisitos() ;
$this->textoHead .= "   ". CHTML::scriptFichero("/js/main.js", ["defer" => "defer"]).PHP_EOL;


//paginador
$varPagina = new CPager($paginador, []);


echo CHTML::dibujaEtiqueta("h1", [], "Crud editoriales", true).PHP_EOL;

//caja de filtrado
$objCaja = new CCaja("Filtrar búsqueda", "", []);
echo $objCaja->dibujaApertura().PHP_EOL;

echo CHTML::iniciarForm("","POST", ["class" => "formulario"]).PHP_EOL;
echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre: ", true).PHP_EOL;
echo CHTML::campoText("nombre", $datos["nombre"], []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "fundador"], "Fundador: ", true).PHP_EOL;
echo CHTML::campoText("fundador", $datos["fundador"], []).PHP_EOL;

echo "<br>".PHP_EOL;


echo CHTML::dibujaEtiqueta("label", ["for" => "cese"], "Estado: ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("cese", $datos["cese"], [-1=> "Todas",1 => "Cerradas " ,0 => "Abiertas"], "").PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label",["for" => "orderByDate"], "Ordenar por fecha: ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("orderByDate", $datos["orderByDate"] , [-1 => "No", 0 => "Ascendente", 1 => "Descendente"], " ", []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::campoBotonSubmit("Filtrar", ["class" => "boton", "name" => "filtrarDatosCrudEd"]).PHP_EOL;
echo CHTML::campoBotonSubmit("Limpiar filtrado", ["class" => "boton", "name" => "limpiarDatosCrudEd"]).PHP_EOL;
echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
echo CHTML::finalizarForm().PHP_EOL;

echo $objCaja->dibujaFin().PHP_EOL;



$tabla = new CGrid($cabecera, $filas, ["class" => "tabla1", "style"=> "margin-left: 7.8%;"]);

echo $varPagina->dibujate().PHP_EOL;
    echo $tabla->dibujate().PHP_EOL;
echo $varPagina->dibujate().PHP_EOL;







echo CHTML::botonHtml(CHTML::link("Volver atrás", ["inicial", "index"]), 
                    ["class"=>"boton", "style" => "margin-top: 3%; margin-left: 1.6%"]).PHP_EOL;

echo CHTML::botonHtml(CHTML::link("Añadir editorial", ["editoriales", "anadeEditorial"]),["class" => "boton"]). PHP_EOL;

?>