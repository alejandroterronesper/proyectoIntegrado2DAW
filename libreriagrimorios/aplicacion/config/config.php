<?php

	$config=array("CONTROLADOR"=> array("inicial"),
				  "RUTAS_INCLUDE"=>array("aplicacion/modelos"),
				  "URL_AMIGABLES"=>true,
				  "VARIABLES"=>array("autor"=>"Alejandro Terrones Pérez",
				  					"direccion"=>"C/ Mesones nº 18, 1º K"
								),
				  "BD"=>array("hay"=>true,
								"servidor"=>"localhost",
								"usuario"=>"root",
								"contra"=>"2daw",
								"basedatos"=>"grimorios"),


					"Acceso" => array("controlAutomatico" => true),

					"SESION" => array("controlAutomatico" => true),

					"ACL" => array("controlAutomatico" => true)
);
