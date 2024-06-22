<?php


echo CHTML::dibujaEtiqueta("h2", [], "Ver editorial: " .$editorial["nombre"], true).PHP_EOL;


//div para editorial
echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDLIBROS"], null, false).PHP_EOL;


    //información
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDEditorial"], null, false).PHP_EOL;
        echo CHTML::iniciarForm("", "", []).PHP_EOL;

            echo CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre: ", true).PHP_EOL;
            echo CHTML::campoText("nombre", $editorial["nombre"], ["readonly" => true], []).PHP_EOL;

            echo "<br>".PHP_EOL;
                
            

            //fundador
            echo CHTML::dibujaEtiqueta("label", ["for" => "fundador"], "Fundador: ", true).PHP_EOL;
            echo CHTML::campoText("fundador", $editorial["fundador"], ["readonly" => true], []).PHP_EOL;
            echo "<br>".PHP_EOL;


            //fecha
            echo CHTML::dibujaEtiqueta("label", ["for" => "fecha_creacion"], "Fecha de creación: ", true).PHP_EOL;
            echo CHTML::campoText("fecha_creacion", $editorial["fecha_creacion"], ["readonly" => true], []).PHP_EOL;
            echo "<br>".PHP_EOL;

            //Cese
            echo CHTML::dibujaEtiqueta("label", ["for" => "cese"], "Cese: ", true) . PHP_EOL;
            echo CHTML::campoText("cese", $editorial["cese"], ["readonly" => true], []).PHP_EOL;

            echo "<br>" . PHP_EOL;

            //Historia
            echo CHTML::dibujaEtiqueta("label", ["for" => "historia"], "Historia: ", true).PHP_EOL;
            echo "<br>" . PHP_EOL;

            echo CHTML::campoTextArea("historia", $editorial["historia"],  ["readonly" => true, "rows" => 10, "cols" => 70]).PHP_EOL;

        echo CHTML::finalizarForm().PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

    
    //logo
    echo CHTML::dibujaEtiqueta("div", ["class" => "divCRUDEditorialImg"], null, false).PHP_EOL;
        echo CHTML::imagen("../../imagenes/logosEditoriales/" .$editorial["logo"] , "Logo: {$editorial['logo']}").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;




echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;


    //Operaciones
    echo CHTML::dibujaEtiqueta("div", ["class" => "operaciones"], null, false).PHP_EOL;

        echo CHTML::dibujaEtiqueta("fieldset", [], null, false).PHP_EOL;

            echo CHTML::dibujaEtiqueta("legend", [], "Operaciones disponibles", true).PHP_EOL;

            echo CHTML::botonHtml(CHTML::link("Volver atrás", ["editoriales", "editorialCRUD"]), ["class"=>"boton"]).PHP_EOL;
            echo CHTML::botonHtml(CHTML::link("Volver al inicio", ["inicial", "index"]), ["class"=>"boton"]).PHP_EOL;
            echo CHTML::botonHtml(CHTML::link("Modificar editorial", ["editoriales", "modificarEditorial/id=".$editorial["cod_editorial"]]), ["class"=>"boton"]).PHP_EOL;


            if ($editorial["cese"] === "No"){
                echo CHTML::botonHtml(CHTML::link("Borrar editorial", ["editoriales", "borrarEditorial/id=".$editorial["cod_editorial"]]), ["class"=>"boton"]).PHP_EOL;
            }


        echo CHTML::dibujaEtiquetaCierre("fieldset").PHP_EOL;
    echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;




?>