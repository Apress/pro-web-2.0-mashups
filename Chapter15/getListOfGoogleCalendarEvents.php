<?php

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

function getGDataClient($user, $pass)
{
  $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;

  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

function printEventsForCalendar($client, $userID)
{
  $gdataCal = new Zend_Gdata_Calendar($client);

  $query = $gdataCal->newEventQuery();
  $query->setUser($userID);
  $query->setVisibility('private');
  $query->setProjection('full');

  $eventFeed = $gdataCal->getCalendarEventFeed($query);

  echo $eventFeed->title->text . "\n";
  echo "\n";
  foreach ($eventFeed as $event) {
    echo $event->title->text, "\t", $event->id->text, "\n" ;
    echo $event->content->text, "\n";
    foreach ($event->where as $where) {
      echo $where, "\n";
    }
    foreach ($event->when as $when) {
      echo "Starts: " . $when->startTime . "\n";
      echo "Ends: " . $when->endTime . "\n";
    }
    
    # check for recurring events
    if ($recurrence = $event->getRecurrence()) {
      echo "recurrence: ", $recurrence, "\n";
    } 
    
    print "\n";
  }
}

$USER = "raymond.yee";
$PASSWORD = "john316_";

# userID for the Mashup Guide Demo calendar
$userID = "9imfjk71chkcs66t1i436je0s0%40group.calendar.google.com";

$client = getGDataClient($USER, $PASSWORD);
printEventsForCalendar($client, $userID);

?> 