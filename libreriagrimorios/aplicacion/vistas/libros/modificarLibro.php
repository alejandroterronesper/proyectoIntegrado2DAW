<?php


$titulo = CHTML::dibujaEtiqueta("span",["style" => "font-style:italic"], $libro->titulo);
echo CHTML::dibujaEtiqueta("h2", [], "Modificar libro: " .$titulo, true).PHP_EOL;


//div del libro
echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDLIBROS"], null, false).PHP_EOL;


    //información
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDInfo"], null, false).PHP_EOL;
        echo CHTML::iniciarForm("", "POST", [ "enctype" =>"multipart/form-data"]).PHP_EOL;

            echo CHTML::modeloLabel($libro, "titulo", []).PHP_EOL;
            echo CHTML::modeloText($libro, "titulo", ["size" => 40, "readonly" => true]).PHP_EOL;
            echo CHTML::modeloError($libro, "titulo").PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "isbn", []).PHP_EOL;
            echo CHTML::modeloText($libro, "isbn", ["size" => 50, "placeholder" => "978-xx-xxx-xxxx-x"]).PHP_EOL;
            echo CHTML::modeloError($libro, "isbn").PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "autor", []).PHP_EOL;
            echo CHTML::modeloText($libro, "autor", ["size" => 40]).PHP_EOL;
            echo CHTML::modeloError($libro, "autor").PHP_EOL;


            echo "<br>".PHP_EOL;


            echo CHTML::modeloLabel($libro, "cod_genero", []).PHP_EOL;
            echo CHTML::modeloListaDropDown($libro, "cod_genero", $generos).PHP_EOL;
            echo CHTML::modeloError($libro, "cod_genero").PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "cod_editorial", []).PHP_EOL;
            echo CHTML::modeloListaDropDown($libro, "cod_editorial", $editoriales).PHP_EOL;
            echo CHTML::modeloError($libro, "cod_editorial").PHP_EOL;


            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "fecha_lanzamiento", []).PHP_EOL;
            echo CHTML::modeloText($libro, "fecha_lanzamiento", [ "placeholder" => "dd/mm/aaaa"]).PHP_EOL;
            echo CHTML::modeloError($libro, "fecha_lanzamiento").PHP_EOL;

        
            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "unidades", []).PHP_EOL;
            echo CHTML::modeloNumber($libro, "unidades", ["placeholder" => "Debe ser mayor de -1"]).PHP_EOL;
            echo CHTML::modeloError($libro, "unidades").PHP_EOL;


            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "precio_venta", []).PHP_EOL;
            echo CHTML::modeloText($libro, "precio_venta", ["size" => 50, "placeholder" => "Debe ser mayor de 0"]).PHP_EOL;
            echo CHTML::modeloError($libro, "precio_venta").PHP_EOL;

            echo "<br>".PHP_EOL;

            echo CHTML::modeloLabel($libro, "borrado", []).PHP_EOL;
            echo CHTML::modeloListaRadioButton($libro, "borrado", [0=>"NO", 1=> "SI"], " ", []).PHP_EOL;
            echo CHTML::modeloError($libro, "borrado").PHP_EOL;

            echo "<br>".PHP_EOL;
            echo CHTML::modeloLabel($libro, "foto", []).PHP_EOL;
            echo CHTML::campoHidden("MAX_FILE_SIZE", 100000000, []).PHP_EOL;
            echo CHTML::modeloFile($libro, "foto",["accept" => "image/*"]).PHP_EOL;
            echo CHTML::modeloError($libro, "foto").PHP_EOL;


            echo "<br>".PHP_EOL;
            echo "<br>".PHP_EOL;
            echo CHTML::campoBotonSubmit("Modificar producto", ["class" => "boton", "name" => "modificarLibroInput"]).PHP_EOL;
            echo CHTML::campoBotonReset("Limpiar campos", ["class" => "boton"]).PHP_EOL;

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

            echo CHTML::botonHtml(CHTML::link("Ver libro", ["libros", "verLibro/id=".$libro->cod_libro]), ["class"=>"boton"]).PHP_EOL;


            if ($libro->borrado === 0){
                echo CHTML::botonHtml(CHTML::link("Borrar libro", ["libros", "borrarLibro/id=".$libro->cod_libro]), ["class"=>"boton"]).PHP_EOL;
            }


        echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


?>