<?php


$titulo = CHTML::dibujaEtiqueta("span",["style" => "font-style:italic"], $libro->titulo);
echo CHTML::dibujaEtiqueta("h2", [], "Ver libro: " .$titulo, true).PHP_EOL;


//div del libro
echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDLIBROS"], null, false).PHP_EOL;


    //información
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDInfo"], null, false).PHP_EOL;
        echo CHTML::iniciarForm("", "", []).PHP_EOL;

            echo CHTML::modeloLabel($libro, "titulo", []).PHP_EOL;
            echo CHTML::modeloText($libro, "titulo", ["size" => 40, "readonly" => true]).PHP_EOL;
            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "isbn", []).PHP_EOL;
            echo CHTML::modeloText($libro, "isbn", ["size" => 50, "readonly" => true]).PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "autor", []).PHP_EOL;
            echo CHTML::modeloText($libro, "autor", ["size" => 40, "readonly" => true]).PHP_EOL;


            echo "<br>".PHP_EOL;


            echo CHTML::modeloLabel($libro, "genero", []).PHP_EOL;
            echo CHTML::modeloText($libro, "genero", ["size" => 40, "readonly" => true]).PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "editorial", []).PHP_EOL;
            echo CHTML::modeloText($libro, "editorial", ["size" => 40, "readonly" => true]).PHP_EOL;


            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "fecha_lanzamiento", []).PHP_EOL;
            echo CHTML::modeloText($libro, "fecha_lanzamiento", [ "readonly" => true]).PHP_EOL;
        
        
            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "unidades", []).PHP_EOL;
            echo CHTML::modeloNumber($libro, "unidades", ["readonly" => true]).PHP_EOL;


            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "precio_venta", []).PHP_EOL;
            echo CHTML::modeloText($libro, "precio_venta", ["size" => 40, "readonly" => true]).PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "borrado", []).PHP_EOL;
            echo CHTML::modeloListaRadioButton($libro, "borrado", [0=>"NO", 1=> "SI"], " ", ["disabled" => true]).PHP_EOL;

        echo CHTML::finalizarForm().PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

    //foto

    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDImg"], null, false).PHP_EOL;
        echo CHTML::imagen("../../imagenes/libros/" .$libro->foto , "Libro: {$libro->titulo}").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;




echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


    //Operaciones
    echo CHTML::dibujaEtiqueta("div", ["class" => "operaciones"], null, false).PHP_EOL;

        echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

            echo CHTML::dibujaEtiqueta("legend", [], "Operaciones disponibles", true).PHP_EOL;

            echo CHTML::botonHtml(CHTML::link("Volver atrás", ["libros", "index"]), ["class"=>"boton"]).PHP_EOL;
            echo CHTML::botonHtml(CHTML::link("Volver al inicio", ["inicial", "index"]), ["class"=>"boton"]).PHP_EOL;
            echo CHTML::botonHtml(CHTML::link("Modificar libro", ["libros", "modificarLibro/id=".$libro->cod_libro]), ["class"=>"boton"]).PHP_EOL;


            if ($libro->borrado === 0){
                echo CHTML::botonHtml(CHTML::link("Borrar libro", ["libros", "borrarLibro/id=".$libro->cod_libro]), ["class"=>"boton"]).PHP_EOL;
            }


        echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


?>