<!DOCTYPE html>
<html>  
	<head> 
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Rótulos APP Rotulación (ANCCE)</title>
		<link rel="stylesheet" href="rotulos_app.css" />
		<link rel="icon" type="image/x-icon" href="../../../img/favicon.ico">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	</head>
	<body>

		<div class="contenedor">
			
			<div style=" display: flex;">
				<div id="capa1" class="capa1" style=" width: 100%"></div>

				<div class="ojos" style=" margin: 20px 0 0 30px">
					<span id="ojo_capa1_off" class="material-symbols-rounded">visibility_off</span>
					<span id="ojo_capa1_on" class="material-symbols-rounded" style="display: none;">visibility</span>
				</div>
			</div>

			<div style=" display: flex;">
				<div class="capa2 scroll-container" style=" width: 100%">
					<div id="capa2" class="scroll-text"></div>
				</div>

				<div class="ojos" style=" margin: 20px 0 0 30px">
					<span id="ojo_capa2_off" class="material-symbols-rounded">visibility_off</span>
					<span id="ojo_capa2_on" class="material-symbols-rounded" style="display: none;">visibility</span>
				</div>
			</div>

			<div class="botonera">
				<button id="bt1" class="bt" onclick="restar();">
					<span class="material-symbols-rounded">arrow_back_ios</span>
				</button>
				<button id="bt2" class="bt" onclick="sumar();">
					<span class="material-symbols-rounded">arrow_forward_ios</span>
				</button>
			</div>
			
			<div class="capa_tabla">
				<select class="concursos" name="concursos" id="concursos" onchange="seleccionar_concurso();"></select>
			</div>
			
			<div class="capa_tabla">
				<table id="tabla" class="tabla">
					<tr><td>Sin registros...</td></tr>
				</table>
			</div>
							
		</div>
				
	</body>

	<script src="rotulos_app.js"></script>

</html>
