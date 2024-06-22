<?php
class CCaja extends CWidget{
    //Variables de instancia
    private string $_titulo;
    private string $_contenido;
    private array $_atributosHTML = [];

    //Constructor
    public function __construct(string $titulo, string $contenido="", array $atributosHTML = [])
    {
        $this->_titulo = $titulo;
        $this->_contenido = $contenido;
        if (!isset($atributosHTML["class"]))
            $this->_atributosHTML["class"] = "caja";

        
    }

    /**
     * Redefinción del método dibujaApertura(), el cual se encarga de abrir las etiquetas que engloban al contenido del componente
     * formando una caja
     * @return string -> Cadena con las etiquetas y contenido de la caja
     */
    public function dibujaApertura(): string
    {
        ob_start();
        
        $idCaja=CHTML::generaID();
        $idBoton=CHTML::generaID();
        
        echo CHTML::dibujaEtiqueta("div", $this->_atributosHTML, "", false).PHP_EOL;
        echo CHTML::dibujaEtiqueta("div", ["class" => "h1Caja"], "", false).PHP_EOL;
        echo CHTML::dibujaEtiqueta("h1", [], $this->_titulo, true).PHP_EOL;
        echo CHTML::botonHtml("Ocultar Información", ["id"=>$idBoton,  "onclick"=>"ocultar('$idBoton','$idCaja')", "class"=>"boton"]).PHP_EOL;
        echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
        echo CHTML::dibujaEtiqueta("div", ["class"=>"formulario","id"=>$idCaja], "", false).PHP_EOL;
        echo $this->_contenido;
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

        echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;
        echo CHTML::dibujaEtiquetaCierre("div").PHP_EOL;

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
     * Redefinición del método requisitos(), el cual se encarga de darle funcionamiento JavaScript a nuestro componente
     * En este caso, cuando se pulsa el boton de colutar se oculta/muestra el contenido del componente
     * @return string -> Cadena con el script que hace que se oculte/muestre la información
     */
    public static function requisitos():string
    {
        $codigo=<<<EOF
			function ocultar(idBoton,idCaja)
			{
				let formulario = document.getElementById(idCaja);
                let boton = document.getElementById(idBoton)
                if(formulario.style.display==='none')
                {
                    formulario.style.display = 'block';
                    formulario.style.transition = 'all 2s';
                    boton.innerHTML = "Ocultar Información";
                }
                else 
                {
                    formulario.style.transition = 'all 2s';
                    formulario.style.display = 'none';
                    boton.innerHTML = "Mostrar Información";
                }
			}
EOF;
			return CHTML::script($codigo);
    }
}