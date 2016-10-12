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

function printCalendarList($client)
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $calFeed = $gdataCal->getCalendarListFeed();
  echo $calFeed->title->text . "\n";
  echo "\n";
  foreach ($calFeed as $calendar) {
    echo $calendar->title->text, "\n";
  }
}

$USER = "raymond.yee";
$PASSWORD = "john316_";

$client = getGDataClient($USER, $PASSWORD);
printCalendarList($client);

?>