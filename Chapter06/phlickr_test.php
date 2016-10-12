<?php

include("flickr_key.php");
require_once 'Phlickr/Api.php';

$api = new Phlickr_Api(API_KEY, API_SECRET);
$response = $api->ExecuteMethod(
   'flickr.test.echo',
   array('message' => 'It worked!')
   );

print "<hi>{$response->xml->message}</h1>";
?>
