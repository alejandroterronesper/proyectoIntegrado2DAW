<?php



echo CHTML::dibujaEtiqueta("div", ["class"=> "editoriales"], null, true).PHP_EOL;
    
    echo CHTML::dibujaEtiqueta("div", ["class" => "editorialesImagen"], null, false).PHP_EOL;
        echo CHTML::imagen( "../../../imagenes/logosEditoriales/".$editorial["logo"]).PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

    echo CHTML::dibujaEtiqueta("div", ["class" => "editorialesInformacion", null, false]).PHP_EOL;
        echo CHTML::dibujaEtiqueta("span", [], "{$editorial['nombre']} - Fundada por {$editorial['fundador']} en {$editorial['fecha_creacion']}").PHP_EOL;
        echo CHTML::dibujaEtiqueta("p", [], $editorial["historia"], true).PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;





?>