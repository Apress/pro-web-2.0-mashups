<?php
// http://api.eventful.com/libs/Services_EVDB

ini_set(
  'include_path',
    ini_get( 'include_path' ) . PATH_SEPARATOR . "/home/rdhyee/pear/lib/php" . PATH_SEPARATOR . '/usr/local/lib/php'
    );

require 'Services/EVDB.php';

// Enter your application key here. (See http://api.evdb.com/keys/)
$app_key = '[APP_KEY}';

$evdb = &new Services_EVDB($app_key);

// Authentication is required for some API methods.
$user     = $_REQUEST['user'];
$password = $_REQUEST['password'];

if ($user and $password)
{
  $l = $evdb->login($user, $password);

  if ( PEAR::isError($l) )
  {
      print("Can't log in: " . $l->getMessage() . "\n");
  }
}

// All method calls other than login() go through call().
$args = array(
  'id' => $_REQUEST['id'],
);
$event = $evdb->call('events/get', $args);

if ( PEAR::isError($event) )
{
    print("An error occurred: " . $event->getMessage() . "\n");
    print_r( $evdb );
}

// The return value from a call is an XML_Unserializer data structure.
print_r( $event );
?>
