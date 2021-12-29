<?php

	$hostname = "localhost";
	$dbname = "clock";
	$user = "root";
	$password = "";

	$today = "";

	setlocale(LC_ALL, array('pt_BR', 'pt_br.UTF-8'));
  date_default_timezone_set("America/Sao_Paulo");

	// Connect to MySQL
	try {
		$conn = new PDO("mysql:host=$hostname;", $user, $password);
	} catch (Exception $e) {
		print "ERROR PDO_NEW_CONN:" . $e->getMessage() . "<br/>";
	}

	// Create database
	try {
		$conn->exec("CREATE DATABASE IF NOT EXISTS $dbname;
			CREATE USER IF NOT EXISTS `$user`@`localhost` IDENTIFIED BY '$password';
			GRANT ALL PRIVILEGES ON `$dbname`.* TO  `$user`@`localhost` IDENTIFIED BY '$password';
			FLUSH PRIVILEGES;");
	} catch (Exception $e) {
		print "ERROR CREATE_DB: " . $e->getMessage() . "<br/>";
	}

	// Use database
	try {
		$conn->exec("USE $dbname;");
	} catch (Exception $e) {
		print "ERROR USE_DB: " . $e->getMessage() . "<br/>";
	}

	// Create table
	try {	
		$conn->exec("CREATE TABLE IF NOT EXISTS shift_register(
			START_TIME VARCHAR(15) NOT NULL,
			LUNCH_TIME VARCHAR(15),
			END_LUNCH_TIME VARCHAR(15),
			FINISH_TIME VARCHAR(15) NOT NULL,
			SHIFT_DATE DATE NOT NULL
		);");
	} catch (Exception $e) {
		print "ERROR CREATE_TBL: " . $e->getMessage() . "<br/>";
	}

	$stmt;
	$today = date("Y-m-d");

	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_REQUEST["clock"])) {
			if ($_REQUEST["clock"] == "true") {
			
				// Select shift times from table
				try {
					$stmt = $conn->prepare("SELECT * FROM shift_register WHERE SHIFT_DATE = STR_TO_DATE(:today, '%Y-%m-%d')");

					$stmt->bindParam(":today", $today);
					$stmt->execute();
				} catch (Exception $e) {
					print "ERROR SELECT_shiftr: " . $e.getMessage() . "<br/>";
				}
	
				$results = "";
	
				foreach ($stmt as $key => $value) {
					$results .= "{";
					$results .= '"START_TIME": "' . $value["START_TIME"] . '",';
					$results .= '"LUNCH_TIME": "' . $value["LUNCH_TIME"] . '",';
					$results .= '"END_LUNCH_TIME": "' . $value["END_LUNCH_TIME"] . '",';
					$results .= '"FINISH_TIME": "' . $value["FINISH_TIME"] . '"';
					$results .= "}=";
				}
	
				echo print_r($results);
			}
		}
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($_SERVER["PATH_INFO"] == "/insertTime") {
			if (isset($_POST["begin"])) {

				$column = "START_TIME";
				// Insert start shift time on table
				try {
					$stmt = $conn->prepare("INSERT INTO shift_register($column, SHIFT_DATE)
						VALUES (:hour, STR_TO_DATE(:shift_time, '%Y-%m-%d'));");
					$stmt->bindParam(":hour", $_POST["begin"]);
					$stmt->bindParam(":shift_time", $today);
					$stmt->execute();
					return 1;

				} catch (Exception $e) {
					print $e;
					return 0;
				}
			}

			if (isset($_POST["startLunch"])) {
				$column = "LUNCH_TIME";

				// Insert lunch time on table
				try {
					$stmt = $conn->prepare("UPDATE shift_register
						SET $column = :hour
						WHERE SHIFT_DATE = :shift_time;");
					$stmt->bindParam(":hour", $_POST["startLunch"]);
					$stmt->bindParam(":shift_time", $today);

					$stmt->execute();
					return 1;

				} catch (Exception $e) {
					print $e;
					return 0;
				}
			}

			if (isset($_POST["endLunch"])) {
				$column = "END_LUNCH_TIME";

				// Insert end lunch time on table
				try {
					$stmt = $conn->prepare("UPDATE shift_register
						SET $column = :hour
						WHERE SHIFT_DATE = :shift_time;");
					$stmt->bindParam(":hour", $_POST["endLunch"]);
					$stmt->bindParam(":shift_time", $today);

					$stmt->execute();
					return 1;

				} catch (Exception $e) {
					print $e;
					return 0;
				}
			}

			if (isset($_POST["end"])) {
				$column = "FINISH_TIME";

				// Insert end shift time on table
				try {
					$stmt = $conn->prepare("UPDATE shift_register
						SET $column = :hour
						WHERE SHIFT_DATE = :shift_time;");
					$stmt->bindParam(":hour", $_POST["end"]);
					$stmt->bindParam(":shift_time", $today);

					$stmt->execute();
					return 1;

				} catch (Exception $e) {
					print $e;
					return 0;
				}
			}
		}
	}

?>