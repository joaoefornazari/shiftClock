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

        function cleanTable() {
          const newTable = `
              <tbody><tr>
                <th> Entrada </th>
                <th> Saída para almoço </th>
                <th> Retorno do almoço </th>
                <th> Saída </th>
              </tr>
            </tbody>
          `;
          $('#workshift-table').html(newTable);
        }

        async function saveDataOnBank() {
          const tableData = $('#workshift-table > tr > td').map(cell => {
            return $(cell).val();
          })
          await $.post(`./function.php?data=1&tableData=${tableData}`);
        }

        async function loadDataFromBank() {
          const tableData = $.get('./function.php?data=GET_SHIFT_TIMES');
          let i = 1;
          tableData.forEach(cell => {
            time = cell;
            cleanTable();
            refreshTable(i);
            i++;
            if (i > 4) i = 1;
          })
        }
      }
    </script>
  </head>
  <body>

    <?php
      include("function.php");

      $databaseConnection = connectToDatabase();
      if ($databaseConnection != 'OK') {
        echo "alert($databaseConnection);";
      }
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
        <button id="beginShift" onclick="saveEnterShift()">Entrada</button>
        <button id="goToLunch" onclick="saveEnterLunch()">Almoço</button>
        <button id="backFromLunch" onclick="saveEndLunch()">Saída do almoço</button>
        <button id="endShift" onclick="saveEndShift()">Saída</button>
      </div>

      <div id="workshift-databank">
        <button id="saveData" onclick="saveDataOnBank()">Salvar Dados</button>
        <button id="loadData" onclick="loadDataFromBank()">Carregar Dados</button>
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