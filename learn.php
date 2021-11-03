<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>PHP Training</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script>
      setInterval(() => {
        $('#time').html(Date().substring(16, 25))
      }, 0);

      let time = 0;

      const COLUMN = {
        BEGIN_SHIFT: 1,
        BEGIN_LUNCH: 2,
        END_LUNCH: 3,
        END_SHIFT: 4.
      };

      let row;

      let formerColumn = 4;

      async function saveEnterShift() {
        time = await $.get('./function.php?data=1');
        refreshTable(COLUMN.BEGIN_SHIFT);
      }

      async function saveEnterLunch() {
        time = await $.get('./function.php?data=2');
        refreshTable(COLUMN.BEGIN_LUNCH);
      }

      async function saveEndLunch() {
        time = await $.get('./function.php?data=3');
        refreshTable(COLUMN.END_LUNCH);
      }

      async function saveEndShift() {
        time = await $.get('./function.php?data=4');
        refreshTable(COLUMN.END_SHIFT);
      }

      function refreshTable(column) {
        switch (column) {
          case 1: {

            if (formerColumn !== 4) {
              alert("Encerre o turno antes de registrar entrada novamente!");
              return;
            }

            const table = $("#workshift-table");
            row = table[0].insertRow(-1);
            const cell = row.insertCell(0);
            
            cell.appendChild(document.createTextNode(time));
            formerColumn = 1;
            
          } break;

          case 2: {

            if (formerColumn !== 1) {
              alert("Inicie o turno antes de registrar saída para almoço!");
              return;
            }

            const cell = row.insertCell(1);
            
            cell.appendChild(document.createTextNode(time));
            formerColumn = 2;
                     
          } break;

          case 3: {

            if (formerColumn !== 2) {
              alert("Inicie o almoço antes de encerrá-lo!");
              return;
            }

            const cell = row.insertCell(2);
            
            cell.appendChild(document.createTextNode(time));
            formerColumn = 3;

          } break;

          case 4: {

            if (formerColumn !== 3) {
              alert("Encerre o almoço antes de encerrar o turno!");
              return;
            }

            const cell = row.insertCell(3);
            
            cell.appendChild(document.createTextNode(time));
            formerColumn = 4;

          } break;
        }
      }
    </script>
  </head>
  <body>

    <?php
      include("function.php")
    ?>

    <div id="main-body-div">
      <h1>Workshift PHP</h1>

      <div id="clock-div" style="display: flex; flex-flow: column; justify-content: center; align-items: center;">
        <p>Time:</p>
        <div id="clock">
          <span id="time"></span>
        </div>
      </div>  

      <div id="workshift-functions">
        <button id="beginShift" onclick="saveEnterShift()">Bater ponto de entrada</button>
        <button id="goToLunch" onclick="saveEnterLunch()">Bater saída para almoço</button>
        <button id="backFromLunch" onclick="saveEndLunch()">Bater retorno do almoço</button>
        <button id="endShift" onclick="saveEndShift()">Bater ponto de saída</button>
      </div>

      <div id="workshift-table-div">
        <table id="workshift-table">
          <tr>
            <th> Entrada no serviço </th>
            <th> Saída para almoço </th>
            <th> Retorno para almoço </th>
            <th> Saída do serviço </th>
          </tr>
        </table>
      </div>
    </div>
  </body>
</html>