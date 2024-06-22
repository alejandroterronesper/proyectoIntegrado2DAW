<?php

$this->textoHead = "   ". CHTML::scriptFichero("/js/main.js", ["defer" => "defer"]).PHP_EOL;

//Vista del login

echo CHTML::iniciarForm("", "POST", ["class" => "operaciones"]).PHP_EOL;
echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

echo CHTML::dibujaEtiqueta("legend", [], "Formulario de login", true).PHP_EOL;



echo CHTML::modeloLabel($miLOGIN, "nick").PHP_EOL;
echo CHTML::modeloText($miLOGIN, "nick", ["maxlength" => 20])."<br>".PHP_EOL;
echo CHTML::modeloError($miLOGIN, "nick").PHP_EOL;


echo CHTML::modeloLabel($miLOGIN, "contrasenia").PHP_EOL;
echo CHTML::modeloPassword($miLOGIN, "contrasenia", ["maxlength" => 20])."<br>".PHP_EOL;
echo CHTML::modeloError($miLOGIN, "contrasenia").PHP_EOL;


echo CHTML::campoBotonSubmit("Logearse", ["class" => "boton"]).PHP_EOL;

echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
echo CHTML::finalizarForm().PHP_EOL;



echo "<br>".PHP_EOL;
echo "<br>".PHP_EOL;
echo "<br>".PHP_EOL;
echo CHTML::botonHtml(CHTML::link("Volver atrás", ["inicial"]), ["class"=>"boton"]).PHP_EOL;

?>