<?php
//dibujamos los divs




    echo CHTML::dibujaEtiqueta("div", ["class"=> "libros"], null, true).PHP_EOL;
    echo CHTML::dibujaEtiqueta("label", ["class" => "cursiva"], $libro["titulo"], true).PHP_EOL;
    echo CHTML::imagen( "../../../imagenes/libros/".$libro["foto"]).PHP_EOL;
    echo CHTML::dibujaEtiqueta("label", [], "Autor: ". $libro["autor"]).PHP_EOL;
    echo CHTML::botonHtml("Ver más información", ["onclick"=> "verInformacionLibro({$libro['cod_libro']})", "class" => "boton"]).PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

 


?>