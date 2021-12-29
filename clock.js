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

			if (formerColumn === COLUMN.BEGIN_SHIFT) {
				alert("Este horário já foi registrado!");
				return null;
			}

			const table = $("#workshift-table");
			row = table[0].insertRow(-1);
			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.BEGIN_SHIFT;

			$('#beginShift').attr('disabled', true);

			return 1;
			
		} break;

		case COLUMN.BEGIN_LUNCH: {

			if (formerColumn !== COLUMN.BEGIN_SHIFT) {
				if (formerColumn === COLUMN.BEGIN_LUNCH) {
					alert("Este horário já foi registrado!");
					return null;
				} else {
					alert("Registre o início do almoço para registrar o final!");
					return null;
				}	
			}

			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.BEGIN_LUNCH;

			$('#goToLunch').attr('disabled', true);

			return 1;
								
		} break;

		case COLUMN.END_LUNCH: {

			if (formerColumn !== COLUMN.BEGIN_LUNCH) {
				if (formerColumn === COLUMN.END_LUNCH) {
					alert("Este horário já foi registrado!");
					return null;
				} else {
					alert("Registre o início do almoço para registrar o final!");
					return null;
				}				
			}			

			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.END_LUNCH;

			$('#backFromLunch').attr('disabled', true);
			
			return 1;

		} break;

		case COLUMN.END_SHIFT: {

			if (formerColumn !== COLUMN.END_LUNCH) {
				if (formerColumn === COLUMN.END_SHIFT) {
					alert("Este horário já foi registrado!");
					return null;

				} else {
					alert("Encerre o almoço antes de encerrar o turno!");
					return null;
				}
			}
			const cell = row.insertCell(column-1);
			
			cell.appendChild(document.createTextNode(time));
			formerColumn = COLUMN.END_SHIFT;

			$('#endShift').attr('disabled', true);

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

async function loadDataFromBank() {
	const data = await $.get('./dbconnect.php?clock=true');
	const jsonTimes = JSON.parse(data.split("=")[0]);

	console.log(jsonTimes);
	
	if (jsonTimes.START_TIME !== '' && typeof jsonTimes.FINISH_TIME !== 'undefined') {
		time = jsonTimes.START_TIME;
		refreshTable(COLUMN.BEGIN_SHIFT);
	}

	if (jsonTimes.LUNCH_TIME !== '' && typeof jsonTimes.FINISH_TIME !== 'undefined') {
		time = jsonTimes.LUNCH_TIME;
		refreshTable(COLUMN.BEGIN_LUNCH);
	}

	if (jsonTimes.END_LUNCH_TIME !== '' && typeof jsonTimes.FINISH_TIME !== 'undefined') {
		time = jsonTimes.LUNCH_TIME;
		refreshTable(COLUMN.END_LUNCH);
	}

	if (jsonTimes.FINISH_TIME !== '' && typeof jsonTimes.FINISH_TIME !== 'undefined') {
		time = jsonTimes.FINISH_TIME;
		refreshTable(COLUMN.END_SHIFT);
	}
}

async function start() {
	await loadDataFromBank();
}

start();