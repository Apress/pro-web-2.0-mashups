<?php

require_once 'iCalcreator/iCalcreator.class.php';

  $filename = 'D:\Document\Docs\2007\05\iCal-20070508-082112.ics';

  $v = new vcalendar(); // initiate new CALENDAR
  $v->parse($filename);

  # get first vevent
  $comp = $v->getComponent("VEVENT");
  
  #print_r($comp);
  $summary_array = $comp->getProperty("summary", 1, TRUE);
  echo "summary: ", $summary_array["value"], "\n";

  $dtstart_array = $comp->getProperty("dtstart", 1, TRUE);
  $dtstart = $dtstart_array["value"];
  $startDate = "{$dtstart["year"]}-{$dtstart["month"]}-{$dtstart["day"]}";
  $startTime = "{$dtstart["hour"]}:{$dtstart["min"]}:{$dtstart["sec"]}";
  
  $dtend_array = $comp->getProperty("dtend", 1, TRUE);
  $dtend = $dtend_array["value"];
  $endDate = "{$dtend["year"]}-{$dtend["month"]}-{$dtend["day"]}";
  $endTime = "{$dtend["hour"]}:{$dtend["min"]}:{$dtend["sec"]}";
  
  echo "start: ",  $startDate,"T",$startTime, "\n";
  echo "end: ",  $endDate,"T",$endTime, "\n";
  


?>
