<?php

	/**
	 * GGeneral es una clase que aporta diversos métodos estáticos
	 */
	class CGeneral{
		
		/**
		 * Método que convierte una cadena de fecha en el formato "aa-mm-dd"
		 * usado por Mysql al formato de fecha español (dd/mm/aa) devolviendo la
		 * cadena obtenida
		 *
		 * @param string $fecha Fecha original en formato "aa-mm-dd"
		 * @return string Devuelve una cadena con la fecha en formato dd/mm/aa
		 */
		public static function fechaMysqlANormal(string $fecha):string 
		{
			$fechaAux=explode("/",$fecha);
			if (count($fechaAux)==3)
			    return $fecha;
			
			$fecha=explode("-", $fecha);
			$fecha=date('d/m/Y',mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]));
			
			return $fecha;
		}

		/**
		 * Método que convierte una cadena de fecha en el formato dd/mm/aa al
		 * formato aa-mm-dd, devolviendo la cadena.
		 *
		 * @param string $fecha Fecha en formato dd/mm/aa
		 * @return string Cadena con la fecha en formato aa-mm-dd
		 */
		public static function fechaNormalAMysql(string $fecha):string
		{
			$fechaAux=explode("-",$fecha);
			if (count($fechaAux)==3)
			    return $fecha;
			
			$fecha=explode("/", $fecha);
			$fecha=date('Y-m-d',mktime(0,0,0,$fecha[1],$fecha[0],$fecha[2]));
			
			return $fecha;
			
		}

		
		/**
		 * Método que escapa en la cadena de entrada el carácter '.
		 * Se usa para prevenir el ataque por inyección de código en 
		 * SQL
		 * 
		 * @param string $cadena Cadena a escapar
		 * @return string
		 * 
		 */
		public static function addSlashes(string $cadena):string 
		{
			return str_replace("'", "''", $cadena);
		}
		
		
		/**
		 * Elimina el escape para una cadena dada
		 * 
		 * @param string $cadena 
		 * @return string
		 * 
		 */
		public static function stripSlashes($cadena)
		{
			return str_replace("''", "'", $cadena);
		}


	/**
	 * Realiza una petición CURL a un servidor, servicio web, etc
	 *
	 * @param string $link  Direccion a la que realizar la petición
	 * @param string $metodo Metodo de la petición: GET, POST, PUT, DELETE
	 * @param string $parametros Parametros a incluir en la petición
	 * @param boolean $proxy Usa proxy para salir a internet
	 * @param string $dirproxy  Dirección del proxy
	 * @return string|false  Falso en caso de error, la cadena que devuelve la
	 *                      peticion en caso de exito
	 */
	public static function getCURL(string $link,string $metodo = "POST",string $parametros = "",
									bool $proxy = false,string $dirproxy = ""): string|false {
		
		//metodos posibles 
		$metodos = ["POST", "GET", "DELETE", "PUT"];
		$metodo = mb_strtoupper($metodo);
		if (!in_array($metodo, $metodos))
			return false;

		//creo una sesión CUrl
		$enlaceCurl = curl_init();

		//se indican las opciones para una petición HTTP Post

		//método de la petición
		switch ($metodo) {
			case 'GET':
				curl_setopt($enlaceCurl, CURLOPT_HTTPGET, 1);
				if ($parametros != "")
					$link .= "?$parametros";
				break;
			case 'POST':
				curl_setopt($enlaceCurl, CURLOPT_POST, 1);
				break;
			case 'PUT':
				curl_setopt($enlaceCurl, CURLOPT_CUSTOMREQUEST, "PUT");
				break;
			case 'DELETE':
				curl_setopt($enlaceCurl, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
		}
		curl_setopt($enlaceCurl, CURLOPT_HEADER, 0);
		curl_setopt($enlaceCurl, CURLOPT_RETURNTRANSFER, 1);

		//direccion url de la petición
		curl_setopt(
			$enlaceCurl,
			CURLOPT_URL,
			$link
		);

		//parametros si el método es distinto de GET
		if (!empty($parametros) && in_array($metodo, ["POST", "PUT", "DELETE"])) {
			curl_setopt($enlaceCurl, CURLOPT_POSTFIELDS, $parametros);
		}

		// PROXY
		if ($proxy) {
			curl_setopt($enlaceCurl, CURLOPT_PROXY, $dirproxy);
		}
		//ejecuto la petición
		$res = curl_exec($enlaceCurl);
		//cierro la sesión
		curl_close($enlaceCurl);

		return $res;
	}
}
