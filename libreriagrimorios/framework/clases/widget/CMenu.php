<?php

/**
 * Componente CMenu que hereda del CWidget
 * lo usamos en la plantilla de la aplicación
 * 
 * Será la etiqueta barra nav, la cual será responsive
 * y mostrará un menu de tipo Android
 */
class CMenu extends CWidget {


    //Variables de instancia
    private array $_contenido = []; 
    private array $_atributosHTML = [];

    /**
     * Constructor de la clase CMenu
     * recibe como parametros los atributos html en array
     * y el contenido que hay dentro del menu en formato string
     *
     * @param Array $contenido información contenida dentro del CMenu
     * @param Array $atributosHTML atributos de HTML
     */
    public function __construct(array $contenido = [], array $atributosHTML = []){
        
        $this->_contenido = $contenido;

        if (!isset($atributosHTML["class"])){
            $this->_atributosHTML["class"] = "menu"; //clase menu en CSS
        }

    }


    /**
     * Redefinción del método dibujaApertura(), el cual se encarga de abrir las etiquetas que engloban al contenido del componente
     * formando una caja
     * @return String -> Cadena con las etiquetas y contenido del menu
     */
    public function dibujaApertura(): string
    {

        ob_start();

        $idBotonMenu = CHTML::generaID();
        $botonUlNav = CHTML::generaID();
        
        echo CHTML::dibujaEtiqueta("nav", $this->_atributosHTML, null, false).PHP_EOL;
            // echo CHTML::botonHtml("&#9776;", ["id" => $idBotonMenu, "class" => "botonMenu", "onclick" => "activarMenu('$botonUlNav')"]).PHP_EOL;
            echo CHTML::dibujaEtiqueta("ul", ["id" => $botonUlNav], null, false).PHP_EOL;

            foreach ($this->_contenido as $clave => $valor){
                echo $valor.PHP_EOL;
            }
        $contenido = ob_get_contents();
        ob_end_clean();


        return $contenido;
    }



    /**
     * Redefinición del método dibujaFin(), el cual se encarga de cerra las etiquetas abiertas y no cerradas en el 
     * método dibujaApertura()
     * @return string -> Cadena con el cierre de las etiquetas abiertas en dibujaApertura()
     */
    public function dibujaFin(): string
    {
        ob_start();

        
            echo CHTML::dibujaEtiquetaCierre("ul").PHP_EOL;
        echo CHTML::dibujaEtiquetaCierre("nav").PHP_EOL;
        $contenido = ob_get_contents();
        ob_end_clean();

        return $contenido;
    }



    /**
     * Redefinición del método dibujate(), el cual se encarga de dibujar el componente, llamando sucesivamente al 
     * dibujaApertura() y dibujaCierre()
     * @return string -> Cadena con el contenido del componente (dibujaApertura() y dibujaCierre())
     */
    public function dibujate(): string
    {
        return $this->dibujaApertura().$this->dibujaFin();

    }


    /**
     * 
     *
     * @return string
     */
    public static function requisitos(): string
    {
        $codigo=<<<EOF
			function activarMenu(idBotonUlNav){
				
                document.getElementsByClassName("menu")[0].getElementsByTagName("ul")[0]
                let caja = document.getElementById(idBotonUlNav)
  

                if (caja.style.display === "" || caja.style.display === "none"){
                    caja.style.display = 'block';
                }
                else{
                    caja.style.display = 'none';

                }

			}
EOF;

            $codigo =  CHTML::script($codigo);
			return  $codigo;
    }
}



?>