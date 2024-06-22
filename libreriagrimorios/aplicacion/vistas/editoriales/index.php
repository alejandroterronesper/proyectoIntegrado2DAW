<?php

//javascript
$this->textoHead = CPager::requisitos();
$this->textoHead .= "    ". CCaja::requisitos() ;
$this->textoHead .= "   ". CHTML::scriptFichero("/js/main.js", ["defer" => "defer"]).PHP_EOL;

//paginador
$varPagina = new CPager($paginador, []);

echo CHTML::dibujaEtiqueta("h2", [], "Index de editoriales ", true).PHP_EOL;

//caja de filtrado
$objCaja = new CCaja("Filtrar búsqueda", "", []);
echo $objCaja->dibujaApertura().PHP_EOL;

echo CHTML::iniciarForm("","POST", ["class" => "formulario"]).PHP_EOL;
echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre: ", true).PHP_EOL;
echo CHTML::campoText("nombre", $datos["nombre"], []).PHP_EOL;

echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "fundador"], "Fundador: ", true).PHP_EOL;
echo CHTML::campoText("fundador",$datos["fundador"], []).PHP_EOL;

echo "<br>".PHP_EOL;


echo CHTML::dibujaEtiqueta("label", ["for" => "cese"], "Cese: ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("cese", $datos["cese"], [-1=> "Todos", 0 => "Si " ,1 => "No", ], "").PHP_EOL;


echo "<br>".PHP_EOL;

echo CHTML::dibujaEtiqueta("label", ["for" => "orderBy"], "Ordenar alfabeticamente (nombre editorial): ", true).PHP_EOL;
echo CHTML::campoListaRadioButton("orderBy", $datos["orderBy"], [-1=> "No", 0 => "Ascendente " ,1 => "Descendente", ], "").PHP_EOL;


echo "<br>".PHP_EOL;


echo CHTML::campoBotonSubmit("Filtrar", ["class" => "boton", "name" => "filtraDatosEdPrincipal"]).PHP_EOL;
echo CHTML::campoBotonSubmit("Limpiar filtrado", ["class" => "boton", "name" => "limpiaFiltradoEdPrincipal"]).PHP_EOL;
echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
echo CHTML::finalizarForm().PHP_EOL;

echo $objCaja->dibujaFin().PHP_EOL;

echo $varPagina->dibujate().PHP_EOL;
echo CHTML::dibujaEtiqueta("div", ["class" => "contenedorEditoriales"], null, false).PHP_EOL;
foreach ($filas as $clave => $valor){
    echo $this->dibujaVistaParcial("divEditorial", ["editorial" => $valor], true).PHP_EOL;

}
echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
echo $varPagina->dibujate().PHP_EOL;

?>