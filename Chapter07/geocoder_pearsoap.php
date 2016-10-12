<?php
# example using PEAR::SOAP + Geocoder SOAP search

ini_set(
  'include_path',
    ini_get( 'include_path' ) . PATH_SEPARATOR . "/home/rdhyee/pear/lib/php" . PATH_SEPARATOR . "/home/rdhyee/phplib"
    );

require 'SOAP/Client.php';

# let's look up Apress
$address = '2855 Telegraph Avenue, Berkeley, CA 94705'; // your Google search terms

$wsdl_url = "http://geocoder.us/dist/eg/clients/GeoCoderPHP.wsdl";

# true to indicate that it is a WSDL url.
$soap = new SOAP_Client($wsdl_url,true);

$params = array(
    'location'=>$address
  );

$results = $soap->call('geocode', $params);

# include some fault handling code
if(PEAR::isError($results)) {
    $fault = $results->getFault();
    print "Error number " . $fault->faultcode . " occurred\n";
    print "     " . $fault->faultstring . "\n";
} else {
    print "The latitude and longitude for address is: {$results[0]->lat},{$results[0]->long}";
}
?>
