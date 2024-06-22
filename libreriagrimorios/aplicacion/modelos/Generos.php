<?php

/**
 * Clase para el modelo de generos
 * de la tabla generos, contiene los parámetros correspondiente
 * y sus restricciones contiene el método de dameGeneros
 * que en funcion del parámetro pasado, nos devolvera un genero
 * o una lista de generos actuales de la tabla generos o false en caso de error
 */
class Generos extends CActiveRecord {


    /**
     * Función para definir el nombre del 
     * modelo editoriales
     *
     * @return String devuelve el nombre de géneros
     */
    protected function fijarNombre(): string
    {
        return "editoriales";
    }


   /**
    * 
    * Función que devuelve un array con
    *   los parámetros del modelo géneros 
    * @return Array devuelve array con parámetros
    */
    protected function fijarAtributos(): array
    {
        return array ("cod_genero", "desc_genero");
    }


    /**
     * devuelve el nombre de la tabla
     * del modelo correspondiente
     *
     * @return String devuelve nombre de la tabla
     */
    protected function fijarTabla(): string
    {
        return "generos";
    }


    /**
     * Devuelve el nombre de la primary key
     * de la tabla del modelo
     *
     * @return String devuelve el nombre de la primary key
     */
    protected function fijarId(): string
    {
        return "cod_genero";
    }


    /**
     * Se devuelve un array con los diferentes campos
     *
     * @return Array devuelve array de campos de generos
     */
    protected function fijarDescripciones(): array
    {
        return array ("cod_genero" => "Código de género",
                    "desc_genero" => "Género");
    }

    /**
     * devuelve un array con las restricciones del
     * modelo de genero
     *
     * @return Array de las restricciones
     */
    protected function fijarRestricciones(): array
    {
        return array (
            array("ATRI" => "desc_genero", "TIPO" => "REQUERIDO"),
            array("ATRI" => "desc_genero", "TIPO" => "CADENA", "TAMANIO" => 20)
        
        );

    }


    /**
     * Función que inicializa 
     * los valores de los parámetros
     * cuando se inicializa el modelo
     *
     * @return Void no devuelve nada
     */
    protected function afterCreate(): void
    {
        $this->cod_genero = 0;
        $this->desc_genero = "";
    }


    /**
     * Función que tras hacer consulta a la base de datos
     * se tratan los datos
     *
     * @return Void no devuelve nada
     */
    protected function afterBuscar(): void
    {
        $this->cod_genero = intval($this->cod_genero);
    
    }



    /**
     * Método de clase generos 
     * que recibe como parámetro un null o un entero
     * 
     * Si recibe un null, devuelve un array con todos los generos
     * 
     * Si recibe un entero, se comprueba que el cod existe
     * y devuelve su descripción, si no se devuelve false
     *
     * @param integer|null $cod_genero cod del genero o parámetro vacio
     * @return Array | String | False array si llega un null, string si se encuentra codigo
     * false si no se encuentra nada
     */
    public static function dameGenero (?int $cod_genero = null): Array | String | False{


        $objGeneros = new Generos ();

        $arrayGeneros = [];

        foreach($objGeneros->buscarTodos() as $clave => $valor){

            $arrayGeneros[intval($valor["cod_genero"])] = $valor["desc_genero"];
        }

        if ($cod_genero === null){
            return $arrayGeneros;
        }
        else{

            if (isset($arrayGeneros[$cod_genero])){
                return $arrayGeneros[$cod_genero];
            }
            else{
                return false;
            }
        }
    }
}


?>