
	let concurso = "";
	
	/*al cargar la pagina*/
	$(document).ready (function(e) {
		
		//al cargar la pagina, si no hay concurso seleccionado...
		if ($("#concursos").html() == "") {
			cargar_concursos (); 
		}
	
		/*ocultar capas con boton ojo*/
	    $("#ojo_capa1_off").on ("click", function(e) {
	    	$("#ojo_capa1_off").hide();
	    	$(".capa1").css("visibility","hidden");
	    	$("#ojo_capa1_on").show();
		});
		
	    $("#ojo_capa1_on").on ("click", function(e) {
	    	$("#ojo_capa1_on").hide();
	    	$(".capa1").css("visibility","visible");
	    	$("#ojo_capa1_off").show();
		});

	    $("#ojo_capa2_off").on ("click", function(e) {
	    	$("#ojo_capa2_off").hide();
	    	$(".capa2").css("visibility","hidden");
	    	$("#ojo_capa2_on").show();
		});
		
	    $("#ojo_capa2_on").on ("click", function(e) {
	    	$("#ojo_capa2_on").hide();
	    	$(".capa2").css("visibility","visible");
	    	$("#ojo_capa2_off").show();
		});
	
	});


	//cargar listado de concurso en select
	function cargar_concursos () {
		var xmlhttp = new XMLHttpRequest();
		var url = "/pages/rotulos/aplicacion/action.php";
		xmlhttp.onreadystatechange = function () {
		    if (this.readyState == 4 && this.status == 200) {
		    	var resp = xmlhttp.responseText,
					data = JSON.parse(resp);
				if (data.estado == 'ok'){
					var concursos = "<option value=''>Seleccionar concurso...</option>";
					for (let c = 0; c < data.concursos.length; c++){
						concursos += "<option value='" + data.concursos[c].concurso + "'>" + data.concursos[c].concurso + "</option>";
					}
					document.getElementById('concursos').innerHTML = concursos;
				} else {
					document.getElementById('capa1').innerHTML = data.estado;
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send("action=concursos");
	}
	  
	
	//seleccion en el select
	function seleccionar_concurso (){
		concurso = document.getElementById('concursos').value;
		if (concurso == "") {
			document.getElementById('capa1').innerHTML = "";
			document.getElementById('capa2').innerHTML = "";
			document.getElementById('tabla').innerHTML = "<tr><td>Sin registros...</td></tr>";
		} else {
			actualizar();
			setInterval('actualizar()', 7000);
		}
	}

	//carga ejemplare anterior
	function restar () {
		var xmlhttp = new XMLHttpRequest();
		var url = "/pages/rotulos/aplicacion/action.php";
		xmlhttp.onreadystatechange = function () {
		    if (this.readyState == 4 && this.status == 200) {
		    	var resp = xmlhttp.responseText,
					data = JSON.parse(resp);
				if (data.estado == 'ok'){
					//console.log ('restar ok');
					actualizar();
				} else if (data.estado == 1){
					//console.log ('restar ok - primer reg');
				} else {
					console.log ('ERROR restar: ' + data.estado);
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("action=restar&concurso=" + concurso);
		
	}

	
	//carga ejemplar posterior
	function sumar () {
		var xmlhttp = new XMLHttpRequest();
		var url = "/pages/rotulos/aplicacion/action.php";
		xmlhttp.onreadystatechange = function () {
		    if (this.readyState == 4 && this.status == 200) {
		    	var resp = xmlhttp.responseText,
					data = JSON.parse(resp);
				if (data.estado == 'ok'){
					//console.log ('sumar ok');
					actualizar();
				} else if (data.estado == 1){
					//console.log ('sumar ok - último reg');
				} else {
					console.log ('ERROR sumar: ' + data.estado);
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send("action=sumar&concurso=" + concurso);
	}


	//actualizamos datos
	function actualizar () {
		var xmlhttp = new XMLHttpRequest();
		var url = "/pages/rotulos/aplicacion/action.php";
		xmlhttp.onreadystatechange = function () {
		    if (this.readyState == 4 && this.status == 200) {
				var resp = xmlhttp.responseText,
					data = JSON.parse(resp);
				if (data.estado == 'ok') {
					
					//montamos registro ACTIVO
					var c1 = "<span class='ejemplar'>" + data.activo.ejemplar+ "</span><br>";
					c1 += "<span class='ganaderia'>" + data.activo.ganaderia + "</span><br>";
					c1 += "<span class='jinete'>" + data.activo.jinete + "</span>";
					document.getElementById('capa1').innerHTML = c1;

					//montamos tabla participantes
					var tabla = "";
					var fila_activa = "";
					for (let f = 0; f < data.tabla.length; f++){
						tabla += "<tr";
						if (data.tabla[f].activo == 1) {
							tabla += " class='sel' ";
							fila_activa = f + 1;
						}
						tabla += " onclick ='marcar(" + data.tabla[f].id + ");'> ";
						tabla += "<td>" + data.tabla[f].orden + "</td>";
						tabla += "<td>" + data.tabla[f].ejemplar + "</td>";
						tabla += "<td>" + data.tabla[f].p1 + "</td>";
						tabla += "<td>" + data.tabla[f].p2 + "</td>";
						tabla += "<td>" + data.tabla[f].p3 + "</td>";
						tabla += "</tr>";
					}
					document.getElementById('tabla').innerHTML = tabla;
					
					var filaSeleccionada = document.getElementById('tabla').getElementsByTagName('tr')[fila_activa];
					try {
						filaSeleccionada.scrollIntoView({ behavior: 'smooth', block: 'center' });
					} catch {
						//console.log('caso ultimo registro');
					}

					//montamos capa notas
					var c2 = "";
					
					/*if (typeof data.puntuado === 'object' && data.puntuado.p1 != "") {*/
					/* esto se cancela para usar cuando tengamos acceso a la PAPI de concursos
					if (typeof data.puntuado === 'object') {
						c2 = "ÚLTIMO PUNTUADO | <strong>" + data.puntuado.ejemplar + "</strong>";
						if (data.puntuado.p1 != "")
							if (data.puntuado.p2 != "" && data.puntuado.p1 > data.puntuado.p2)
								c2 += " | <strong>P1: " + data.puntuado.p1 + "</strong>";
							else
								c2 += " | P1: " + data.puntuado.p1;
						if (data.puntuado.p2 != "")
							if (data.puntuado.p1 != "" && data.puntuado.p2 > data.puntuado.p1)
								c2 += " | <strong>P2: " + data.puntuado.p2 + "</strong>";
							else
								c2 += " | P2: " + data.puntuado.p2;
					} else {
						if (data.puntuado == 1)
							c2 = "";
					}
					document.getElementById('capa2').innerHTML = c2;
					*/
					
					
				} else if (data.estado == 1) {
					actualizar ();
				}		
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send("action=activo&concurso=" + concurso);
		
		document.getElementById('bt2').focus();
	} 
	
	//selecciona un ejemplar desde la tabla
	function marcar (idm) {
		var xmlhttp = new XMLHttpRequest();
		var url = "/pages/rotulos/aplicacion/action.php";
		xmlhttp.onreadystatechange = function () {
		    if (this.readyState == 4 && this.status == 200) {
		    	var resp = xmlhttp.responseText,
					data = JSON.parse(resp);
				if (data.estado == 'ok'){
					//console.log ('marcar ok');
					actualizar();
				} else {
					console.log ('ERROR marcar: ' + data.estado);
				}
			}
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send("action=marcar&concurso=" + concurso + "&idm=" + idm);
	}


