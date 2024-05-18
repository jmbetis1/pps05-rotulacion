<?php  

	// FUNCIONES ////////////////////////////////////////////////////////////////////////
	
	//buscamos el id del concurso ACTIVO
	function leer ($con, $concurso) {
		$sql = $res = $row = "";
		$sql = "SELECT orden FROM rotulos WHERE concurso = '$concurso' AND activo = 1";
		if ($res = mysqli_query($con, $sql)){
			if (mysqli_num_rows ($res) == 1){
				$row = mysqli_fetch_array($res);
				return $row[0];
			}
		}
		return false;
	}
	
	//marcamos id del concurso como ACTIVO
	function escribir ($con, $id, $concurso){
		//quitamos ACTIVO de todos los registros del concurso
		$sql = $res = $row = "";
		$sql = "UPDATE rotulos SET activo = 0 WHERE concurso = '$concurso'";
		if (!$res = mysqli_query($con, $sql))
			return false;
		
		//marcamos ACTIVO id recibido
		$sql = $res = $row = "";
		$sql = "UPDATE rotulos SET activo = 1 WHERE id = $id";
		if ($res = mysqli_query($con, $sql)){
			if (mysqli_affected_rows($con) ==1){
				return true;
			}
		}
		return false;
	}

	//devuelve el primer registro de un concurso
	function primer_registro ($con, $concurso){
		$sql = $res = $row = "";
		$sql = "SELECT id FROM rotulos WHERE concurso = '$concurso' ORDER BY orden ASC LIMIT 1";
		if ($res = mysqli_query($con, $sql)){
			if (mysqli_num_rows($res) == 1){
				$row = mysqli_fetch_array($res);
				return $row[0];
			}
		}
		return false;
	}
	
	//funcion AGRUPA escribir primer registro
	function escribir_primer_registro ($con, $concurso) {
		$idp = primer_registro ($con, $concurso);
		if ($idp === false){
			return false;
		} else {
			if (escribir ($con, $idp, $concurso) === false)
				return false;
		} 
		return true;
	}
		