<?php


$this->textoHead = "   ". CHTML::scriptFichero("/js/formContacto.js", ["defer" => "defer"]).PHP_EOL;


echo CHTML::dibujaEtiqueta("div", ["class" => "divForm"], null, false).PHP_EOL;

echo CHTML::iniciarForm("#", "post", ["id" => "formularioJS"]).PHP_EOL;

    echo CHTML::campoLabel("Introduce tu nombre: ", "nombre", []).PHP_EOL;
    echo CHTML::campoText("nombre", "", ["placeholder" => "nombre completo", "required" => true]).PHP_EOL;

    echo "<br>".PHP_EOL;

    echo CHTML::campoLabel("Introduce tu teléfono: ", "telefono", []).PHP_EOL;
    echo CHTML::campoNumber("telefono", "", ["placeholder" => "952XXXXXX", "required" => true]).PHP_EOL;

    echo "<br>".PHP_EOL;

    echo CHTML::campoLabel("Introduce tu email: ", "correo", []).PHP_EOL;
    echo CHTML::dibujaEtiqueta("input", ["placeholder" => "correo@correo.es", "type" => "email", "required" => true, "id" => "correo"]).PHP_EOL;
    
    echo "<br>".PHP_EOL;

    echo CHTML::campoLabel("Introduce tu fecha de nacimiento: ", "fechaNac", []).PHP_EOL;
    echo CHTML::dibujaEtiqueta("input", [ "type" => "date", "required" => true, "id" => "fechaNac"]).PHP_EOL;

    echo "<br>".PHP_EOL;

    echo CHTML::campoLabel("Indique su nivel de estudios más alto: ", "estudios", []).PHP_EOL;
    echo CHTML::campoListaDropDown("estudios", 0, [0 => "Sin estudios", 1 => "Primaria", 2 => "ESO", 3 => "Bachiller", 4 => "FP"], ["required" => true]).PHP_EOL;

    echo "<br>".PHP_EOL;

    echo CHTML::campoLabel("Escriba alguna sugerencia: ", "sugerencia", []).PHP_EOL;
    echo "<br>".PHP_EOL;

    echo CHTML::campoTextArea("sugerencia", "", ["rows" => 10, "cols" => 70,"required" => true]).PHP_EOL;

    echo "<br>".PHP_EOL;
    
    echo CHTML::campoBotonSubmit("Enviar información", ["id" => "botonJS", "class" => "boton"] ).PHP_EOL;
    echo CHTML::campoBotonReset("Limpiar formulario", ["class" => "boton"]).PHP_EOL;
    echo CHTML::botonHtml(CHTML::link("Volver al inicio", ["inicial"]), ["class"=>"boton"]).PHP_EOL;

echo CHTML::finalizarForm().PHP_EOL;


echo CHTML::dibujaEtiqueta("div", ["id" => "divSugERROR", "class" => "error"], "hola", true).PHP_EOL;

echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


?>