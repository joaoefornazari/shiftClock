<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>PHP Training</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="./clock.js"></script>
		<script>
			setInterval(() => {
				$('#time').html(Date().substring(16, 25))
			}, 0);
		</script>
  </head>
  <body>

    <?php
			include_once("function.php");
			include_once("dbconnect.php");
    ?>

    <div id="main-body-div">
      <h1>Workshift PHP</h1>

      <div id="clock-div" style="display: flex; flex-flow: column; justify-content: center; align-items: center;">
        <p>Hora:</p>
        <div id="clock">
          <span id="time"></span>
        </div>
				<select id="option-list">
					<option>Selecione uma opção...</option disabled>
					<option>Exportar para Excel</option>
					<option>Permitir intervalos</option>
				</select>
      </div>  

      <div id="workshift-functions">
        <button class="shift-buttons" id="beginShift" onclick="saveShiftTime(1)">Entrada</button>
        <button class="shift-buttons" id="goToLunch" onclick="saveShiftTime(2)">Almoço</button>
        <button class="shift-buttons" id="backFromLunch" onclick="saveShiftTime(3)">Saída do almoço</button>
        <button class="shift-buttons" id="endShift" onclick="saveShiftTime(4)">Saída</button>
      </div>

      <div id="workshift-table-div">
        <table id="workshift-table">
          <tr>
            <th> Entrada </th>
            <th> Saída para almoço </th>
            <th> Retorno do almoço </th>
            <th> Saída </th>
          </tr>
        </table>
      </div>
    </div>
  </body>
</html>