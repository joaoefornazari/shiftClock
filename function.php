<?php

  $beginShiftTime = 0;
  $beginLunchTime = 0;
  $endLunchTime = 0;
  $endShiftTime = 0;

  $listOfTimes[] = "";

  setlocale(LC_ALL, array('pt_BR', 'pt_br.UTF-8'));
  date_default_timezone_set("America/Campo_Grande");

  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_REQUEST["data"] == 1) {
      $beginShiftTime = getTime();
      array_push($listOfTimes, $beginShiftTime);
      echo $beginShiftTime;
    }
    if ($_REQUEST["data"] == 2) {
      $beginLunchTime = getTime();
      array_push($listOfTimes, $beginLunchTime);
      echo $beginLunchTime;
    }
    if ($_REQUEST["data"] == 3) {
      $endLunchTime = getTime();
      array_push($listOfTimes, $endLunchTime);
      echo $endLunchTime;
    }
    if ($_REQUEST["data"] == 4) {
      $endShiftTime = getTime();
      array_push($listOfTimes, $endShiftTime);
      echo $endShiftTime;
    }
  }

  function getTime() {
    return strftime("%H:%M:%S");
  } 
?>