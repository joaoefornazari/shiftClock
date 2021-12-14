let time = "";

const COLUMN = {
	BEGIN_SHIFT: 1,
	BEGIN_LUNCH: 2,
	END_LUNCH: 3,
	END_SHIFT: 4.
};

let row;

let formerColumn = 4;

let shift = {
	begin: "",
	startLunch: "",
	endLunch: "",
	end: "",
};

const today = new Date().toLocaleDateString();

async function saveShiftTime(shiftTime) {
	time = await $.get(`./function.php?time=${shiftTime}`);
	refreshTable(shiftTime);
	console.log(await updateClockDB(shiftTime));
}

async function updateClockDB(shiftTime) {
	switch (shiftTime) {
		case COLUMN.BEGIN_SHIFT: {
			shift.begin = time;
			return await $.post("./dbconnect.php/insertTime", {
				begin: shift.begin,
			});
		} break;

		case COLUMN.BEGIN_LUNCH: {
			shift.startLunch = time;
			return await $.post("./dbconnect.php/insertTime", {
				startLunch: shift.startLunch,
			});
		} break;

		case COLUMN.END_LUNCH: {
			shift.endLunch = time;
			return await $.post("./dbconnect.php/insertTime", {
				endLunch: shift.endLunch,
			});
		} break;

		case COLUMN.END_SHIFT: {
			shift.end = time;
			return await $.post("./dbconnect.php/insertTime", {
				end: shift.end,
			});
		} break;
	
	}

	return;
}

function refreshTable(column) {
	switch (column) {
		case COLUMN.BEGIN_SHIFT: {

			if (formerColumn !== COLUMN.END_SHIFT) {
				alert("Encerre o turno antes de registrar entrada novamente!");
				return null;
			}

			const table = $("#workshift-table");
			row = table[0].insertRow(-1);
			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.BEGIN_SHIFT;

			return 1;
			
		} break;

		case COLUMN.BEGIN_LUNCH: {

			if (formerColumn !== COLUMN.BEGIN_SHIFT) {
				alert("Inicie o turno antes de registrar saída para almoço!");
				return null;
			}

			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.BEGIN_LUNCH;

			return 1;
								
		} break;

		case COLUMN.END_LUNCH: {

			if (formerColumn !== COLUMN.BEGIN_LUNCH) {
				alert("Inicie o almoço antes de encerrá-lo!");
				return null;
			}

			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.END_LUNCH;
			
			return 1;

		} break;

		case COLUMN.END_SHIFT: {

			if (formerColumn !== COLUMN.END_LUNCH) {
				alert("Encerre o almoço antes de encerrar o turno!");
				return null;
			}

			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.END_SHIFT;

			return 1;

		} break;
	}
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
	const data = await $.get('./dbconnect.php?clock=true');
	const jsonTimes = JSON.parse(data.split("=")[0]);
	
	if (jsonTimes.START_TIME !== '') {
		time = jsonTimes.START_TIME;
		refreshTable(COLUMN.BEGIN_SHIFT);
	}

	if (jsonTimes.LUNCH_TIME !== '') {
		time = jsonTimes.LUNCH_TIME;
		refreshTable(COLUMN.BEGIN_LUNCH);
	}

	if (jsonTimes.END_LUNCH_TIME !== '') {
		time = jsonTimes.LUNCH_TIME;
		refreshTable(COLUMN.END_LUNCH);
	}

	if (jsonTimes.FINISH_TIME !== '') {
		time = jsonTimes.END_SHIFT;
		refreshTable(COLUMN.END_SHIFT);
	}
}

async function start() {
	await loadDataFromBank();
}

start();