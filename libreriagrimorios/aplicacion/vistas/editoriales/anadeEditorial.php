<?php


echo CHTML::dibujaEtiqueta("h2", [], "Añadir editorial ", true).PHP_EOL;


//div para editorial
echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDLIBROS"], null, false).PHP_EOL;


    //información
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDEditorial"], null, false).PHP_EOL;
    echo CHTML::iniciarForm("#", "POST", []).PHP_EOL;

            echo CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre: ", true).PHP_EOL;
            echo CHTML::campoText("nombre", $datos["nombre"], []).PHP_EOL;
            echo "<br>".PHP_EOL;
            if (isset($errores["nombre"])){
                echo CHTML::dibujaEtiqueta("div", ["class" => "error"], null, false).PHP_EOL;
                    foreach ($errores["nombre"] as $error){
                        echo "$error <br> ".PHP_EOL;
                    }
                echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
            }  
            

            //fundador
            echo CHTML::dibujaEtiqueta("label", ["for" => "fundador"], "Fundador: ", true).PHP_EOL;
            echo CHTML::campoText("fundador", $datos["fundador"], [], []).PHP_EOL;
            echo "<br>".PHP_EOL;
            if (isset($errores["fundador"])){
                echo CHTML::dibujaEtiqueta("div", ["class" => "error"], null, false).PHP_EOL;
                    foreach ($errores["fundador"] as $error){
                        echo "$error <br> ".PHP_EOL;
                    }
                echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
            }  

            //fecha
            echo CHTML::dibujaEtiqueta("label", ["for" => "fecha_creacion"], "Fecha de creación: ", true).PHP_EOL;
            echo CHTML::campoText("fecha_creacion", $datos["fecha_creacion"],["placeholder" => "dd/mm/aaaa"]).PHP_EOL;
            echo "<br>".PHP_EOL;
            if (isset($errores["fecha_creacion"])){
                echo CHTML::dibujaEtiqueta("div", ["class" => "error"], null, false).PHP_EOL;
                    foreach ($errores["fecha_creacion"] as $error){
                        echo "$error <br> ".PHP_EOL;
                    }
                echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
            }  

            //Cese
            echo CHTML::dibujaEtiqueta("label", ["for" => "cese"], "Cese: ", true) . PHP_EOL;
            echo CHTML::campoListaRadioButton("cese", $datos["cese"],[0 => "NO", 1=>"SI"], " ", []).PHP_EOL;
            echo "<br>" . PHP_EOL;
            if (isset($errores["cese"])){
                echo CHTML::dibujaEtiqueta("div", ["class" => "error"], null, false).PHP_EOL;
                    foreach ($errores["cese"] as $error){
                        echo "$error <br> ".PHP_EOL;
                    }
                echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
            }  

            //Historia
            echo CHTML::dibujaEtiqueta("label", ["for" => "historia"], "Historia: ", true).PHP_EOL;
            echo "<br>" . PHP_EOL;

            echo CHTML::campoTextArea("historia", $datos["historia"],  ["rows" => 10, "cols" => 70]).PHP_EOL;
            echo "<br>" . PHP_EOL;
            if (isset($errores["historia"])){
                echo CHTML::dibujaEtiqueta("div", ["class" => "error"], null, false).PHP_EOL;
                    foreach ($errores["historia"] as $error){
                        echo "$error <br> ".PHP_EOL;
                    }
                echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
            }  

    
            echo CHTML::campoBotonSubmit("Añadir editorial",  ["name" => "inputAddEditorial" , "class" => "boton", "style" => "margin-top: 1%"]).PHP_EOL;
            echo CHTML::campoBotonReset("Limpiar campos", ["class" => "boton"]).PHP_EOL;


        echo CHTML::finalizarForm().PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

    
    //logo
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDEditorialImg"], null, false).PHP_EOL;
        echo CHTML::imagen("../../imagenes/logosEditoriales/" .$datos["logo"] , "Logo: {$datos['logo']}").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;




echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


    //Operaciones
    echo CHTML::dibujaEtiqueta("div", ["class" => "operaciones"], null, false).PHP_EOL;

        echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

            echo CHTML::dibujaEtiqueta("legend", [], "Operaciones disponibles", true).PHP_EOL;

            echo CHTML::botonHtml(CHTML::link("Volver atrás", ["editoriales", "editorialCRUD"]), ["class"=>"boton"]).PHP_EOL;
            echo CHTML::botonHtml(CHTML::link("Volver al inicio", ["inicial", "index"]), ["class"=>"boton"]).PHP_EOL;


        echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;




?>