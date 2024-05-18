<?php
	
	//archivo con las acciones principales 
	//que se ejecutarán mediante ajax

	require_once '../../includes/config.inc.php'; 	//configuración
	require_once ROOT.'func/funciones.inc.php'; 	//cargamos funciones generales
	require_once 'action_funciones.php';

	//conexión a bd intranet
	$con = conectarBD ("bd_intranetv3");
	if (!is_object($con)) {
		$respuesta = array (
			'estado' => "Error en la conexión - ".__LINE__,
			'concursos' => ""
		);
		echo json_encode($respuesta);	
		die();
	}

	$action = 	$_POST ['action'] ?? "";	//accion a realizar
	$concurso = $_POST ['concurso'] ?? "";	//concurso
	$idm = 		$_POST ['idm'] ?? "";		//id a marcar

	$msn = "ok";
	
	//pasa SIGUIENTE ejemplar
	if ($action == 'sumar'){
		$sql = $res = $row = $orden = "";
		
		//recibimos ID del registro activo
		$orden = leer($con, $concurso);
		
		//en el caso de no recibir un array, es que recibimos un ERROR
		if (!is_array($orden)) {
			$msn = $orden." - ".__LINE__;
			
			if (escribir_primer_registro($con, $concurso) === false)
				$msn = "Error escribir primer registro ".__LINE__;
		
		//si recibimos id, pasamos al siguiente
		} else {
			$sql = "SELECT id FROM rotulos WHERE concurso = '$concurso' AND orden > $orden ORDER BY orden ASC LIMIT 1";
			if ($res = mysqli_query ($con, $sql)){
				if (mysqli_num_rows($res) >  0){
					$row = mysqli_fetch_array($res);
					//mandamos activar el ID recibido
					if (escribir($con, $row[0], $concurso) === false)
						$msn = "Error escribir ".__LINE__;
				} else {
					$msn = 1; //no hay mas registros, no hacemos nada
				}
			} else {
				$msn = "Error siguiente ".__LINE__;
			}
		}
		
		$respuesta = array (
			'estado' => $msn
		);
		
	//pasa ANTERIOR ejemplar
	} elseif ($action == 'restar'){
		$sql = $res = $row = $orden = "";

		//recibimos ID del registro activo				
		$orden = leer($con, $concurso);
		
		//si no recibimos ID, marcamos el primero
		if ($orden === false) {
			if (escribir_primer_registro($con, $concurso) === false)
				$msn = "Error escribir primer registro $concurso".__LINE__;
			
		//si recibimos id, pasamos al anterior
		} else {
			$sql = "SELECT id FROM rotulos WHERE concurso = '$concurso' AND orden < $orden ORDER BY orden DESC LIMIT 1";
			if ($res = mysqli_query ($con, $sql)){
				if (mysqli_num_rows($res) >  0){
					$row = mysqli_fetch_array($res);
					//mandamos activar el ID recibido
					if (escribir($con, $row[0], $concurso) === false)
						$msn = "Error escribir ".__LINE__;
				} else {
					$msn = 1; //no hay mas registros, no hacemos nada
				}	
			} else {
				$msn = "Error anterior ".__LINE__;
			}
		}

		$respuesta = array (
			'estado' => $msn
		);

	//MARCAR ejemplar seleccionado de la tabla
	} elseif ($action == 'marcar'){
		if (escribir($con, $idm, $concurso) === false)
			$msn = "Error marcar ".__LINE__;

		$respuesta = array (
			'estado' => $msn
		);

		
	//devuelve ejemplar ACTIVO
	} elseif ($action == 'activo'){

		$activo = $tabla = $puntuado = [];

		//devolvemos TABLA ejemplares y ACTIVO
		$sql = $res = $row = "";
		$sql = "SELECT * FROM rotulos WHERE concurso = '$concurso'";
		if ($res = mysqli_query ($con, $sql)){
			if (mysqli_num_rows($res) < 1 ){
				$msn = "No hay registros para ese concurso ".__LINE__;
			} else {
				while ($row = mysqli_fetch_assoc($res)){
					$tabla [] = $row;
					//si el registro es ACTIVO, devolvemos registro ACTIVO
					if ($row['activo'] == 1)
						$activo = $row;
				}
				
				if (empty($activo)){
					if (escribir_primer_registro($con, $concurso) === false)
						$msn = "Error escribir primer registro ".__LINE__;
					else
						$msn = 1; //para volvar a cargar despues de marcar el primero
				}
			}
		} else {
			if (escribir_primer_registro($con, $concurso) === false)
				$msn = "Error escribir primer registro ".__LINE__;
		}
		
		//devolvemos ultimo puntuado P1
		$sql = $res = $row = $puntuado = "";
		$sql = "SELECT * FROM rotulos WHERE concurso = '$concurso' AND (p1 != '' OR p2 != '' OR p3 != '') ORDER BY actualizado DESC LIMIT 1";
		if ($res = mysqli_query ($con, $sql)){
			 if (mysqli_num_rows($res) == 1 && $row = mysqli_fetch_assoc($res)){
			 	if ($row['p1']!=1 && $row['p2']!=1 && $row['p3']!=1)
					$puntuado = $row;
			 	else
			 		$puntuado = 1;
			}
		}

		$respuesta = array (
			'estado' => $msn,
			'tabla' => $tabla,
			'activo' => $activo,
			'puntuado' => $puntuado,
		);
		

	} elseif ($action == "concursos"){
		$sql = $res = $row = "";
		$concursos = [];
		$sql = "SELECT concurso FROM rotulos GROUP BY concurso ORDER BY concurso ";
		if ($res = mysqli_query ($con, $sql)){
			if (mysqli_num_rows($res) > 0)
				while ($row = mysqli_fetch_assoc($res))
					$concursos [] = $row;		
		}

		$respuesta = array (
			'estado' => $msn,
			'concursos' => $concursos
		);


	} else {
		echo "Error: no action";
	}

	echo json_encode($respuesta);	
	die();